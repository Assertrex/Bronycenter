<?php

/**
* Used for getting and modifing user details
*
* @since Release 0.1.0
*/

namespace BronyCenter;

use AssertrexPHP\Database;
use AssertrexPHP\Flash;
use AssertrexPHP\Utilities;
use AssertrexPHP\Validator;

class User
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
     * Place for instance of an utilities class
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
        $this->utilities = Utilities::getInstance();
        $this->validator = Validator::getInstance();
    }

    /**
     * Check if instance of current class is existing and create and/or return it
     *
     * @since Release 0.1.0
     * @var boolean Set as true to reset class instance
     * @return object Instance of a current class
     */
    public static function getInstance($reset = false)
    {
        if (!self::$instance || $reset === true) {
            self::$instance = new User();
        }

        return self::$instance;
    }

    /**
     * Check if selected user is online
     *
     * @since Release 0.1.0
     * @var integer Optional ID of a user
     * @var string Optional date of user's last activity
     * @return boolean State of selected user activity
     */
    public function isOnline($id = null, $datetime = null)
    {
        // Check if one of required parameters is existing
        if (is_null($id) && is_null($datetime)) {
            return false;
        }

        // Check if user's last activity datetime has been provided
        if (!is_null($datetime)) {
            // User has been seen in last 40 seconds
            if ($this->utilities->countDateInterval($datetime) < 40) {
                return true;
            }

            // User has not been active for more than 60 seconds
            return false;
        }
    }

    /**
     * Get all active registered members ordered by last seen time
     *
     * @since Release 0.1.0
     * @return array Array of active registered members
     */
    public function getMembersList()
    {
        $members = $this->database->read(
            'id, display_name, username, last_online, avatar, account_type',
            'users',
            'WHERE account_type != 0 AND account_standing NOT IN (8, 9) ORDER BY last_online DESC',
            []
        );

        return $members;
    }

    /**
     * Get all active registered members by last seen time
     *
     * @since Release 0.1.0
     * @var integer ID of a user
     * @return array Array of user details
     */
    public function getUserDetails($id)
    {
        // Get details about user from database
        $user = $this->database->read(
			'u.id, u.display_name, u.username, u.email, u.registration_ip,' .
            'u.registration_datetime, u.login_ip, u.login_datetime, u.login_count,' .
            'u.last_online, u.country_code, u.timezone, u.avatar, u.account_type,' .
            'u.account_standing, u.displayname_changes, u.displayname_recent, d.birthdate, d.gender, d.city, d.short_description,' .
            'd.full_description, d.contact_methods, d.favourite_music, d.favourite_movies,' .
            'd.favourite_games, d.fandom_becameabrony, d.fandom_favouritepony, d.fandom_favouriteepisode,' .
            'd.creations_links',
			'users u',
			'INNER JOIN users_details d ON u.id = d.user_id WHERE u.id = ?',
			[$id],
            false
		);

        // Return false if user has not been found
        if (empty($user)) {
            return false;
        }

        // Return details about selected user
        return $user;
    }

    /**
     * Change user's display name
     *
     * @since Release 0.1.0
     * @var string $value New display name
     * @return string Current display name
     */
    public function changeUserDisplayname($value)
    {
        // Check if new display name is not too short
        if (strlen($value) < 3) {
            $this->flash->error('Display name needs to be at least 3 characters long.');
            return false;
        }

        // Check if new display name is not too long
        if (strlen($value) > 32) {
            $this->flash->error('Display name can\'t be longer than 32 characters.');
            return false;
        }

        // Check if anyone is already using this display name
        $isUsed = $this->database->read(
            'id',
            'users',
            'WHERE display_name = ?',
            [$value],
            false
        );

        if ($isUsed) {
            // Check if user has not made any changes to the display name
            if ($_SESSION['account']['id'] == $isUsed['id']) {
                $this->flash->info('No changes have been made to your display name.');
                return $value;
            }

            $this->flash->error('Different user is already using this display name.');
            return $value;
        }

        // Check how many times user have changed display name
        $userDetails = $this->database->read(
            'display_name, displayname_changes, displayname_recent',
            'users',
            'WHERE id = ?',
            [$_SESSION['account']['id']],
            false
        );

        // Don't allow an display name change if user has changed it 3 times already
        if ($userDetails['displayname_changes'] >= 3) {
            $this->flash->error('You have used the limit of your 3 display name changes.');
            return $value;
        }

        // Store previous display name in a string with escaped commas
        if (is_null($userDetails['displayname_recent'])) {
            $userDetails['displayname_recent'] = str_replace(',', '&#44;', $userDetails['display_name']);
        } else {
            $userDetails['displayname_recent'] = $userDetails['displayname_recent'] . ',' . str_replace(',', '&#44;', $userDetails['display_name']);
        }

        // Replace field value in database
        $hasChanged = $this->database->update(
            'display_name, displayname_changes, displayname_recent',
            'users',
            'WHERE id = ?',
            [$value, $userDetails['displayname_changes'] + 1, $userDetails['displayname_recent'], $_SESSION['account']['id']]
        );

        // Check if display name have been changed
        if ($hasChanged) {
            // Create a public post that inform about that change
            $o_post = Post::getInstance();
            $o_post->add(null, 11);

            $this->flash->success('You have successfully changed your display name.');
            return $value;
        }

        $this->flash->error('Your display name have not been changed due to an unknown error.');
        return $value;
    }

    /**
     * Change user's birthdate
     *
     * @since Release 0.1.0
     * @var string $day New birthdate day
     * @var string $month New birthdate month
     * @var string $year New birthdate year
     * @return string Current birthdate
     */
    public function changeUserBirthdate($day, $month, $year)
    {
        // Make sure that birthdate values contains only numbers
        $day = intval($day);
        $month = intval($month);
        $year = intval($year);

        // If any value is empty, set birthdate as null
        if (empty($day) || empty($month) || empty($year)) {
            $birthdate = null;
        } else {
            // Make sure that birthdate is valid
            if (!checkdate($month, $day, $year) || $year < 1900) {
                $this->flash->error('Your birthdate seems to be invalid!');
                return false;
            }

            // Combine birthdate into MySQL date format
            $birthdate = $year . '-' . $month . '-' . $day;
        }

        // Replace field value in database
        $hasChanged = $this->database->update(
            'birthdate',
            'users_details',
            'WHERE id = ?',
            [$birthdate, $_SESSION['account']['id']]
        );

        // Check if birthdate have been changed
        if ($hasChanged) {
            $this->flash->success('You have successfully changed your birthdate.');
            return $birthdate;
        }

        $this->flash->error('Your birthdate have not been changed due to an unknown error.');
        return false;
    }

    /**
     * Change user's avatar
     *
     * @since Release 0.1.0
     * @var string $file Array containing details about file
     * @return string|boolean Hash of a new avatar
     */
    public function changeAvatar($file)
    {
        // Set hash as not unique on before generating it
        $uniqueHash = false;

        // Generate a hash until it's unique
        while ($uniqueHash === false) {
            // Generate a hash for a new avatar
            $hashAvatar = $this->utilities->getRandomHash(16);

            // Check if a directory, named with a generated hash, already exists
            if (!is_dir('../../media/avatars/' . $hashAvatar)) {
                $uniqueHash = true;
            }
        }

        // Get instance of an Image class (class needs to be included before)
        $classImage = Image::getInstance();

        // Try to create three versions of an avatar
        if ($classImage->createAvatar($file, $hashAvatar)) {
            // Get current avatar hash
            $hashPreviousAvatar = $this->database->read(
                'avatar',
                'users',
                'WHERE id = ?',
                [$_SESSION['account']['id']],
                false
            )['avatar'];

            // Remove a current avatar directory if exists
            if (!is_null($hashPreviousAvatar) && is_dir('../../media/avatars/' . $hashAvatar)) {
                // Delete files
                unlink('../../media/avatars/' . $hashPreviousAvatar . '/minres.jpg');
                unlink('../../media/avatars/' . $hashPreviousAvatar . '/defres.jpg');
                unlink('../../media/avatars/' . $hashPreviousAvatar . '/maxres.jpg');

                // Delete a directory
                rmdir('../../media/avatars/' . $hashPreviousAvatar);
            }

            // Update an avatar hash with a new value
            $this->database->update(
                'avatar',
                'users',
                'WHERE id = ?',
                [$hashAvatar, $_SESSION['account']['id']]
            );

            // Update a session with a new avatar hash
            $_SESSION['user']['avatar'] = $hashAvatar;

            return $hashAvatar;
        }

        return false;
    }

    /**
     * Change user's details settings
     *
     * @since Release 0.1.0
     * @var string $field Name of a column to change
     * @var string $value New value for a column
     * @return string New/current value of a column
     */
    public function changeSettingsDetails($field, $value)
    {
        // Check if field is available for edit
        $availableFields = ['birthdate', 'gender', 'city', 'short_description', 'full_description', 'contact_methods', 'favourite_music', 'favourite_movies', 'favourite_games', 'fandom_becameabrony', 'fandom_favouritepony', 'fandom_favouriteepisode', 'creations_links'];
        if (!in_array($field, $availableFields)) {
            $this->flash->error('Field does not exist or is not available for editing.');
            return false;
        }

        // TODO Limit letters

        // Change value to null if empty
        if (empty($value)) {
            $value = null;
        }

        // Replace field value in database
        $hasChanged = $this->database->update(
            $field,
            'users_details',
            'WHERE id = ?',
            [$value, $_SESSION['account']['id']]
        );

        // Check if creations links have been changed
        if ($hasChanged) {
            $this->flash->success('You have successfully changed your details.');
            return $value;
        }

        $this->flash->error('Your settings have not been changed due to an unknown error.');
        return false;
    }
}
