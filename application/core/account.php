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
     * Place for instance of a config class
     *
     * @since Release 0.1.0
     */
    private $config = null;

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
     * Place for instance of a post class
     *
     * @since Release 0.1.0
     */
    private $post = null;

    /**
     * Get instances of required classes
     *
     * @since Release 0.1.0
     */
    public function __construct()
    {
        $this->config = Config::getInstance();
        $this->database = Database::getInstance();
        $this->flash = Flash::getInstance();
        $this->session = Session::getInstance();
        $this->utilities = Utilities::getInstance();
        $this->validator = Validator::getInstance();
        $this->post = Post::getInstance();
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
     * @return boolean Result of a login attempt
     */
    public function login()
    {
        // Get website's settings
        $websiteSettings = $this->config->getSection('system');

        // Check if login form has been submitted correctly
        if (empty($_POST['submit']) || $_POST['submit'] !== 'login') {
            $this->flash->error('Login method has been called incorrectly. Please, refresh a page and try again.');
            return false;
        }

        // Stop executing if login is disabled in website's settings
        if (!$websiteSettings['enableLogin']) {
            $this->flash->error('Login has been temporary disabled. Sorry about that. Please, try again later.');
            return false;
        }

        // Store user input values
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Check if username is an e-mail address
        $isEmail = false;
        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $isEmail = true;
        }

        // Check if username or e-mail address is valid
        if ($isEmail) {
            if (!$this->validator->checkEmail($username)) {
                return false;
            }
        } else {
            if (!$this->validator->checkUsername($username)) {
                return false;
            }
        }

        // Get selected details about user from database
        $user = $this->database->read(
            'id, display_name, username, email, password, login_count, avatar, account_type, account_standing',
            'users',
            $isEmail ? 'WHERE email = ?' : 'WHERE username = ?',
            [$username]
        );

        // Check if any user has been found
		if (count($user) != 1) {
            // Hash a fake password to prevent timing attacks
            $this->utilities->doHashPassword(md5(rand()));

            $this->flash->error('Wrong username/e-mail or password.');
			return false;
		}

        // Check if password is correct
		if (!password_verify($password, $user[0]['password'])) {
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

        // Log details about successful login
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
     * @return boolean Result of a register attempt
     */
    public function register()
    {
        // Get website's settings
        $websiteSettings = $this->config->getSection('system');

        /**
         * Step 1: Registration validation
        **/

        // Check if registration form has been submitted correctly
        if (empty($_POST['submit']) || $_POST['submit'] !== 'register') {
            $this->flash->error('Register method has been called incorrectly. Please, refresh a page and try again.');
            return false;
        }

        // Stop executing if registration is disabled in website's settings
        if (!$websiteSettings['enableRegistration']) {
            $this->flash->error('Registration has been temporary disabled. Sorry about that. Please, try again later.');
            return false;
        }

        // Store POST values in a variables
        $displayname = $_POST['display_name'];
        $username = strtolower($_POST['username']);
        $email = strtolower($_POST['email']);
        $password = $_POST['password'];
        $passwordRepeat = $_POST['passwordrepeat'];

        // Validate new display name
        $isDisplaynameValid = $this->validator->checkDisplayname($displayname);
        $isUsernameValid = $this->validator->checkUsername($username);
        $isEmailValid = $this->validator->checkEmail($email);
        $arePasswordsValid = $this->validator->checkPasswords($password, $passwordRepeat);

        // Check if all input fields are valid
        if (!$isDisplaynameValid || !$isUsernameValid || !$isEmailValid || !$arePasswordsValid) {
            return false;
        }

        /**
         * Step 2: Checking uniqueness of user values
        **/

        // Get any of other users using same values in a database
        $duplicateValues = $this->database->read(
            'id, display_name, username, email',
            'users',
            'WHERE display_name = ? OR username = ? OR email = ?',
            [$displayname, $username, $email]
        );

        // Check if any of other users is already using same values
		if (count($duplicateValues) != 0) {
			foreach ($duplicateValues as $duplicateAccount) {
                // Check if user using same display name has been found
				if ($displayname === $duplicateAccount['display_name']) {
                    $this->flash->error('Different user is already using this display name!');
				}

                // Check if user using same username has been found
				if ($username === $duplicateAccount['username']) {
                    $this->flash->error('Different user is already using this username!');
				}

                // Check if user using same e-mail address has been found
				if ($email === $duplicateAccount['email']) {
                    $this->flash->error('Different user is already using this e-mail address!');
				}
			}

			return false;
		}

        /**
         * Step 3: Add a user to the database
        **/

        // Store common variables
        $currentIP = $this->utilities->getVisitorIP();
		$currentDatetime = $this->utilities->getDatetime();

        // Get user's country code and timezone
		$o_geoip = new GeoIP();
        $o_geoip->getDetails($currentIP);

        // Hash a password
        $password = $this->utilities->doHashPassword($password);

        // Insert new user into database if nothing has returned an error
        $accountID = $this->database->create(
            'display_name, username, email, password, registration_ip, registration_datetime, country_code, timezone',
            'users',
            '',
            [
                $displayname,
                $username,
                null,
                $password,
                $currentIP,
                $currentDatetime,
                $o_geoip->getCountryCode(),
                $o_geoip->getTimezone()
            ]
        );

        // Check if account has been created successfully
        if (empty($accountID)) {
            $this->flash->error('There was an unknown error while trying to create an account. Sorry about that. Please, try again later.');
            return false;
        }

        // Add user to additional tables
        $this->database->create('user_id', 'users_details', '', [$accountID]);
        $this->database->create('user_id', 'users_statistics', '', [$accountID]);

        // Show successful system flash message about created account
        $this->flash->success(
            'Account has been created successfully.
            Check your e-mail address and click on a link that we\'ve sent to you to activate your account.<br />
            If you\'re not getting any, then check your <b>Spam folder</b> or log in here (with username) and click a <b>re-send e-mail button</b>.'
        );

        /**
         * Step 4: Send an e-mail with verification link
        **/

        // Generate a token key used for e-mail verification
        $tokenEmail = $this->utilities->getRandomHash(16);

        // Insert a new row to allow e-mail address verification
        $this->database->create(
            'user_id, hash, email, date',
            'key_email',
            '',
            [$accountID, $tokenEmail, $email, $currentDatetime]
        );

        // Send an e-mail with verification link
        $o_mail = new Mail();
        $o_mail->sendRegistration($email, $accountID, $displayname, $tokenEmail);

        return true;
    }

    /**
     * Verify an e-mail address
     *
     * @since Release 0.1.0
     * @var string $accountID ID of a selected account
     * @var string $tokenEmail Token sent with an e-mail used for verification
     * @return boolean Result of a method
     */
    public function doVerifyEmail($accountID, $tokenEmail)
    {
        // Find selected account ID and e-mail token pair in database
        $foundHash = $this->database->read(
            'id, email',
            'key_email',
            'WHERE user_id = ? AND hash = ? AND used_datetime IS NULL',
            [$accountID, $tokenEmail]
        );

        // Check if hash has been found
        if (count($foundHash) != 1) {
            return false;
        }

        // Store common variables
        $currentIP = $this->utilities->getVisitorIP();
		$currentDatetime = $this->utilities->getDatetime();

        // Update user account with an e-mail address and new registration datetime
        $hasChanged = $this->database->update(
            'email, registration_datetime, account_type',
            'users',
            'WHERE id = ?',
            [$foundHash[0]['email'], $currentDatetime, 1, $accountID]
        );

        // Update key_email table row with usage info
        if (!empty($hasChanged)) {
            $this->database->update(
                'used_ip, used_datetime',
                'key_email',
                'WHERE id = ?',
                [$currentIP, $currentDatetime, $foundHash[0]['id']]
            );

            // Add a post about account creation
            $this->post->add(null, 10, $accountID);

            return true;
        }

        return false;
    }

    /**
     * Change user's password
     *
     * @since Release 0.1.0
     * @var string $currentPassword User's old password
     * @var string $newPassword User's new password
     * @var string $newPasswordRepeat User's new repeated password
     * @return boolean Result of a method
     */
    public function changePassword($currentPassword, $newPassword, $newPasswordRepeat)
    {
        // Check if new password is valid
        if (!$this->validator->checkPasswords($newPassword, $newPasswordRepeat)) {
            return false;
        }

        // Get user's old password from database
        $userPassword = $this->database->read(
            'password',
            'users',
            'WHERE id = ?',
            [$_SESSION['account']['id']],
            false
        );

        // Check if old password is correct
        if (!password_verify($currentPassword, $userPassword['password'])) {
            $this->flash->error('Old password is incorrect.');
            return false;
        }

        // Make a hash from new password
        $newPassword = $this->utilities->doHashPassword($newPassword);

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
