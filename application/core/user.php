<?php

// Used for getting and modifing user details

namespace BronyCenter;

use DateTime;

class User
{
    private static $instance = null;
    private $o_translation = null;
    private $database = null;
    private $flash = null;
    private $utilities = null;
    private $validator = null;
    private $users_details = [];

    public function __construct()
    {
        $this->o_translation = Translation::getInstance();
        $this->database = Database::getInstance();
        $this->flash = Flash::getInstance();
        $this->utilities = Utilities::getInstance();
        $this->validator = Validator::getInstance();
    }

    public static function getInstance(bool $reset = false)
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
     * Check if current user is a moderator
     *
     * @since Release 0.1.0
     * @return boolean Moderator status of a user
    **/
    public function isCurrentModerator()
    {
        if ($_SESSION['account']['type'] == 8 || $_SESSION['account']['type'] == 9) {
            return true;
        }

        return false;
    }

    /**
     * Get active registered members ordered by last seen time
     *
     * @var integer $amount - Amount of members to fetch
     * @return array - Array of active registered members ID's
     */
    public function getMembersList(int $amount = 50) : array
    {
        $members = $this->database->read(
            'id',
            'users',
            'WHERE account_type != 0 AND account_standing NOT IN (2, 3) ORDER BY last_online DESC LIMIT ?',
            [$amount]
        );

        return is_array($members) ? $members : [];
    }

    /**
     * Count all created accounts (including not-verified and banned)
     */
    public function countCreatedAccounts(): int
    {
        $members = $this->database->read(
            'count(id) AS amount',
            'users',
            '',
            [],
            false
        )['amount'];

        return intval($members) ?: 0;
    }

    /**
     * Count not-banned members with activated accounts
     */
    public function countExistingMembers(): int
    {
        $members = $this->database->read(
            'count(id) AS amount',
            'users',
            'WHERE account_type != 0 AND account_standing NOT IN (2, 3)',
            [],
            false
        )['amount'];

        return intval($members) ?: 0;
    }

    public function countOnlineMembers(string $intervalUnitName = '1 MINUTE'): int
    {
        $members = $this->database->read(
            'count(id) AS amount',
            'users',
            "WHERE last_online >= (DATE_SUB(NOW(), INTERVAL $intervalUnitName))",
            [],
            false
        )['amount'];

        return intval($members) ?: 0;
    }

    public function countMembersByAccountType(): array
    {
        $members = $this->database->read(
            'account_type, count(id) AS amount',
            'users',
            'GROUP BY account_type',
            []
        );

        return is_array($members) ? $members : [];
    }

    public function countMembersByAccountStanding(): array
    {
        $members = $this->database->read(
            'account_standing, count(id) AS amount',
            'users',
            'GROUP BY account_standing',
            []
        );

        return is_array($members) ? $members : [];
    }

    /**
     * Get details about selected user and generate additional informations
     *
     * @since Release 0.1.0
     * @var integer $id ID of a user
     * @var array $settings Select which data should be fetched
     * @return array Contains user details
     */
    public function getUserDetails($id, $settings = [])
    {
        // Start with default SQL columns and joins
        $sql_columns = 'u.id, u.display_name, u.username, u.registration_datetime, u.login_datetime, ' .
                       'u.last_online, u.country_code, u.timezone, u.avatar, u.account_type, d.short_description, ' .
                       'u.account_standing, d.birthdate, d.gender, d.city, u.displayname_changes, u.displaynames_recent';
        $sql_additional = 'INNER JOIN users_details d ON u.id = d.user_id';

        // Add user's statistics to the columns (usually used for statistics)
        if (!empty($settings['statistics'])) {
            $sql_columns .= ', user_points, posts_created, posts_likes_given, posts_comments_given, ' .
                            'posts_removed, posts_likes_received, posts_comments_receved';
            $sql_additional .= ' INNER JOIN users_statistics s ON u.id = s.user_id';
        }

        // Add user's descriptions to the columns (usually used for profiles and settings)
        if (!empty($settings['descriptions'])) {
            $sql_columns .= ', d.full_description, d.contact_methods, ' .
                            'd.favourite_music, d.favourite_movies, d.favourite_games, d.fandom_becameabrony, ' .
                            'd.fandom_favouritepony, d.fandom_favouriteepisode, d.creations_links';
        }

        // Add user's more sensitive details (usually used for settings)
        if (!empty($settings['sensitive']) && $id == $_SESSION['account']['id']) {
            $sql_columns .= ', u.email, u.registration_ip, u.login_ip, u.login_count';
        }

        // Get details about user from database
        $user = $this->database->read(
			$sql_columns,
			'users u',
			$sql_additional . ' WHERE u.id = ?',
			[intval($id)],
            false
		);

        // Return false if user does not exist
        if (empty($user)) {
            return false;
        }

        // Return details about selected user
        return $user;
    }

