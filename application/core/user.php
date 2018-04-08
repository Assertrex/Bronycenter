<?php

namespace BronyCenter;

use DateTime;

interface UserInterface
{
    public static function getInstance(bool $reset);
    public function isCurrentUserID(int $id) : bool;
    public function isCurrentUserAdministrator() : bool;
    public function isCurrentUserModerator() : bool;
    public function isOnlineByID(int $userID) : bool;
    public function isOnlineByDatetime(string $datetime) : bool;
    public function countCreatedAccounts(): int;
    public function countExistingMembers(): int;
    public function countOnlineMembers(): int;
    public function countRecentlyOnlineMembers(string $intervalUnitName): int;
    public function countMembersByAccountType(): array;
    public function countMembersByAccountStanding(): array;
    public function getMembers(array $settings) : array;
    public function getUserDetails(int $id, array $settings) : array;
    public function generateUserDetails(int $id, array $settings) : array;
}

class User implements UserInterface
{
    private static $instance = null;
    private $o_translation = null;
    private $o_database = null;
    private $o_flash = null;
    private $o_utilities = null;

    private $usersDetails = null;

    public function __construct()
    {
        $this->o_translation = Translation::getInstance();
        $this->o_database = Database::getInstance();
        $this->o_flash = Flash::getInstance();
        $this->o_utilities = Utilities::getInstance();
    }

    public static function getInstance(bool $reset = false)
    {
        if (!self::$instance || $reset === true) {
            self::$instance = new User();
        }

        return self::$instance;
    }

    public function isCurrentUserID(int $id) : bool
    {
        if ($id != $_SESSION['account']['id']) {
            return false;
        }

        return true;
    }

    public function isCurrentUserAdministrator() : bool
    {
        if ($_SESSION['account']['type'] != 9) {
            return false;
        }

        return true;
    }

    public function isCurrentUserModerator() : bool
    {
        if ($_SESSION['account']['type'] != 8 && $_SESSION['account']['type'] != 9) {
            return false;
        }

        return true;
    }

    public function isOnlineByID(int $userID) : bool
    {
        $datetime = $this->o_database->read(
            'id, last_online',
            'users',
            'WHERE id = ?',
            [$userID],
            false
        )['last_online'];

        if ($this->o_utilities->countDateInterval($datetime) > 30) {
            return false;
        }

        return true;
    }

    public function isOnlineByDatetime(string $datetime) : bool
    {
        if ($this->o_utilities->countDateInterval($datetime) > 30) {
            return false;
        }

        return true;
    }

    public function countCreatedAccounts(): int
    {
        $amount = $this->o_database->read(
            'count(id) AS amount',
            'users',
            '',
            [],
            false
        )['amount'];

        return intval($amount) ?: 0;
    }

    public function countExistingMembers(): int
    {
        $amount = $this->o_database->read(
            'count(id) AS amount',
            'users',
            'WHERE account_type != 0 AND account_standing NOT IN (2, 3)',
            [],
            false
        )['amount'];

        return intval($amount) ?: 0;
    }

    public function countOnlineMembers(): int
    {
        $amount = $this->o_database->read(
            'count(id) AS amount',
            'users',
            "WHERE last_online >= (DATE_SUB(NOW(), INTERVAL 30 SECOND))",
            [],
            false
        )['amount'];

        return intval($amount) ?: 0;
    }

    public function countRecentlyOnlineMembers(string $intervalUnitName): int
    {
        $amount = $this->o_database->read(
            'count(id) AS amount',
            'users',
            "WHERE last_online >= (DATE_SUB(NOW(), INTERVAL $intervalUnitName))",
            [],
            false
        )['amount'];

        return intval($amount) ?: 0;
    }

    public function countMembersByAccountType(): array
    {
        $members = $this->o_database->read(
            'account_type, count(id) AS amount',
            'users',
            'GROUP BY account_type',
            []
        );

        return is_array($members) ? $members : [];
    }

    public function countMembersByAccountStanding(): array
    {
        $members = $this->o_database->read(
            'account_standing, count(id) AS amount',
            'users',
            'GROUP BY account_standing',
            []
        );

        return is_array($members) ? $members : [];
    }

    public function getMembers(array $settings) : array
    {
        $sql_additional = '';

        if (!empty($settings['where'])) {
            $sql_additional .= ' WHERE ' . $settings['where'];
        }

        if (!empty($settings['orderBy'])) {
            $sql_additional .= ' ORDER BY ' . $settings['orderBy'];
        }

        if (!empty($settings['limit'])) {
            $sql_additional .= ' LIMIT ' . $settings['limit'];
        }

        $members = $this->o_database->read(
            'id',
            'users',
            $sql_additional,
            []
        );

        return is_array($members) ? $members : [];
    }

