<?php

/**
 * Class used for validating user inputs.
 *
 * @copyright 2017 BronyCenter
 * @author Assertrex <norbert.gotowczyc@gmail.com>
 * @since 0.1.0
 */
class Validate
{
    /**
     * Object of a system class.
     *
     * @since 0.1.0
     * @var null|object
     */
    private $system = null;

    /**
     * @since 0.1.0
     * @var object $o_system Object of a system class.
     */
    public function __construct($o_system)
    {
        // Store required class object in a property.
        $this->system = $o_system;
    }

    /**
     * Validate user's display name.
     *
     * @since 0.1.0
     * @var string $displayname User's account display name.
     * @return boolean Result of this method.
     */
    public function displayName($displayname)
    {
        // Start from a valid value.
        $isValid = true;

        // Check if display name is between 3 and 32 characters.
        if (strlen($displayname) < 3 || strlen($displayname) > 24) {
            $this->system->setMessage(
                'error',
                'Display name must be between 3 and 24 characters.'
            );

            $isValid = false;
        }

        // Check if display name is using only allowed characters.
		if (preg_match('/[^a-zA-Z0-9 _()]/', $displayname)) {
            $this->system->setMessage(
                'error',
                'Display name can contain only alphanumeric characters <b>a-zA-Z0-9</b>, <b>_()</b> and <b>spaces</b>.'
            );

            $isValid = false;
        }

        return $isValid;
    }

    /**
     * Validate user's username.
     *
     * @since 0.1.0
     * @var string $username User's account username.
     * @return boolean Result of this method.
     */
    public function username($username)
    {
        // Start from a valid value.
        $isValid = true;

        // Check if username is between 3 and 24 characters.
        if (strlen($username) < 3 || strlen($username) > 20) {
            $this->system->setMessage(
                'error',
                'Username must be between 3 and 20 characters.'
            );

            $isValid = false;
        }

        // Check if username is using only alphanumeric characters.
		if (!ctype_alnum($username)) {
            $this->system->setMessage(
                'error',
                'Username can contain only alphanumeric characters (a-zA-Z0-9).'
            );

            $isValid = false;
        }

        return $isValid;
    }

    /**
     * Validate user's e-mail address.
     *
     * @since 0.1.0
     * @var string $email User's account e-mail address.
     * @return boolean Result of this method.
     */
    public function email($email)
    {
        // Start from a valid value.
        $isValid = true;

        // Check if e-mail address contains allowed amount of characters.
		if (strlen($email) < 5 || strlen($email) > 64) {
            $this->system->setMessage(
                'error',
                'E-mail address needs to be between 5 and 64 characters.'
            );

            $isValid = false;
		}

        // Check if e-mail address is valid.
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->system->setMessage(
                'error',
                'E-mail address is invalid.'
            );

            $isValid = false;
        }

        return $isValid;
    }

    /**
     * Validate user's password.
     *
     * @since 0.1.0
     * @var string $password User's account password.
     * @return boolean Result of this method.
     */
    public function password($password)
    {
        // Start from a valid value.
        $isValid = true;

        // Check if password is not too short.
		if (strlen($password) < 6) {
            $this->system->setMessage(
                'error',
                'Password needs to be at least 6 characters long.'
            );

			$isValid = false;
		}

        return $isValid;
    }

    /**
     * Validate city name.
     *
     * @since 0.1.0
     * @var string $city User's city name.
     * @return boolean Result of this method.
     */
    public function city($city)
    {
        // Start from a valid value
        $isValid = true;

        // Check if city contains more than 32 characters
        if (strlen($city) >= 32) {
            $this->system->setMessage(
                'error',
                'City name can\'t contain more than 32 characters.'
            );

            $isValid = false;
        }

        return $isValid;
    }
}
