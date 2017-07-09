<?php

/**
 * Class used for actions with users accounts.
 *
 * @copyright 2017 BronyCenter
 * @author Assertrex <norbert.gotowczyc@gmail.com>
 * @since 0.1.0
 */
class User
{
    /**
     * Object of a system class.
     *
     * @since 0.1.0
     * @var null|object
     */
    private $system = null;

    /**
     * Object of a database class.
     *
     * @since 0.1.0
     * @var null|object
     */
    private $database = null;

    /**
     * Object of a session class.
     *
     * @since 0.1.0
     * @var null|object
     */
    private $session = null;

    /**
     * Object of a validate class.
     *
     * @since 0.1.0
     * @var null|object
     */
    private $validate = null;

    /**
     * @since 0.1.0
     * @var object $o_system Object of a system class.
     * @var object $o_database Object of a database class.
     * @var object $o_session Object of a session class.
     * @var object $o_validate Object of a validate class.
     */
    public function __construct($o_system, $o_database, $o_session, $o_validate)
    {
        // Store required classes objects in a properties.
        $this->system = $o_system;
        $this->database = $o_database;
        $this->session = $o_session;
        $this->validate = $o_validate;
    }

    /**
     * Check if selected user is online.
     *
     * @since 0.1.0
     * @var null|integer $id ID of the selected user.
     * @var null|string $datetime Last online datetime.
     * @return boolean Is user online.
     */
    public function isOnline($id = null, $datetime = null) {
        // Check if user has been selected.
        if (is_null($id)) {
            // Check if user has been seen in last 90 seconds.
            if ($this->system->countDateInterval($datetime) < 90) {
                return true;
            }

            // Return false if user has been inactive for more than 90 seconds.
            return false;
        }

        // TODO Allow checking for selected users (not needed yet).
        return false;
    }

    /**
     * Get details about selected user.
     *
     * @since 0.1.0
     * @var integer $id ID of the selected user.
     * @return boolean|array Details about user or false if user is not existing.
     */
    public function getDetails($id) {
        // Get details about user from database.
        $user = $this->database->read(
			'u.id, u.display_name, u.username, u.email, u.registration_ip, u.registration_datetime, u.login_ip, u.login_datetime, u.login_count, u.last_online, u.country_code, u.timezone, u.avatar, u.account_type, u.account_standing, d.birthdate, d.gender, d.city, d.description',
			'users u',
			'INNER JOIN users_details d ON u.id = d.user_id WHERE u.id = ?',
			[$id]
		);

        // Return false if user has not been found.
        if (count($user) != 1) {
            return false;
        }

        // Return details about selected user.
        return $user[0];
    }

    /**
     * Count logged users (action/ajax from last 90 seconds).
     *
     * @since 0.1.0
     * @return integer Amount of logged users.
     */
    public function getOnlineUsersCount() {
        // Get amount of logged user from database.
        $onlineUsers = $this->database->read(
			'COUNT(*) AS users',
			'users',
			'WHERE last_online >= DATE_SUB(NOW(), INTERVAL 90 SECOND)',
			[]
		);

        // Return amount of logged users.
        return intval($onlineUsers[0]['users']);
    }

    /**
     * Count created accounts.
     * Function doesn't count users that haven't verified or has deleted their accounts.
     *
     * @since 0.1.0
     * @return integer Amount of registered users.
     */
    public function getUsersCount() {
        $existingUsers = $this->database->read(
			'COUNT(*) AS users',
			'users',
			'WHERE account_type NOT IN (0, 9)',
			[]
		);

        // Return amount of registered users.
        return $existingUsers[0]['users'];
    }

