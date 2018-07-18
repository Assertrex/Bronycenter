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
            'Your account has been successfully created! ' .
            'Click on a verification link sent to your e-mail address to confirm your account. ' .
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
                            (new Flash($this->container['flash']))->error(
                                'E-mail address for this account has not been verified.<br />' .
                                'Click a verification link sent on <b>' . $emailKey->getEmail() . '</b> or ' .
                                '<b><a href="' . $this->container['router']->pathFor('authResend') . '">click here</a></b> to re-send it.'
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
}
