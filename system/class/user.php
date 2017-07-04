<?php

/**
 * Class used for actions with users accounts
 *
 * @since 0.1.0
 */
class User
{
    /**
     * Object of system class
     *
     * @since 0.1.0
     * @var object
     */
    private $system = null;

    /**
     * Object of database class
     *
     * @since 0.1.0
     * @var object
     */
    private $database = null;

    /**
     * Object of validate class
     *
     * @since 0.1.0
     * @var object
     */
    private $validate = null;

    /**
     * @since 0.1.0
     * @var object $o_system Object of system class
     * @var object $o_database Object of database class
     * @var object $o_validate Object of validate class
     */
    public function __construct($o_system, $o_database, $o_validate)
    {
        $this->system = $o_system;
        $this->database = $o_database;
        $this->validate = $o_validate;
    }

    /**
     * Get details about user
     *
     * @since 0.1.0
     * @var int ID of requested user
     * @return array|bool Details about user or false on not existing
     */
    public function getDetails($id) {
        $user = $this->database->read(
			'u.id, u.display_name, u.username, u.email, u.registration_ip, u.registration_datetime, u.login_ip, u.login_datetime, u.login_count, u.last_online, u.country_code, u.timezone, u.avatar, u.account_type, u.account_standing, d.birthdate, d.gender, d.city, d.description',
			'users u',
			'INNER JOIN users_details d ON u.id = d.user_id WHERE u.id = ?',
			[$id]
		);

        if (count($user) != 1) {
            return false;
        }

        return $user[0];
    }

    /**
     * Count logged users (action/ajax in last 1 minute)
     *
     * @since 0.1.0
     * @return string Number of logged users
     */
    public function getOnlineUsersCount() {
        $onlineUsers = $this->database->read(
			'COUNT(*) AS users',
			'users',
			'WHERE last_online >= DATE_SUB(NOW(), INTERVAL 1 MINUTE)',
			[]
		);

        return $onlineUsers[0]['users'];
    }

    /**
     * Count created (but not removed) accounts
     *
     * @since 0.1.0
     * @return string Number of registered users
     */
    public function getUsersCount() {
        $existingUsers = $this->database->read(
			'COUNT(*) AS users',
			'users',
			'',
			[]
		);

        return $existingUsers[0]['users'];
    }

    /**
     * Change user's display name
     *
     * @since 0.1.0
     * @var int $id ID of user
     * @var int $displayname User's new display name
     * @return bool Action status
     */
     public function changeDisplayname($id, $displayname) {
         // Validate display name
         if (!$this->validate->displayname($displayname)) {
             return false;
         }

         // Check if display name is free
         $unique = $this->database->read(
             'id',
             'users',
             'WHERE display_name = ?',
             [$displayname]
         );

         // Check if any other users with same display name has been found
         if (count($unique) !== 0) {
             if ($unique[0]['id'] == $id) {
                 $this->system->setMessage('error', 'You\'re already using this display name!');
             } else {
                 $this->system->setMessage('error', 'Different user is already using this display name!');
             }

             return false;
         }

         // Change user's displayname in database
         $update = $this->database->update(
             'display_name',
             'users',
             'WHERE id = ?',
             [$displayname, $id]
         );

         // Check if row value has been changed
         if ($update != 1) {
             $this->system->setMessage('error', 'System couldn\'t change your display name, please try again!');
             return false;
         }

         // Publish post about display name change
         $o_post = new Post($this->system, $this->database, $this->validate);
         $o_post->create($id, NULL, 11);

         $this->system->setMessage('success', 'Your display name has been changed successfully!');
         return true;
     }