    /**
     * Change user's profile display name.
     *
     * @since 0.1.0
     * @var integer $id ID of selected user.
     * @var integer $displayname User's new profile display name.
     * @return boolean Result of this method.
     */
     public function changeDisplayname($id, $displayname) {
         // Validate user's new display name.
         if (!$this->validate->displayName($displayname)) {
             return false;
         }

         // Get different user with same display name from database.
         $unique = $this->database->read(
             'id',
             'users',
             'WHERE display_name = ?',
             [$displayname]
         );

         // Check if user's new display name is available.
         if (count($unique) !== 0) {
             if ($unique[0]['id'] != $id) {
                 // Show failed system message if other user is already using this display name.
                 $this->system->setMessage(
                     'error',
                     'Different user is already using this display name!'
                 );
             }

             return false;
         }

         // Change user's display name in database.
         $update = $this->database->update(
             'display_name',
             'users',
             'WHERE id = ?',
             [$displayname, $id]
         );

         // Check if row value has been changed in database.
         if ($update != 1) {
             $this->system->setMessage(
                 'error',
                 'System couldn\'t change your display name, please try again!'
             );

             return false;
         }

         // Publish system post about display name change.
         $o_post = new Post($this->system, $this->database, $this->validate);
         $o_post->create($id, NULL, 11);

         // Show successful system message about changed display name.
         $this->system->setMessage(
             'success',
             'Your display name has been changed successfully!'
         );

         return true;
     }

    /**
     * Change password of user.
     *
     * @since 0.1.0
     * @var integer $id ID of user.
     * @var string $old Old password.
     * @var string $new New password.
     * @var string $repeat Repeated new password.
     * @return boolean Result of this method.
     */
     public function changePassword($id, $old, $new, $repeat) {
         // Get selected user's password and e-mail address from database.
         $user = $this->database->read(
             'password, email',
             'users',
             'WHERE id = ?',
             [$id]
         )[0];

         // Check if old password is correct.
         if (!password_verify($old, $user['password'])) {
             $this->system->setMessage(
                 'error',
                 'Old password is incorrect!'
             );

             return false;
         }

         // Check if new password is valid.
         if (!$this->validate->password($new)) {
             return false;
         }

         // Check if new passwords are the same.
         if ($new !== $repeat) {
             $this->system->setMessage(
                 'error',
                 'New passwords are not the same!'
             );

             return false;
         }

         // Hash a password with BCrypt algorithm.
         $password = password_hash($new, PASSWORD_BCRYPT, ['cost' => 13]);

         // Change password in database.
         $update = $this->database->update(
             'password',
             'users',
             'WHERE id = ?',
             [$password, $id]
         );

         // Check if row value has been changed in database.
         if ($update != 1) {
             $this->system->setMessage(
                 'error',
                 'System could\'t change your password, please try again!'
             );

             return false;
         }

         // TODO Send an e-mail with password change notification.


         // Show successful system message about changed password.
         $this->system->setMessage(
             'success',
             'Your password has been changed successfully!'
         );

         return true;
     }

     /**
      * Change user's birthdate.
      *
      * @since 0.1.0
      * @var integer $id ID of user.
      * @var integer $day User's new birthdate day.
      * @var integer $month User's new birthdate month.
      * @var integer $year User's new birthdate year.
      * @return boolean Result of this method.
      */
      public function changeBirthdate($id, $day, $month, $year) {
          // Make sure that birthdate values are all numbers.
          $day = intval($day);
          $month = intval($month);
          $year = intval($year);

          // Make sure that birthdate is valid.
          if (!checkdate($month, $day, $year)) {
              $this->system->setMessage(
                  'error',
                  'Your birthdate seems to be invalid!'
              );

              return false;
          }

          // Combine birthdate into MySQL date format.
          $birthdate = $year . '-' . $month . '-' . $day;

          // Change user's birthdate in database.
          $update = $this->database->update(
              'birthdate',
              'users_details',
              'WHERE user_id = ?',
              [$birthdate, $id]
          );

          // Check if row value has been changed in database.
          if ($update != 1) {
              $this->system->setMessage(
                  'error',
                  'System couldn\'t change your birthdate, please try again!'
              );

              return false;
          }

          // Show successful system message on birthdate change.
          $this->system->setMessage('success', 'Your birthdate has been changed successfully!');
          return true;
      }

