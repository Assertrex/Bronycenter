<?php

namespace BronyCenter\Core;

use BronyCenter\Repository\EmailKeyRepository;
use BronyCenter\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;

class Account
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function createUser($request, array $postValues)
    {
        // Validate user input
        $validation = new Validation($this->container);
        $validation->checkDisplayName($postValues['display_name']);
        $validation->checkUsername($postValues['username']);
        $validation->checkEmail($postValues['email']);
        $validation->checkPasswords($postValues['password'], $postValues['password_repeat']);

        // Check if all fields are valid
        if (!$validation->isValid()) {
            $errors = $validation->getErrors();

            foreach ($errors as $errorType) {
                foreach ($errorType as $errorMessage) {
                    if (!empty($errorMessage)) {
                        (new Flash($this->container['flash']))->error($errorMessage);
                    }
                }
            }

            return false;
        }

        // Check if unique fields have not been used yet
        $displayNameUsed = $validation->checkIfDisplayNameIsUsed($postValues['display_name']);
        $usernameUsed = $validation->checkIfUsernameIsUsed($postValues['username']);
        $emailUsed = $validation->checkIfEmailIsUsed($postValues['email']);

        if ($displayNameUsed || $usernameUsed || $emailUsed) {
            return false;
        }

        // Add user to the database
        $userRepository = new UserRepository($this->container[EntityManager::class]);
        $user = $userRepository->createUser($postValues);

        // Check if user's account has been correctly created
        if (empty($user->getId())) {
            (new Flash($this->container['flash']))->error(
                'Account couldn\'t be created due to unknown error.'
            );

            return false;
        }

        // Create a key for e-mail confirmation
        $emailKeyRepository = new EmailKeyRepository($this->container[EntityManager::class]);
        $hash = null;

        do {
            $tempHash = substr(md5(uniqid(rand(), true)), 0, 16);

            if (!$emailKeyRepository->checkIfKeyExists($tempHash)) {
                $hash = $tempHash;
            }
        } while (is_null($hash));

        $key = $emailKeyRepository->createKey([
            'user_id' => $user->getId(),
            'hash' => $hash,
            'email' => $postValues['email']
        ]);

        // Check if user's account has been correctly created
        if (empty($key->getId())) {
            (new Flash($this->container['flash']))->error(
                'Unknown error occurred while trying to create verification link. You can try to create an account again.'
            );

            return false;
        }

        // Send an email with e-mail confirmation link
        (new Mail())->sendAsTemplate($key->getEmail(), 'verification', [
            'display_name' => $key->getUser()->getDisplayName(),
            'verification_link' => $request->getUri()->getScheme() . '://' . $request->getUri()->getHost() .
                $this->container['router']->pathFor('authVerify') . '?user_id=' . $key->getUser()->getId() . '&hash=' . $key->getHash()
        ]);

        // Return a success flash message
        (new Flash($this->container['flash']))->success(
            'Your account has been successfully created!<br />' .
            'Click on a verification link sent to your e-mail address to confirm your account.<br />' .
            'If you can\'t find it, check your spam folder.'
        );

        return true;
    }

    public function loginUser($request, array $postValues)
    {
        $userRepository = new UserRepository($this->container[EntityManager::class]);
        $user = $userRepository->findByUsername($postValues['username']);

        // Check if selected user have not been found
        if (is_null($user)) {
            // Check if selected e-mail address has been found as not verified
            if (filter_var($postValues['username'], FILTER_VALIDATE_EMAIL)) {
                $emailKeyRepository = new EmailKeyRepository($this->container[EntityManager::class]);
                $emailKeys = $emailKeyRepository->findByEmail($postValues['username']);

                // Check if any of last created unverified accounts matches selected password
                if (count($emailKeys) > 0) {
                    foreach ($emailKeys as $emailKey) {
                        $user = $this->container[EntityManager::class]->getRepository('BronyCenter\Model\User')->find($emailKey->getUser()->getId());
                        $emailKey->setUser($user);

                        // Allow user to re-send verification link
                        if (password_verify($postValues['password'], $emailKey->getUser()->getPassword())) {
                            $pathResend = $this->container['router']->pathFor('authResend');
                            $userID = $emailKey->getUser()->getId();
                            $userEmail = $emailKey->getEmail();
                            $emailHash = $emailKey->getHash();

                            (new Flash($this->container['flash']))->error(
                                'E-mail address for this account has not been verified.<br />' .
                                'Click a verification link sent on <b>' . $userEmail . '</b> or <b>' .
                                '<a href="' . $pathResend . '?user_id=' . $userID . '&hash=' . $emailHash . '">click here</a>' .
                                '</b> to re-send it.'
                            );

                            return false;
                        }
                    }
                }
            }

            // Protect against timing attacks by hashing a random string
            password_hash(rand(), PASSWORD_ARGON2I, [
                'memory_cost' => 8192,
                'time_cost' => 36,
                'threads' => 2
            ]);

            // Return an error about wrong username or password
            (new Flash($this->container['flash']))->error('Wrong username/e-mail address or password.');
            return false;
        }

        // Check if password for found user is correct
        if (!password_verify($postValues['password'], $user->getPassword())) {
            (new Flash($this->container['flash']))->error('Wrong username/e-mail address or password.');
            return false;
        }

        // TODO: Create a user session

        return true;
    }

    public function verifyVerificationCode($request, array $queryValues)
    {
        if (empty($queryValues['user_id']) || empty($queryValues['hash'])) {
            (new Flash($this->container['flash']))->error(
                'Verification link is corrupted!'
            );

            return false;
        }

        // Get selected email key
        $emailKey = $this->container[EntityManager::class]->getRepository('BronyCenter\Model\EmailKey')->findBy([
            'user' => $queryValues['user_id'],
            'hash' => $queryValues['hash']
        ]);

        // Check if hash is valid
        if (count($emailKey) == 0) {
            (new Flash($this->container['flash']))->error(
                'Verification link is invalid!'
            );

            return false;
        }

        // Check if email key has been used
        if (empty($emailKey[0]->getEmail()) && !empty($emailKey[0]->getUsedDatetime())) {
            (new Flash($this->container['flash']))->success(
                'Verification link has been already used!'
            );

            return false;
        }

        // Get selected user account
        $emailKey = $emailKey[0];
        $user = $this->container[EntityManager::class]->getRepository('BronyCenter\Model\User')->find($emailKey->getUser()->getId());
        $emailKey->setUser($user);

        // Check if e-mail address is free
        $validation = new Validation($this->container);

        if ($validation->checkIfEmailIsUsed($emailKey->getEmail())) {
            return false;
        }

        // Add e-mail address to user account
        $emailKeyRepository = new EmailKeyRepository($this->container[EntityManager::class]);
        $emailKeyRepository->confirmKey([
            'id' => $emailKey->getId(),
            'email' => $emailKey->getEmail(),
            'ip_address' => $request->getAttribute('ip_address')
        ]);

        (new Flash($this->container['flash']))->success(
            'Your account has been verified! You can now log in.'
        );

        return true;
    }

    public function resendVerificationCode($request, array $queryValues)
    {
        if (empty($queryValues['user_id']) || empty($queryValues['hash'])) {
            (new Flash($this->container['flash']))->error(
                'Verification link couldn\'t be re-sent, because current link is corrupted!'
            );

            return false;
        }

        // Get selected email key
        $emailKey = $this->container[EntityManager::class]->getRepository('BronyCenter\Model\EmailKey')->findBy([
            'user' => $queryValues['user_id'],
            'hash' => $queryValues['hash']
        ]);

        // Check if hash is valid
        if (count($emailKey) == 0) {
            (new Flash($this->container['flash']))->error(
                'Verification link couldn\'t be re-sent, because selected verification code is invalid!'
            );

            return false;
        }

        $emailKey = $emailKey[0];

        // Re-send verification link
        (new Mail())->sendAsTemplate($emailKey->getEmail(), 'verification', [
            'display_name' => $emailKey->getUser()->getDisplayName(),
            'verification_link' => $request->getUri()->getScheme() . '://' . $request->getUri()->getHost() .
                $this->container['router']->pathFor('authVerify') . '?user_id=' . $emailKey->getUser()->getId() . '&hash=' . $emailKey->getHash()
        ]);

        (new Flash($this->container['flash']))->success(
            'Verification link has been sent again on <b>' . $emailKey->getEmail() . '</b>.<br />' .
            'If you don\'t see it, check your spam folder or contact with adminsitrator.'
        );

        return true;
    }
}