    /**
     * Change password of user
     *
     * @since 0.1.0
     * @var int $id ID of user
     * @var string $old Old password
     * @var string $new New password
     * @var string $repeat Repeated new password
     * @return bool Action status
     */
     public function changePassword($id, $old, $new, $repeat) {
         $user = $this->database->read(
             'password, email',
             'users',
             'WHERE id = ?',
             [$id]
         )[0];

         // Check if old password is correct
         if (!password_verify($old, $user['password'])) {
             $this->system->setMessage('error', 'Old password is incorrect!');
             return false;
         }

         // Check if new password is valid
         if (!$this->validate->password($new)) {
             return false;
         }

         // Check if new passwords are same
         if ($new !== $repeat) {
             $this->system->setMessage('error', 'New passwords are not the same!');
             return false;
         }

         // Hash a password
         $password = password_hash($new, PASSWORD_BCRYPT, ['cost' => 13]);

         // Change password in database
         $update = $this->database->update(
             'password',
             'users',
             'WHERE id = ?',
             [$password, $id]
         );

         // Check if row value has been changed
         if ($update != 1) {
             $this->system->setMessage('error', 'System could\'t change your password, please try again!');
             return false;
         }

         // TODO Send an e-mail with password change notification

         $this->system->setMessage('success', 'Your password has been changed successfully!');
         return true;
     }

     /**
      * Change user's birthdate
      *
      * @since 0.1.0
      * @var int $id ID of user
      * @var int $day User's new birthdate day
      * @var int $month User's new birthdate month
      * @var int $year User's new birthdate year
      * @return bool Action status
      */
      public function changeBirthdate($id, $day, $month, $year) {
          // Make sure that birthdate values are numbers
          $day = intval($day);
          $month = intval($month);
          $year = intval($year);

          // Make sure that birthdate is valid
          if (!checkdate($month, $day, $year)) {
              $this->system->setMessage('error', 'Your birthdate seems to be invalid!');
              return false;
          }

          // Combine birthdate into MySQL date format
          $birthdate = $year . '-' . $month . '-' . $day;

          // Change user's birthdate in database
          $update = $this->database->update(
              'birthdate',
              'users_details',
              'WHERE user_id = ?',
              [$birthdate, $id]
          );

          // Check if row value has been changed
          if ($update != 1) {
              $this->system->setMessage('error', 'System couldn\'t change your birthdate, please try again!');
              return false;
          }

          $this->system->setMessage('success', 'Your birthdate has been changed successfully!');
          return true;
      }

     /**
      * Change user's gender
      *
      * @since 0.1.0
      * @var int $id ID of user
      * @var int $city User's new gender value
      * @return bool Action status
      */
      public function changeGender($id, $gender) {
          // Make sure that gender value is a number
          $gender = intval($gender);

          // Set null if gender is empty
          if (empty($gender)) {
              $gender = NULL;
          }

          // Change user's gender in database
          $update = $this->database->update(
              'gender',
              'users_details',
              'WHERE user_id = ?',
              [$gender, $id]
          );

          // Check if row value has been changed
          if ($update != 1) {
              $this->system->setMessage('error', 'System couldn\'t change your gender, please try again!');
              return false;
          }

          $this->system->setMessage('success', 'Your gender has been changed successfully!');
          return true;
      }

     /**
      * Change user's city name
      *
      * @since 0.1.0
      * @var int $id ID of user
      * @var string $city User's new city name
      * @return bool Action status
      */
      public function changeCity($id, $city) {
          // Validate city name
          if (!$this->validate->city($city)) {
              return false;
          }

          // Escape HTML characters in new description
          $city = htmlspecialchars($city, ENT_QUOTES);

          // Set null if city name is empty
          if (strlen($city) === 0) {
              $city = NULL;
          }

          // Change user's city name in database
          $update = $this->database->update(
              'city',
              'users_details',
              'WHERE user_id = ?',
              [$city, $id]
          );

          // Check if row value has been changed
          if ($update != 1) {
              $this->system->setMessage('error', 'System couldn\'t change your city name, please try again!');
              return false;
          }

          $this->system->setMessage('success', 'Your city name has been changed successfully!');
          return true;
      }