     /**
      * Change user's gender.
      *
      * @since 0.1.0
      * @var integer $id ID of user.
      * @var integer $city User's new gender value.
      * @return boolean Result of this method.
      */
      public function changeGender($id, $gender) {
          // Make sure that gender value is a number.
          $gender = intval($gender);

          // Set null if gender value is empty.
          if (empty($gender)) {
              $gender = null;
          }

          // Change user's gender in database.
          $update = $this->database->update(
              'gender',
              'users_details',
              'WHERE user_id = ?',
              [$gender, $id]
          );

          // Check if row value has been changed in database.
          if ($update != 1) {
              $this->system->setMessage(
                  'error',
                  'System couldn\'t change your gender, please try again!'
              );

              return false;
          }

          // Show successful system message on changed gender.
          $this->system->setMessage('success', 'Your gender has been changed successfully!');
          return true;
      }

     /**
      * Change user's city name.
      *
      * @since 0.1.0
      * @var integer $id ID of user.
      * @var string $city New city name.
      * @return boolean Result of this method.
      */
      public function changeCity($id, $city) {
          // Validate new city name.
          if (!$this->validate->city($city)) {
              return false;
          }

          // Escape HTML characters in new city name.
          $city = htmlspecialchars($city, ENT_QUOTES);

          // Set null if city name value is empty.
          if (strlen($city) === 0) {
              $city = null;
          }

          // Change user's city name in database.
          $update = $this->database->update(
              'city',
              'users_details',
              'WHERE user_id = ?',
              [$city, $id]
          );

          // Check if row value has been changed in database.
          if ($update != 1) {
              $this->system->setMessage(
                  'error',
                  'System couldn\'t change your city name, please try again!'
              );

              return false;
          }

          // Show successful system message on changed city name.
          $this->system->setMessage(
              'success',
              'Your city name has been changed successfully!'
          );

          return true;
      }

     /**
      * Change user's profile page description.
      *
      * @since 0.1.0
      * @var integer $id ID of user.
      * @var string $description New profile description.
      * @return boolean Result of this method.
      */
      public function changeDescription($id, $description) {
          // Check if new description contains more than 500 characters.
          if (strlen($description) >= 500) {
              $this->system->setMessage(
                  'error',
                  'Profile description can\'t contain more than 500 characters'
              );

              return false;
          }

          // Escape HTML characters in new description.
          $description = htmlspecialchars($description, ENT_QUOTES);

          // Set null if new description value is empty.
          if (strlen($description) === 0) {
              $description = NULL;
          }

          // Change user's description in database.
          $update = $this->database->update(
              'description',
              'users_details',
              'WHERE user_id = ?',
              [$description, $id]
          );

          // Check if row value has been changed in database.
          if ($update != 1) {
              $this->system->setMessage(
                  'error',
                  'System couldn\'t change your description, please try again!'
              );

              return false;
          }

          // Show successful system message on changed profile descrition.
          $this->system->setMessage(
              'success',
              'Your profile description has been changed successfully!'
          );

          return true;
      }

