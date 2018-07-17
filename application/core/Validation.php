<?php

namespace BronyCenter\Core;

use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Respect\Validation\Validator as Validator;
use Respect\Validation\Exceptions\NestedValidationException;

class Validation
{
    private $entityManager;
    private $flash;

    private $errors = [];

    public function __construct(ContainerInterface $container)
    {
        $this->entityManager = $container[EntityManager::class];
        $this->flash = $container['flash'];
    }

    public function checkDisplayName(string $display_name) : void
    {
        try {
            Validator::notEmpty()
                ->length(3, 32)
                ->setName('Display name')
                ->assert($display_name);
        } catch (NestedValidationException $e) {
            $this->errors[] = $e->findMessages([
                'notEmpty' => '{{name}} can\'t be empty!',
                'length' => '{{name}} must contain 3-32 characters!',
            ]);
        }
    }

    public function checkUsername(string $username) : void
    {
        try {
            Validator::notEmpty()
                ->alnum()
                ->noWhitespace()
                ->length(3, 24)
                ->setName('Username')
                ->assert($username);
        } catch (NestedValidationException $e) {
            $this->errors[] = $e->findMessages([
                'notEmpty' => '{{name}} can\'t be empty!',
                'alnum' => '{{name}} can contain only alphanumeric characters (a-zA-Z0-9)!',
                'noWhitespace' => '{{name}} can\'t contain spaces!',
                'length' => '{{name}} must contain 3-16 characters!',
            ]);
        }
    }

    public function checkEmail(string $email) : void
    {
        try {
            Validator::notEmpty()
                ->email()
                ->noWhitespace()
                ->length(5, 64)
                ->setName('E-mail address')
                ->assert($email);
        } catch (NestedValidationException $e) {
            $this->errors[] = $e->findMessages([
                'notEmpty' => '{{name}} can\'t be empty!',
                'email' => '{{name}} seems to be invalid, please try another one!',
                'noWhitespace' => '{{name}} can\'t contain spaces!',
                'length' => '{{name}} must contain 5-64 characters!',
            ]);
        }
    }

    public function checkPasswords(string $password, string $password_repeat) : void
    {
        try {
            Validator::notEmpty()
                ->length(6)
                ->identical($password_repeat)
                ->setName('Password')
                ->assert($password);
        } catch (NestedValidationException $e) {
            $this->errors[] = $e->findMessages([
                'notEmpty' => '{{name}} can\'t be empty!',
                'length' => '{{name}} must contain at least 6 characters!',
                'identical' => 'Passwords must be the same!',
            ]);
        }
    }

    public function checkIfDisplayNameIsUsed(string $display_name) : bool
    {
        $displayNameFound = $this->entityManager->getRepository('BronyCenter\Model\User')->count(['display_name' => $display_name]);

        if ($displayNameFound) {
            (new Flash($this->flash))->error(
                'Selected display name is already in use!'
            );
        }

        return boolval($displayNameFound);
    }

    public function checkIfUsernameIsUsed(string $username) : bool
    {
        $usernameFound = $this->entityManager->getRepository('BronyCenter\Model\User')->count(['username' => $username]);

        if ($usernameFound) {
            (new Flash($this->flash))->error(
                'Selected username is already in use!'
            );
        }

        return boolval($usernameFound);
    }

    public function checkIfEmailIsUsed(string $email) : bool
    {
        $emailFound = $this->entityManager->getRepository('BronyCenter\Model\User')->count(['email' => $email]);

        if ($emailFound) {
            (new Flash($this->flash))->error(
                'Selected e-mail address is already in use!'
            );
        }

        return boolval($emailFound);
    }

    public function isValid() : bool
    {
        return empty($this->errors);
    }

    public function getErrors() : array
    {
        return $this->errors;
    }
}