     /**
      * Change user's profile page description
      *
      * @since 0.1.0
      * @var int $id ID of user
      * @var string $description New profile description
      * @return bool Action status
      */
      public function changeDescription($id, $description) {
          // Check if description contains more than 500 characters
          if (strlen($description) >= 500) {
              $this->system->setMessage('error', 'Profile description can\'t contain more than 500 characters');
              return false;
          }

          // Escape HTML characters in new description
          $description = htmlspecialchars($description, ENT_QUOTES);

          // Set null if description is empty
          if (strlen($description) === 0) {
              $description = NULL;
          }

          // Change user's description in database
          $update = $this->database->update(
              'description',
              'users_details',
              'WHERE user_id = ?',
              [$description, $id]
          );

          // Check if row value has been changed
          if ($update != 1) {
              $this->system->setMessage('error', 'System couldn\'t change your description, please try again!');
              return false;
          }

          $this->system->setMessage('success', 'Your profile description has been changed successfully!');
          return true;
      }

      /**
       * Change user's avatar and create 3 resolutions for it
       *
       * @since 0.1.0
       * @var int $id ID of user
       * @var string $file Path to temporiary image
       * @return bool Action status
       */
      public function changeAvatar($id, $file) {
          // Store default variables for checking if hash is unique
          $isHashUnique = false;
          $avatarHash = null;

          // Generate random hash for user's avatar
          while ($isHashUnique != true) {
              // Generate new hash for avatar
              $avatarHash = $this->system->getRandomHash(16);

              // Get same avatar's hash from database
              $duplicateHash = $this->database->read(
                  'id',
                  'users',
                  'WHERE avatar = ?',
                  [$avatarHash]
              );

              // Check if avatar's hash is unique
              if (count($duplicateHash) == 0) {
                  $isHashUnique = true;

                  // Create images with ImageMagick
                  $o_image = new Image();
                  if (!$o_image->createAvatar($file, $avatarHash)) {
                      $this->system->setMessage('error', 'System could\'t make an avatar! Please, try again!');
                      return false;
                  }

                  // Update avatar's hash in database
                  $insertedHash = $this->database->update(
                      'avatar',
                      'users',
                      'WHERE id = ?',
                      [$avatarHash, $id]
                  );

                  // Return error if hash couldn't be inserted
                  if (empty($insertedHash)) {
                      $this->system->setMessage('error', 'System couldn\'t change your avatar, please try again!');
                      return false;
                  }
              }
          }

          // Update user's avatar in session
          $_SESSION['user']['avatar'] = $avatarHash;

          $this->system->setMessage('success', 'Your avatar has been changed successfully!');
          return true;
      }

    /**
     * Try to authenticate user with username and password from $_POST.
     * Function validates values from inputs, checks account status and creates new session.
     * It throws a system error message if it couldn't log in.
     *
     * @since 0.1.0
     */
    public function login()
    {
        // Check if login form has been called correctly
        if (empty($_POST['submit']) || $_POST['submit'] !== 'login') {
            $o_system->setMessage('error', 'Login form has been called incorrectly.');
            return false;
        }

        // Store POST values in a variables
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Remember if username is an e-mail address
        $isEmail = false;

        // Check if username is an e-mail address
        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $isEmail = true;
        }

        // Remember if fields are valid
        $isUsernameValid = false;
        $isPasswordValid = false;

        // Validate username or e-mail address
        if ($isEmail) {
            $isUsernameValid = $this->validate->email($email);
        } else {
            $isUsernameValid = $this->validate->username($username);
        }

        // Validate password
        $isPasswordValid = $this->validate->password($password);

        // Check if both fields are valid
        if (!$isUsernameValid || !$isPasswordValid) {
            return false;
        }

