<?php

/**
* Class used for validating users input values
*
* @since Release 0.1.0
*/

namespace BronyCenter;

class Validator
{
    /**
     * Singleton instance of a current class
     *
     * @since Release 0.1.0
     */
    private static $instance = null;

    /**
     * Place for instance of a flash class
     *
     * @since Release 0.1.0
     */
    private $flash = null;

    /**
     * Get instances of required classes
     *
     * @since Release 0.1.0
     */
    public function __construct()
    {
        $this->flash = Flash::getInstance();
    }

    /**
     * Check if instance of current class is existing and create and/or return it
     *
     * @since Release 0.1.0
     * @var boolean Set as true to reset class instance
     * @return object Instance of a current class
     */
    public static function getInstance($reset = false) {
        if (!self::$instance || $reset === true) {
            self::$instance = new Validator();
        }

        return self::$instance;
    }

    /**
     * Check if display name is valid
     *
     * @since Release 0.1.0
     * @var string $displayname Display name of a user
     * @return boolean Result of a display name validation
     */
    public function checkDisplayname($displayname) {
        // Start from a valid value
        $isValid = true;

        // Check if display name contains between 3 and 32 characters
        if (strlen($displayname) < 3 || strlen($displayname) > 32) {
            $this->flash->error('Display name must be between 3 and 32 characters.');
            $isValid = false;
        }

        // TODO Cut starting and ending spaces

        return $isValid;
    }

    /**
     * Check if username is valid
     *
     * @since Release 0.1.0
     * @var string $username Username of a user
     * @return boolean Result of a username validation
     */
    public function checkUsername($username) {
        // Start from a valid value
        $isValid = true;

        // Check if username contains between 3 and 24 characters
        if (strlen($username) < 3 || strlen($username) > 24) {
            $this->flash->error('Username must be between 3 and 24 characters.');
            $isValid = false;
        }

        // Check if username is using only alphanumeric characters
        if (!ctype_alnum($username)) {
            $this->flash->error('Username must contain only alphanumeric characters (a-z0-9).');
            $isValid = false;
        }

        return $isValid;
    }

    /**
     * Check if e-mail address is valid
     *
     * @since Release 0.1.0
     * @var string $email E-mail address of a user
     * @return boolean Result of an e-mail address validation
     */
    public function checkEmail($email) {
        // Start from a valid value
        $isValid = true;

        // Check if e-mail address is valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash->error('E-mail address seems to be invalid. Try to use different one.');
            $isValid = false;
        }

        // Check if e-mail address contains allowed amount of characters
        if (strlen($email) < 5 || strlen($email) > 64) {
            $this->flash->error('E-mail address must be between 5 and 64 characters.');
            $isValid = false;
        }

        return $isValid;
    }

    /**
     * Check if both passwords are valid
     *
     * @since Release 0.1.0
     * @var string $password Password of a user
     * @var string $passwordRepeat Repeated password of a user
     * @return boolean Result of passwords validation
     */
    public function checkPasswords($password, $passwordRepeat) {
        // Start from a valid value
        $isValid = true;

        // Check if password is not too short
        if (strlen($password) < 6) {
            $this->flash->error('To increase security, your password must be at least 6 characters long.');
            $isValid = false;
        }

        // Check if passwords are the same
        if ($password != $passwordRepeat) {
            $this->flash->error('Repeated password must be the same as the first one.');
            $isValid = false;
        }

        return $isValid;
    }

    /**
     * Check if post content is valid
     *
     * @since Release 0.1.0
     * @var string $postContent Content of a post
     * @var string $postType Type of a post
     * @return boolean Result of a post content validation
     */
    public function checkPostContent($postContent, $postType) {
        // Start from a valid value
        $isValid = true;

        if ($postType == 1) {
            // Check if standard post is not too short
            if (strlen($postContent) < 3) {
                $this->flash->error('Post content needs to be at least 3 characters long.');
                $isValid = false;
            }

            // Check if standard post is not too long
            if (strlen($postContent) > 1000) {
                $this->flash->error('Post content can\'t contain more than 1000 characters.');
                $isValid = false;
            }
        }

        return $isValid;
    }
}