      /**
       * Change user's avatar and create 3 resolutions for it.
       *
       * @since 0.1.0
       * @var integer $id ID of user.
       * @var string $file Global $_FILES variable containing path to the new avatar.
       * @return boolean Result of this method.
       */
      public function changeAvatar($id, $file) {
          // Check if file has been uploaded correctly.
          if ($file['error'] != 0) {
              // Show failed system message if avatar couldn't be uploaded.
              $o_system->setMessage(
                  'error',
                  'Avatar couldn\'t be uploaded!'
              );
          }

          // Store only path to the new avatar.
          $file = $file['tmp_name'];

          // Store default variables for checking if hash is unique.
          $isHashUnique = false;
          $avatarHash = null;

          // Generate random hash for user's new avatar.
          while ($isHashUnique != true) {
              // Generate new hash for avatar.
              $avatarHash = $this->system->getRandomHash(16);

              // Get same avatar's hash from database.
              $duplicateHash = $this->database->read(
                  'id',
                  'users',
                  'WHERE avatar = ?',
                  [$avatarHash]
              );

              // Check if avatar's hash is unique.
              if (count($duplicateHash) == 0) {
                  $isHashUnique = true;

                  // Create avatars and store them in a folder with unique name.
                  $o_image = new Image();
                  if (!$o_image->createAvatar($file, $avatarHash)) {
                      $this->system->setMessage(
                          'error',
                          'System could\'t make an avatar! Please, try again!'
                      );

                      return false;
                  }

                  // Update avatar's hash in database.
                  $insertedHash = $this->database->update(
                      'avatar',
                      'users',
                      'WHERE id = ?',
                      [$avatarHash, $id]
                  );

                  // Return error if hash couldn't be inserted in database.
                  if (empty($insertedHash)) {
                      $this->system->setMessage(
                          'error',
                          'System couldn\'t change your avatar, please try again!'
                      );

                      return false;
                  }
              }
          }

          // Update user's avatar in session.
          $_SESSION['user']['avatar'] = $avatarHash;

          // Show successful system message on changed avatar.
          $this->system->setMessage(
              'success',
              'Your avatar has been changed successfully!'
          );

          return true;
      }

    /**
     * Try to authenticate user with username and password values from $_POST.
     * Method validates values from inputs, checks account status and creates new session.
     * Method throws a system error message if it couldn't log in.
     *
     * @since 0.1.0
     */
    public function login()
    {
        // Check if login method has been called correctly.
        if (empty($_POST['submit']) || $_POST['submit'] !== 'login') {
            $o_system->setMessage('error', 'Login form has been called incorrectly.');
            return false;
        }

        // Store POST values in a variables.
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Remember if username is an e-mail address.
        $isEmail = false;

        // Check if username is an e-mail address.
        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $isEmail = true;
        }

        // Remember if input fields are valid.
        $isUsernameValid = false;
        $isPasswordValid = false;

        // Validate username or e-mail address.
        if ($isEmail) {
            $isUsernameValid = $this->validate->email($email);
        } else {
            $isUsernameValid = $this->validate->username($username);
        }

        // Validate password.
        $isPasswordValid = $this->validate->password($password);

        // Check if both input fields are valid.
        if (!$isUsernameValid || !$isPasswordValid) {
            return false;
        }

        // Get selected details about user from database.
		$user = $this->database->read(
			'id, display_name, username, email, password, login_count, avatar, account_type, account_standing',
			'users',
			$isEmail ? 'WHERE email = ?' : 'WHERE username = ?',
			[$username]
		);

        // Check if any user has been found.
		if (count($user) != 1) {
            $this->system->setMessage(
                'error',
                'Wrong username/e-mail or password.'
            );

			return false;
		}

        // Check if password is correct.
		if (!password_verify($password, $user[0]['password'])) {
			$this->system->setMessage(
                'error',
                'Wrong username/e-mail or password.'
            );

			return false;
		}

        // Check if user is banned.
        if ($user[0]['account_standing'] == 2) {
            // TODO Display left time of ban.
            $this->system->setMessage(
                'error',
                'Your account has been banned for some time.'
            );
			return false;
        }

        // Store common variables.
		$currentLoginCount = $user[0]['login_count'] + 1;
		$currentIP = $this->system->getVisitorIP();
		$currentDatetime = $this->system->getDatetime();
		$currentAgent = substr($this->system->getVisitorAgent(), 0, 256);

        // Update user's login details.
		$this->database->update(
			'login_ip, login_datetime, login_count, last_online',
			'users',
			'WHERE id = ?',
			[$currentIP, $currentDatetime, $currentLoginCount, $currentDatetime, $user[0]['id']]
		);

        // Log details about successful login.
		$this->database->create(
			'user_id, ip, datetime, agent',
			'log_logins',
			'',
			[$user[0]['id'], $currentIP, $currentDatetime, $currentAgent]
		);