        // Get selected informations about user from database
		$user = $this->database->read(
			'id, display_name, username, email, password, login_count, avatar, account_type, account_standing',
			'users',
			$isEmail ? 'WHERE email = ?' : 'WHERE username = ?',
			[$username]
		);

        // Check if any user has been found
		if (count($user) != 1) {
            $this->system->setMessage('error', 'Wrong username/e-mail or password.');
			return false;
		}

        // Check if password is correct
		if (!password_verify($password, $user[0]['password'])) {
			$this->system->setMessage('error', 'Wrong username/e-mail or password.');
			return false;
		}

        // Check if user is banned
        if ($user[0]['account_standing'] == 2) {
            $this->system->setMessage('error', 'Your account has been banned for some time.'); // TODO Display ban left time
			return false;
        }

        // Store common variables
		$currentLoginCount = $user[0]['login_count'] + 1;
		$currentIP = $this->system->getVisitorIP();
		$currentDatetime = $this->system->getDatetime();
		$currentAgent = substr($this->system->getVisitorAgent(), 0, 256);

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

        // Check if session can be created
        if (!$this->createSession($user[0], $currentIP, $currentDatetime)) {
            $this->system->setMessage('error', 'Couldn\'t create a session.');
			return false;
        }

        return true;
    }

    /**
     * Try to create new user with values from $_POST.
     *
     * @since 0.1.0
     */
    public function register()
    {
        // Check if register form has been called correctly
        if (empty($_POST['submit']) || $_POST['submit'] !== 'register') {
            $this->system->setMessage('error', 'Register form has been called incorrectly.');
            return false;
        }

        // Store POST values in a variables
        $displayname = $_POST['displayname'];
        $username = strtolower($_POST['username']);
        $email = strtolower($_POST['email']);
        $password = $_POST['password'];
        $passwordrepeat = $_POST['passwordrepeat'];

        // Validate display name
        $isDisplaynameValid = $this->validate->displayname($displayname);

        // Validate username
        $isUsernameValid = $this->validate->username($username);

        // Validate e-mail address
        $isEmailValid = $this->validate->email($email);

        // Validate password
        $isPasswordValid = $this->validate->password($password);

        // Check if passwords are the same
        $arePasswordsSame = ($password === $passwordrepeat) ? true : false;

        // Check if all fields are valid
        if (!$isDisplaynameValid || !$isUsernameValid || !$isPasswordValid || !$arePasswordsSame) {
            return false;
        }

        // Get any users using same display name, username or e-mail address
        $sameUsers = $this->database->read(
			'id, display_name, username, email',
			'users',
			'WHERE display_name = ? OR username = ? OR email = ?',
			[$displayname, $username, $email]
		);

        // Check if any user is already using display name, username or e-mail address
		if (count($sameUsers) != 0) {
			foreach ($sameUsers as $duplicateUser) {
				if ($displayname === $duplicateUser['display_name']) {
                    $this->system->setMessage('error', 'Display name is already in use.');
				}

				if ($username === $duplicateUser['username']) {
                    $this->system->setMessage('error', 'Username is already in use.');
				}

				if ($email === $duplicateUser['email']) {
                    $this->system->setMessage('error', 'E-mail address is already in use.');
				}
			}

			return false;
		}

        // Store common variables
        $currentIP = $this->system->getVisitorIP();
		$currentDatetime = $this->system->getDatetime();

        // Get user country code and timezone
		$o_geoip = new GeoIP($currentIP);

        // Insert new user into database if nothing has returned an error
        // TODO Check again if result has been inserted
        $this->database->create(
			'display_name, username, email, password, registration_ip, registration_datetime, country_code, timezone',
			'users',
			'',
			[
				$displayname,
				$username,
                null,
				password_hash($password, PASSWORD_BCRYPT, ['cost' => 13]),
				$currentIP,
				$currentDatetime,
				$o_geoip->getCountryCode(),
				$o_geoip->getTimezoneOffset(),
			]
		);

        // Display a system message if account has been created
        $this->system->setMessage('success', 'Account has been created successfully.');

        // Store default values for e-mail verification
		$isHashUnique = false;
		$uniqueHash = null;

        // Generate random hash for e-mail verification
		while ($isHashUnique != true) {
            // Generate random hash
			$uniqueHash = $this->system->getRandomHash(16);

            // Get same hash from database if existing
			$duplicateHash = $this->database->read(
				'hash',
				'key_email',
				'WHERE hash = ?',
				[$uniqueHash]
			);

            // Check if hash is unique, if true, insert it into database
			if (count($duplicateHash) == 0) {
                // Stop the while loop when unique hash has been found
				$isHashUnique = true;

                // Get created user ID
				$userID = $this->database->read(
					'id',
					'users',
					'WHERE username = ?',
					[$username]
				)[0];

                // Create new row for user details
                $this->database->create(
                    'user_id',
                    'users_details',
                    '',
                    [$userID['id']]
                );

                // Create new row for not verified e-mail address
				$this->database->create(
					'user_id, email, date, hash',
					'key_email',
					'',
					[$userID['id'], $email, $currentDatetime, $uniqueHash]
				);

				break;
			}
		}

        // Send a verification e-mail
		$o_mail = new Mail($this->system);
		$o_mail->sendRegistrationMail($email, $displayname, $uniqueHash, $userID['id']);

		return true;
    }

    /**
     * Create a simple session (not really secure yet)
     *
     * @since 0.1.0
     * @var array $user Details of user's that has logged in
     * @var string $ip IP of user that has just logged in
     * @var string $datetime Datetime of finished login function
     * @var string $avatar Hash of current avatar
     * @return boolean True is returned when session is created
     */
    private function createSession($user, $ip, $datetime)
    {
        // Destroy session first to make sure it's clear
        session_destroy();
        session_start();

        // Store details about user
		$_SESSION['user']['displayName'] = $user['display_name'];
        $_SESSION['user']['username'] = $user['username'];
        $_SESSION['user']['email'] = $user['email'];
        $_SESSION['user']['avatar'] = $user['avatar'];

        // Store details about account
        $_SESSION['account']['id'] = $user['id'];
        $_SESSION['account']['type'] = $user['account_type'];
        $_SESSION['account']['standing'] = $user['account_standing'];

        // Store details about login
        $_SESSION['login']['ip'] = $ip;
        $_SESSION['login']['datetime'] = $datetime;

        return true;
    }

    /**
     * Check if user is logged in and verify a session
     *
     * @since 0.1.0
     * @return boolean Result of session verification
     */
    public function verifySession()
    {
        // Check if user is logged in
        if (empty($_SESSION['account']['id'])) {
            return false;
        }

        // TODO Make something to allow IP switching (like wifi -> mobile)
        // Check if IP has not been changed (session stealing)
        if (empty($_SESSION['login']['ip']) || $_SESSION['login']['ip'] !== $this->system->getVisitorIP()) {
            $this->system->setMessage('error', 'Your IP address has changed. Please, log in again.');
            return false;
        }

        // Get selected informations about account from database
		$user = $this->database->read(
			'email, account_type, account_standing',
			'users',
			'WHERE id = ?',
			[$_SESSION['account']['id']]
		)[0];

        // Log out user if account has been banned
        if ($user['account_standing'] == 2) {
            $this->system->setMessage('error', 'Your account has been banned for some time.'); // TODO Display ban left time
            return false;
        }

        // Update session account details
        $_SESSION['user']['email'] = $user['email'];
        $_SESSION['account']['type'] = $user['account_type'];
        $_SESSION['account']['standing'] = $user['account_standing'];

        // Update last online datetime of user
        $this->database->update(
			'last_online',
			'users',
			'WHERE id = ?',
			[$this->system->getDatetime(), $_SESSION['account']['id']]
		);

        return true;
    }
}