    public function getUserDetails(int $id, array $settings) : array
    {
        $sql_columns = 'u.id, u.display_name, u.username, u.registration_datetime, ' .
            'u.login_datetime, u.last_online, u.country_code, u.timezone, u.avatar, ' .
            'u.account_type, d.short_description, u.account_standing, d.birthdate, ' .
            'd.gender, d.city, u.displayname_changes, u.displaynames_recent';
        $sql_joins = 'INNER JOIN users_details d ON u.id = d.user_id';

        if (!empty($settings['sensitive']) && $this->isCurrentUserID($id)) {
            $sql_columns .= ', u.email, u.registration_ip, u.login_ip, u.login_count';
        }

        if (!empty($settings['descriptions'])) {
            $sql_columns .= ', d.full_description, d.contact_methods, d.favourite_music, ' .
                'd.favourite_movies, d.favourite_games, d.fandom_becameabrony, ' .
                'd.fandom_favouritepony, d.fandom_favouriteepisode, d.creations_links';
        }

        if (!empty($settings['statistics'])) {
            $sql_columns .= ', s.user_points, s.posts_created, s.posts_likes_given, ' .
                's.posts_comments_given, s.posts_removed, s.posts_removed_mod, ' .
                's.posts_comments_removed, s.posts_comments_removed_mod, ' .
                's.posts_likes_received, s.posts_comments_received';
            $sql_joins .= ' INNER JOIN users_statistics s ON u.id = s.user_id';
        }

        $details = $this->o_database->read(
			$sql_columns,
			'users u',
			$sql_joins . ' WHERE u.id = ?',
			[$id],
            false
		);

        return is_array($details) ? $details : [];
    }

    public function generateUserDetails(int $id, array $settings) : array
    {
        if (!empty($this->usersDetails[$id]) && is_array($this->usersDetails[$id])) {
            return $this->usersDetails[$id];
        }

        $details = $this->getUserDetails($id, $settings);

        if (empty($details)) {
            return false;
        }

        $details = $this->formatUserGeneratedDetails($details);

        $this->usersDetails[$id] = $details;

        return $details;
    }

    private function formatUserGeneratedDetails(array $details) : array
    {
        $details['is_online'] = $this->isOnlineByDatetime($details['last_online']);
        $details['avatar'] = $details['avatar'] ?? 'default';

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

        if (!is_null($details['country_code'])) {
            $details['country_name'] = $this->o_utilities->getCountryName($details['country_code']) ?: $this->o_translation->getString('common', 'unknownCountry');
        }

        if (!is_null($details['registration_datetime'])) {
            $details['registration_interval'] = $this->o_utilities->getDateIntervalString($this->o_utilities->countDateInterval($details['registration_datetime']));
        }

        if (!is_null($details['last_online'])) {
            $details['last_online_interval'] = $this->o_utilities->getDateIntervalString($this->o_utilities->countDateInterval($details['last_online']));
        }

        if (!is_null($details['birthdate'])) {
            $current_date = new DateTime();
            $age_interval = new DateTime($details['birthdate']);
            $age_interval = $current_date->diff($age_interval);
            $details['birthdate_years'] = $age_interval->format('%y ') . $this->o_translation->getString('common', 'yearsOld');
        }

        $recentDisplaynamesArray = explode(',', $details['displaynames_recent'] ?? '');
        $details['recent_displaynames_divs'] = '';

        if ($recentDisplaynamesArray[0] != '') {
            foreach ($recentDisplaynamesArray as $recentDisplayname) {
                $details['recent_displaynames_divs'] .= '<div style=\'line-height: 1.2;\'><small>' . strip_tags($recentDisplayname) . '</small></div>';
            }
        }

        $details['tooltip'] = '
        <div class=\'user-tooltip\'>
            <div>
                <div>' . strip_tags($details['display_name']) . '</div>
                <div><small class=\'text-muted\'>@' . strip_tags($details['username']) . '</small></div>
            </div>
            <div class=\'user-tooltip-details\'>
                <div>
                    <span class=\'text-center mr-1\'>
                        <i class=\'fa fa-transgender text-primary\' aria-hidden=\'true\'></i>
                    </span>
                    <small>' . strip_tags($details['gender'] ? $details['gender_name'] : $this->o_translation->getString('common', 'unknownGender')) . '</small>
                </div>
                <div>
                    <span class=\'text-center mr-1\'>
                        <i class=\'fa fa-user-o text-primary\' aria-hidden=\'true\'></i>
                    </span>
                    <small>' . strip_tags($details['birthdate_years'] ?? $this->o_translation->getString('common', 'unknownAge')) . '</small>
                </div>
                <div>
                    <span class=\'text-center mr-1\'>
                        <i class=\'fa fa-map-marker text-primary\' aria-hidden=\'true\'></i>
                    </span>
                    <small>' . strip_tags($details['country_name'] ?? $this->o_translation->getString('common', 'unknownCountry')) . '</small>
                </div>
            </div>
        </div>
        ';

        $details['filled_about']     = !empty($details['full_description']) || !empty($details['contact_methods']) ||
                                       !empty($details['favourite_music']) || !empty($details['favourite_movies']) ||
                                       !empty($details['favourite_games']);
        $details['filled_fandom']    = !empty($details['fandom_becameabrony']) || !empty($details['fandom_favouritepony']);
        $details['filled_creations'] = !empty($details['creations_links']);

        return $details;
    }
}
