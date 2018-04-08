<?php

declare(strict_types=1);

namespace BronyCenter;

interface AccountInterface
{
    public static function getInstance(bool $reset);
    public function doLogin();
    public function doRegister();
    public function doVerifyEmail($accountID, $tokenEmail);
    public function changePassword(string $currentPassword, string $newPassword, string $newPasswordRepeat) : bool;
    public function changeDisplayname(string $value) : bool;
    public function changeBirthdate(int $day, int $month, int $year) : bool;
    public function changeAvatar(array $file) : bool;
    public function changeDetails(string $field, string $value) : bool;
}

class Account implements AccountInterface
{
    private static $instance = null;
    private $o_config = null;
    private $o_database = null;
    private $o_flash = null;
    private $o_session = null;
    private $o_utilities = null;
    private $o_validator = null;
    private $o_post = null;

    public function __construct()
    {
        $this->o_config = Config::getInstance();
        $this->o_database = Database::getInstance();
        $this->o_flash = Flash::getInstance();
        $this->o_session = Session::getInstance();
        $this->o_utilities = Utilities::getInstance();
        $this->o_validator = Validator::getInstance();
        $this->o_post = Post::getInstance();
    }

    public static function getInstance(bool $reset = false)
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
    public function doLogin()
    {
        // Get website's settings
        $websiteSettings = $this->o_config->getSettings('system');

        // Check if login form has been submitted correctly
        if (empty($_POST['submit']) || $_POST['submit'] !== 'login') {
            $this->o_flash->error('Login method has been called incorrectly. Please, refresh a page and try again.');
            return false;
        }

        // Stop executing if login is disabled in website's settings
        if (!$websiteSettings['enableLogin']) {
            $this->o_flash->error('Login has been temporary disabled. Sorry about that. Please, try again later.');
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
            if (!$this->o_validator->checkEmail($username)) {
                return false;
            }
        } else {
            if (!$this->o_validator->checkUsername($username)) {
                return false;
            }
        }

        // Get selected details about user from database
        $user = $this->o_database->read(
            'id, display_name, username, email, password, login_count, avatar, account_type, account_standing',
            'users',
            $isEmail ? 'WHERE email = ?' : 'WHERE username = ?',
            [$username]
        );

        // Check if any user has been found
		if (count($user) != 1) {
            // Hash a fake password to prevent timing attacks
            $this->o_utilities->doHashPassword(md5(rand()));

            $this->o_flash->error('Wrong username/e-mail or password.');
			return false;
		}

        // Check if password is correct
		if (!password_verify($password, $user[0]['password'])) {
            $this->o_flash->error('Wrong username/e-mail or password.');
			return false;
		}

        // Check if user is banned
        if ($user[0]['account_standing'] == 2) {
            $this->o_flash->error('Your account has been banned.');
            return false;
        }

        // Store common variables
		$currentLoginCount = $user[0]['login_count'] + 1;
		$currentIP = $this->o_utilities->getVisitorIP();
		$currentDatetime = $this->o_utilities->getDatetime();
		$currentAgent = substr($this->o_utilities->getVisitorAgent(), 0, 256);

        // Update user's login details
		$this->o_database->update(
			'login_ip, login_datetime, login_count, last_online',
			'users',
			'WHERE id = ?',
			[$currentIP, $currentDatetime, $currentLoginCount, $currentDatetime, $user[0]['id']]
		);

        // Log details about successful login
		$this->o_database->create(
			'user_id, ip, datetime, agent',
			'log_logins',
			'',
			[$user[0]['id'], $currentIP, $currentDatetime, $currentAgent]
		);

        // Create a user session
        $this->o_session->create([
            'id' => $user[0]['id'],
            'displayname' => $user[0]['display_name'],
            'username' => $user[0]['username'],
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
    public function doRegister()
    {
        // Get website's settings
        $websiteSettings = $this->o_config->getSettings('system');

        /**
         * Step 1: Registration validation
        **/

        // Check if registration form has been submitted correctly
        if (empty($_POST['submit']) || $_POST['submit'] !== 'register') {
            $this->o_flash->error('Register method has been called incorrectly. Please, refresh a page and try again.');
            return false;
        }

        // Stop executing if registration is disabled in website's settings
        if (!$websiteSettings['enableRegistration']) {
            $this->o_flash->error('Registration has been temporary disabled. Sorry about that. Please, try again later.');
            return false;
        }

        // Store POST values in a variables
        $displayname = $_POST['display_name'];
        $username = strtolower($_POST['username']);
        $email = strtolower($_POST['email']);
        $password = $_POST['password'];
        $passwordRepeat = $_POST['passwordrepeat'];

        // Validate new display name
        $isDisplaynameValid = $this->o_validator->checkDisplayname($displayname);
        $isUsernameValid = $this->o_validator->checkUsername($username);
        $isEmailValid = $this->o_validator->checkEmail($email);
        $arePasswordsValid = $this->o_validator->checkPasswords($password, $passwordRepeat);

        // Check if all input fields are valid
        if (!$isDisplaynameValid || !$isUsernameValid || !$isEmailValid || !$arePasswordsValid) {
            return false;
        }

        /**
         * Step 2: Checking uniqueness of user values
        **/

        // Get any of other users using same values in a database
        $duplicateValues = $this->o_database->read(
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
                    $this->o_flash->error('Different user is already using this display name!');
				}

                // Check if user using same username has been found
				if ($username === $duplicateAccount['username']) {
                    $this->o_flash->error('Different user is already using this username!');
				}

                // Check if user using same e-mail address has been found
				if ($email === $duplicateAccount['email']) {
                    $this->o_flash->error('Different user is already using this e-mail address!');
				}
			}

			return false;
		}

        /**
         * Step 3: Add a user to the database
        **/

        // Store common variables
        $currentIP = $this->o_utilities->getVisitorIP();
		$currentDatetime = $this->o_utilities->getDatetime();

        // Get user's country code and timezone
		$o_geoip = new GeoIP();
        $o_geoip->getDetails($currentIP);

        // Hash a password
        $password = $this->o_utilities->doHashPassword($password);

        // Insert new user into database if nothing has returned an error
        $accountID = $this->o_database->create(
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
            $this->o_flash->error('There was an unknown error while trying to create an account. Sorry about that. Please, try again later.');
            return false;
        }

        // Add user to additional tables
        $this->o_database->create('user_id', 'users_details', '', [$accountID]);
        $this->o_database->create('user_id', 'users_statistics', '', [$accountID]);

        // Show successful system flash message about created account
        $this->o_flash->success(
            'Account has been created successfully.
            Check your e-mail address and click on a link that we\'ve sent to you to activate your account.<br />
            If you\'re not getting any, then check your <b>Spam folder</b> or log in here (with username) and click a <b>re-send e-mail button</b>.'
        );

        /**
         * Step 4: Send an e-mail with verification link
        **/

        // Generate a token key used for e-mail verification
        $tokenEmail = $this->o_utilities->getRandomHash(16);

        // Insert a new row to allow e-mail address verification
        $this->o_database->create(
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
        $foundHash = $this->o_database->read(
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
        $currentIP = $this->o_utilities->getVisitorIP();
		$currentDatetime = $this->o_utilities->getDatetime();

        // Update user account with an e-mail address and new registration datetime
        $hasChanged = $this->o_database->update(
            'email, registration_datetime, account_type',
            'users',
            'WHERE id = ?',
            [$foundHash[0]['email'], $currentDatetime, 1, $accountID]
        );

        // Update key_email table row with usage info
        if (!empty($hasChanged)) {
            $this->o_database->update(
                'used_ip, used_datetime',
                'key_email',
                'WHERE id = ?',
                [$currentIP, $currentDatetime, $foundHash[0]['id']]
            );

            // Add a post about account creation
            $this->o_post->add(null, 10, $accountID);

            return true;
        }

        return false;
    }

    public function changePassword(string $currentPassword, string $newPassword, string $newPasswordRepeat) : bool
    {
        if (!$this->o_validator->checkPasswords($newPassword, $newPasswordRepeat)) {
            return false;
        }

        if (!$this->isCurrentPasswordValid($currentPassword)) {
            $this->o_flash->error('Old password is incorrect.');
            return false;
        }

        $hasChanged = $this->o_database->update(
            'password',
            'users',
            'WHERE id = ?',
            [$this->o_utilities->doHashPassword($newPassword), $_SESSION['account']['id']]
        );

        if ($hasChanged) {
            $this->o_flash->success('Your password has been successfully changed.');
            return true;
        }

        $this->o_flash->error('Your password has not been changed due to unknown error.');
        return false;
    }

    private function isCurrentPasswordValid(string $currentPassword) : bool
    {
        $userPassword = $this->o_database->read(
            'password',
            'users',
            'WHERE id = ?',
            [$_SESSION['account']['id']],
            false
        )['password'];

        if (!password_verify($currentPassword, $userPassword)) {
            return false;
        }

        return true;
    }

    public function changeDisplayname(string $value) : bool
    {
        if (!$this->o_validator->checkDisplayname($value)) {
            return false;
        }

        $isUsed = $this->o_database->read(
            'id',
            'users',
            'WHERE display_name = ?',
            [$value],
            false
        );

        if (!empty($isUsed)) {
            if ($_SESSION['account']['id'] == $isUsed['id']) {
                $this->o_flash->info('No changes have been made to your display name.');
                return false;
            }

            $this->o_flash->error('Different user is already using this display name.');
            return false;
        }

        $userDetails = $this->o_database->read(
            'display_name, displayname_changes, displaynames_recent',
            'users',
            'WHERE id = ?',
            [$_SESSION['account']['id']],
            false
        );

        if ($userDetails['displayname_changes'] >= 3) {
            $this->o_flash->error('You have used the limit of your 3 display name changes.');
            return false;
        }

        if (is_null($userDetails['displaynames_recent'])) {
            $userDetails['displaynames_recent'] = str_replace(',', '&#44;', $userDetails['display_name']);
        } else {
            $userDetails['displaynames_recent'] = $userDetails['displaynames_recent'] . ',' . str_replace(',', '&#44;', $userDetails['display_name']);
        }

        $hasChanged = $this->o_database->update(
            'display_name, displayname_changes, displaynames_recent',
            'users',
            'WHERE id = ?',
            [$value, $userDetails['displayname_changes'] + 1, $userDetails['displaynames_recent'], $_SESSION['account']['id']]
        );

        if (!$hasChanged) {
            $this->o_flash->error('Your display name have not been changed due to an unknown error.');
            return false;
        }

        $this->o_post->add(null, 11);
        $this->o_flash->success('You have successfully changed your display name.');
        $_SESSION['user']['displayname'] = $value;
        return true;
    }

    public function changeBirthdate(int $day, int $month, int $year) : bool
    {
        if (!checkdate($month, $day, $year) || $year < 1900) {
            $this->o_flash->error('Your birthdate seems to be invalid!');
            return false;
        }

        $birthdate = $year . '-' . $month . '-' . $day;

        $hasChanged = $this->o_database->update(
            'birthdate',
            'users_details',
            'WHERE id = ?',
            [$birthdate, $_SESSION['account']['id']]
        );

        if (!$hasChanged) {
            $this->o_flash->error('Your birthdate have not been changed due to an unknown error.');
            return false;
        }

        $this->o_flash->success('You have successfully changed your birthdate.');
        return true;
    }

    public function changeAvatar(array $file) : bool
    {
        if (!empty($file['error'])) {
            return false;
        }

        $uniqueHash = false;

        while ($uniqueHash === false) {
            $hashAvatar = $this->o_utilities->getRandomHash(16);

            if (!is_dir(__DIR__ . '/../../public/media/avatars/' . $hashAvatar)) {
                $uniqueHash = true;
            }
        }

        $o_image = Image::getInstance();

        if (!$o_image->createAvatar($file, $hashAvatar)) {
            return false;
        }

        $hashPreviousAvatar = $this->o_database->read(
            'avatar',
            'users',
            'WHERE id = ?',
            [$_SESSION['account']['id']],
            false
        )['avatar'];

        if (!is_null($hashPreviousAvatar) && is_dir(__DIR__ . '/../../public/media/avatars/' . $hashAvatar)) {
            unlink(__DIR__ . '/../../public/media/avatars/' . $hashPreviousAvatar . '/minres.jpg');
            unlink(__DIR__ . '/../../public/media/avatars/' . $hashPreviousAvatar . '/defres.jpg');
            unlink(__DIR__ . '/../../public/media/avatars/' . $hashPreviousAvatar . '/maxres.jpg');
            rmdir(__DIR__ . '/../../public/media/avatars/' . $hashPreviousAvatar);
        }

        $updatedAvatar = $this->o_database->update(
            'avatar',
            'users',
            'WHERE id = ?',
            [$hashAvatar, $_SESSION['account']['id']]
        );

        $_SESSION['user']['avatar'] = $hashAvatar;
        return true;
    }

    public function changeDetails(string $field, string $value) : bool
    {
        $availableFields = ['city', 'short_description', 'full_description', 'contact_methods', 'favourite_music', 'favourite_movies', 'favourite_games', 'fandom_becameabrony', 'fandom_favouritepony', 'fandom_favouriteepisode', 'creations_links'];
        if (!in_array($field, $availableFields)) {
            $this->o_flash->error('Field does not exist or is not available for editing.');
            return false;
        }

        switch ($field) {
            case 'city':
                $value = substr($value, 0, 32);
                break;
            case 'short_description':
                $value = substr($value, 0, 160);
                break;
            case 'fandom_becameabrony':
            case 'fandom_favouritepony':
            case 'fandom_favouriteepisode':
                $value = substr($value, 0, 300);
                break;
            case 'contact_methods':
            case 'favourite_music':
            case 'favourite_movies':
            case 'favourite_games':
                $value = substr($value, 0, 500);
                break;
            case 'full_description':
            case 'creations_links':
                $value = substr($value, 0, 1000);
                break;
        }

        $hasChanged = $this->o_database->update(
            $field,
            'users_details',
            'WHERE id = ?',
            [$value ?? null, $_SESSION['account']['id']]
        );

        if (!$hasChanged) {
            $this->o_flash->error('Your settings have not been changed due to an unknown error.');
            return false;

        }

        $this->o_flash->success('You have successfully changed your details.');
        return true;
    }
}
