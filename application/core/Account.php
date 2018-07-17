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
}