        // Check if session can be created.
        if (!$this->session->create($user[0], $currentIP, $currentDatetime)) {
            $this->system->setMessage(
                'error',
                'Couldn\'t create an account session.'
            );

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
        // Check if register method has been called correctly.
        if (empty($_POST['submit']) || $_POST['submit'] !== 'register') {
            $this->system->setMessage('error', 'Register form has been called incorrectly.');
            return false;
        }

        // Store POST values in a variables.
        $displayname = $_POST['displayname'];
        $username = strtolower($_POST['username']);
        $email = strtolower($_POST['email']);
        $password = $_POST['password'];
        $passwordrepeat = $_POST['passwordrepeat'];

        // Validate new display name.
        $isDisplaynameValid = $this->validate->displayName($displayname);

        // Validate new username.
        $isUsernameValid = $this->validate->username($username);

        // Validate new e-mail address.
        $isEmailValid = $this->validate->email($email);

        // Validate new password.
        $isPasswordValid = $this->validate->password($password);

        // Check if both passwords are the same.
        $arePasswordsSame = ($password === $passwordrepeat) ? true : false;

        // Check if all input fields are valid.
        if (!$isDisplaynameValid || !$isUsernameValid || !$isPasswordValid || !$arePasswordsSame) {
            return false;
        }

        // Get any users using same display name, username or e-mail address from database.
        $sameUsers = $this->database->read(
			'id, display_name, username, email',
			'users',
			'WHERE display_name = ? OR username = ? OR email = ?',
			[$displayname, $username, $email]
		);

        // Check if any user is already using same display name, username or e-mail address.
		if (count($sameUsers) != 0) {
			foreach ($sameUsers as $duplicateUser) {
                // Check if no other user is using same display name.
				if ($displayname === $duplicateUser['display_name']) {
                    $this->system->setMessage(
                        'error',
                        'Display name is already in use.'
                    );
				}

                // Check if no other user is using same username.
				if ($username === $duplicateUser['username']) {
                    $this->system->setMessage(
                        'error',
                        'Username is already in use.'
                    );
				}

                // Check if no other user is using same e-mail address.
				if ($email === $duplicateUser['email']) {
                    $this->system->setMessage(
                        'error',
                        'E-mail address is already in use.'
                    );
				}
			}

			return false;
		}

        // Store common variables.
        $currentIP = $this->system->getVisitorIP();
		$currentDatetime = $this->system->getDatetime();

        // Get user's country code and timezone.
		$o_geoip = new GeoIP($currentIP);

        // Insert new user into database if nothing has returned an error.
        // TODO Check again if result has been inserted.
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

        // Show successful system message on new account creation.
        $this->system->setMessage(
            'success',
            'Account has been created successfully.'
        );

        // Store default values for e-mail verification.
		$isHashUnique = false;
		$uniqueHash = null;

        // Generate random hash for e-mail verification.
		while ($isHashUnique != true) {
            // Generate random hash.
			$uniqueHash = $this->system->getRandomHash(16);

            // Get same hash from database if existing.
			$duplicateHash = $this->database->read(
				'hash',
				'key_email',
				'WHERE hash = ?',
				[$uniqueHash]
			);

            // Check if hash is unique, if true, insert it into database.
			if (count($duplicateHash) == 0) {
                // Stop the while loop when unique hash has been found.
				$isHashUnique = true;

                // Get created user ID.
				$userID = $this->database->read(
					'id',
					'users',
					'WHERE username = ?',
					[$username]
				)[0];

                // Create new row for user details.
                $this->database->create(
                    'user_id',
                    'users_details',
                    '',
                    [$userID['id']]
                );

                // Create new row for not verified e-mail address.
				$this->database->create(
					'user_id, email, date, hash',
					'key_email',
					'',
					[$userID['id'], $email, $currentDatetime, $uniqueHash]
				);

				break;
			}
		}

        // Send a verification e-mail.
		$o_mail = new Mail($this->system);
		$o_mail->sendRegistrationMail($email, $displayname, $uniqueHash, $userID['id']);

		return true;
    }
}
