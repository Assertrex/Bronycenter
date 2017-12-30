<?php

/**
* Handles user login requests
*
* @since Release 0.1.0
*/

namespace BronyCenter;

class Account
{
    /**
     * Singleton instance of a current class
     *
     * @since Release 0.1.0
     */
    private static $instance = null;

    /**
     * Place for instance of a database class
     *
     * @since Release 0.1.0
     */
    private $database = null;

    /**
     * Place for instance of a flash class
     *
     * @since Release 0.1.0
     */
    private $flash = null;

    /**
     * Place for instance of a session class
     *
     * @since Release 0.1.0
     */
    private $session = null;

    /**
     * Place for instance of a utilities class
     *
     * @since Release 0.1.0
     */
    private $utilities = null;

    /**
     * Place for instance of a validator class
     *
     * @since Release 0.1.0
     */
    private $validator = null;

    /**
     * Get instances of required classes
     *
     * @since Release 0.1.0
     */
    public function __construct()
    {
        $this->database = Database::getInstance();
        $this->flash = Flash::getInstance();
        $this->session = Session::getInstance();
        $this->utilities = Utilities::getInstance();
        $this->validator = Validator::getInstance();
    }

    /**
     * Check if instance of current class is existing and create and/or return it
     *
     * @since Release 0.1.0
     * @var boolean $reset Set as true to reset class instance
     * @return object Instance of a current class
     */
    public static function getInstance($reset = false)
    {
        if (!self::$instance || $reset === true) {
            self::$instance = new Account();
        }

        return self::$instance;
    }

    /**
     * Check if user login credentials are valid and create a session
     *
     * @since Release 0.1.0
     * @var array $credentials User login credentials
     * @return boolean Result of a login attempt
     */
    public function login($credentials)
    {
        // Check if username is an e-mail address
        $isEmail = false;
        if (filter_var($credentials['username'], FILTER_VALIDATE_EMAIL)) {
            $isEmail = true;
        }

        // Set default status for username and password validators
        $isUsernameValid = false;
        $isPasswordValid = false;

        // Check if username or e-mail address is valid
        if ($isEmail) {
            $isUsernameValid = $this->validator->checkEmail($credentials['username']);
        } else {
            $isUsernameValid = $this->validator->checkUsername($credentials['username']);
        }

        // Check if password is valid
        $isPasswordValid = $this->validator->checkPassword($credentials['password']);

        // Check if both input fields are valid
        if (!$isUsernameValid || !$isPasswordValid) {
            return false;
        }

        // Get selected details about user from database
        $user = $this->database->read(
            'id, display_name, username, email, password, login_count, avatar, account_type, account_standing',
            'users',
            $isEmail ? 'WHERE email = ?' : 'WHERE username = ?',
            [$credentials['username']]
        );

        // Check if any user has been found
		if (count($user) != 1) {
            // Hash a fake password to prevent timing attacks
            password_hash(md5(rand()), PASSWORD_BCRYPT, ['cost' => 13]);

            $this->flash->error('Wrong username/e-mail or password.');
			return false;
		}

        // Check if password is correct
		if (!password_verify($credentials['password'], $user[0]['password'])) {
            $this->flash->error('Wrong username/e-mail or password.');
			return false;
		}

        // Check if user is banned
        if ($user[0]['account_standing'] == 2) {
            $this->flash->error('Your account has been banned.');
            return false;
        }

        // Store common variables
		$currentLoginCount = $user[0]['login_count'] + 1;
		$currentIP = $this->utilities->getVisitorIP();
		$currentDatetime = $this->utilities->getDatetime();
		$currentAgent = substr($this->utilities->getVisitorAgent(), 0, 256);

        // Update user's login details
		$this->database->update(
			'login_ip, login_datetime, login_count, last_online',
			'users',
			'WHERE id = ?',
			[$currentIP, $currentDatetime, $currentLoginCount, $currentDatetime, $user[0]['id']]
		);

        // // Log details about successful login
		$this->database->create(
			'user_id, ip, datetime, agent',
			'log_logins',
			'',
			[$user[0]['id'], $currentIP, $currentDatetime, $currentAgent]
		);

        // Create a user session
        $this->session->create([
            'id' => $user[0]['id'],
            'displayname' => $user[0]['display_name'],
            'username' => $user[0]['username'],
            'email' => $user[0]['email'],
            'avatar' => $user[0]['avatar'],
            'account_type' => $user[0]['account_type'],
            'account_standing' => $user[0]['account_standing'],
        ]);

        return true;
    }

    /**
     * Register a new account
     *
     * @since Release 0.1.0
     * @var array $credentials User register credentials
     * @return boolean Result of a register attempt
     */
    public function register($credentials)
    {

    }

    /**
     * Change user's password
     *
     * @since Release 0.1.0
     * @var string $oldPassword User's old password
     * @var string $newPassword User's new password
     * @return boolean Result of a method
     */
    public function changePassword($oldPassword, $newPassword)
    {
        // Check if new password is valid
        if (!$this->validator->checkPassword($newPassword)) {
            return false;
        }

        // Get user's old password from database
        $userPassword = $this->database->read(
            'password',
            'users',
            'WHERE id = ?',
            [$_SESSION['account']['id']],
            false
        )['password'];

        // Check if old password is correct
        if (!password_verify($oldPassword, $userPassword)) {
            $this->flash->error('Old password is incorrect.');
            return false;
        }

        // Make a hash from new password
        $newPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 13]);

        // Replace user's password with new one in database
        $hasChanged = $this->database->update(
            'password',
            'users',
            'WHERE id = ?',
            [$newPassword, $_SESSION['account']['id']]
        );

        // Check if password has been changed
        if ($hasChanged) {
            // TODO Maybe ask for destroying all active sessions
            $this->flash->success('Your password has been successfully changed.');
            return true;
        }

        $this->flash->error('Your password has not been changed due to unknown error.');
        return false;
    }
}