    /**
     * Generate additional details about selected user
     *
     * @since Release 0.1.0
     * @var integer $id ID of a user
     * @var array $settings Select which data should be fetched (from getUsersDetails method)
     * @return array Contains user details with additional details
     */
    public function generateUserDetails($id, $settings = [])
    {
        // Get integer from user's ID and return false if ID is not valid
        if (empty($id = intval($id))) {
            return false;
        }

        // Get cached version of user's details if available
        if (!empty($this->users_details[$id])) {
            return $this->users_details[$id];
        }

        // Get details about user if no have been fetched
        $details = $this->getUserDetails($id, $settings);

        // Return false if user have not been found
        if (empty($details)) {
            return false;
        }

        // Check if user is currently logged in
        $details['is_online'] = $this->isOnline(null, $details['last_online']);

        // Set user's avatar as default if user haven't uploaded any
        $details['avatar'] = $details['avatar'] ?? 'default';

        // Name gender types
        switch ($details['gender']) {
            case 1:
                $details['gender_name'] = ucfirst($this->o_translation->getString('common', 'male'));
                break;
            case 2:
                $details['gender_name'] = ucfirst($this->o_translation->getString('common', 'female'));
                break;
            default:
                $details['gender_name'] = ucfirst($this->o_translation->getString('common', 'unknown'));
        }

        // Format birthdate if available
        if (!is_null($details['birthdate'])) {
            $current_date = new DateTime();
            $age_interval = new DateTime($details['birthdate']);
            $age_interval = $current_date->diff($age_interval);
            $details['birthdate_years'] = $age_interval->format('%y ') . $this->o_translation->getString('common', 'yearsOld');
        }

        // Format activity datetimes
        if (!is_null($details['registration_datetime'])) {
            $details['registration_interval'] = $this->utilities->getDateIntervalString($this->utilities->countDateInterval($details['registration_datetime']));
        }
        if (!is_null($details['last_online'])) {
            $details['last_online_interval'] = $this->utilities->getDateIntervalString($this->utilities->countDateInterval($details['last_online']));
        }

        // Get a full name of user's country
        if (!is_null($details['country_code'])) {
            $details['country_name'] = $this->utilities->getCountryName($details['country_code']) ?? 'Unknown';
        }

        // Check if additional tabs should be displayed
        $details['filled_about']     = !empty($details['full_description']) || !empty($details['contact_methods']) ||
                                       !empty($details['favourite_music']) || !empty($details['favourite_movies']) ||
                                       !empty($details['favourite_games']);
        $details['filled_fandom']    = !empty($details['fandom_becameabrony']) || !empty($details['fandom_favouritepony']);
        $details['filled_creations'] = !empty($details['creations_links']);

        // Store recent display names
        $recentDisplaynamesArray = explode(',', $details['displaynames_recent']);
        $details['recent_displaynames_divs'] = '';

        if ($recentDisplaynamesArray[0] != '') {
            foreach ($recentDisplaynamesArray as $recentDisplayname) {
                $details['recent_displaynames_divs'] .= '<div style=\'line-height: 1.2;\'><small>' . $recentDisplayname . '</small></div>';
            }
        }

        // Store user details in an tooltip
        $details['tooltip'] = '
        <div style=\'padding: .5rem .25rem; line-height: 1.2;\'>
            <div>' . strip_tags($details['display_name']) . '</div>
            <div><small class=\'text-muted\'>@' . strip_tags($details['username']) . '</small></div>

            <div style=\'padding-top: 8px; text-align: left;\'>
                <div style=\'margin-bottom: 1px;\'>
                    <span class=\'text-center mr-1\' style=\'width: 15px;\'>
                        <i class=\'fa fa-transgender text-primary\' style=\'width: 15px;\' aria-hidden=\'true\'></i>
                    </span>
                    <small>' . strip_tags($details['gender'] ? $details['gender_name'] : 'Unknown gender') . '</small>
                </div>
                <div style=\'margin-bottom: 1px;\'>
                    <span class=\'text-center mr-1\' style=\'width: 15px;\'>
                        <i class=\'fa fa-user-o text-primary\' style=\'width: 15px;\' aria-hidden=\'true\'></i>
                    </span>
                    <small>' . strip_tags($details['birthdate_years'] ?? 'Unknown age') . '</small>
                </div>
                <div>
                    <span class=\'text-center mr-1\' style=\'width: 15px;\'>
                        <i class=\'fa fa-map-marker text-primary\' style=\'width: 15px;\' aria-hidden=\'true\'></i>
                    </span>
                    <small>' . strip_tags($details['country_name'] ?? 'Unknown country') . '</small>
                </div>
            </div>
        </div>
        ';

        // Cache user details
        $this->users_details[$id] = $details;

        return $details;
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
            'display_name, displayname_changes, displaynames_recent',
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
        if (is_null($userDetails['displaynames_recent'])) {
            $userDetails['displaynames_recent'] = str_replace(',', '&#44;', $userDetails['display_name']);
        } else {
            $userDetails['displaynames_recent'] = $userDetails['displaynames_recent'] . ',' . str_replace(',', '&#44;', $userDetails['display_name']);
        }

        // Replace field value in database
        $hasChanged = $this->database->update(
            'display_name, displayname_changes, displaynames_recent',
            'users',
            'WHERE id = ?',
            [$value, $userDetails['displayname_changes'] + 1, $userDetails['displaynames_recent'], $_SESSION['account']['id']]
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
            if (!is_dir(__DIR__ . '/../../public/media/avatars/' . $hashAvatar)) {
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
            if (!is_null($hashPreviousAvatar) && is_dir(__DIR__ . '/../../public/media/avatars/' . $hashAvatar)) {
                // Delete files
                unlink(__DIR__ . '/../../public/media/avatars/' . $hashPreviousAvatar . '/minres.jpg');
                unlink(__DIR__ . '/../../public/media/avatars/' . $hashPreviousAvatar . '/defres.jpg');
                unlink(__DIR__ . '/../../public/media/avatars/' . $hashPreviousAvatar . '/maxres.jpg');

                // Delete a directory
                rmdir(__DIR__ . '/../../public/media/avatars/' . $hashPreviousAvatar);
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
