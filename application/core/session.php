<?php

/**
* Manages user session. It can create, verify and destroy it.
*
* @since Release 0.1.0
*/

namespace BronyCenter;

class Session
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
     * Place for instance of a utilities class
     *
     * @since Release 0.1.0
     */
    private $utilities = null;

    /**
     * Initialize required classes
     *
     * @since Release 0.1.0
     */
    public function __construct()
    {
        $this->database = Database::getInstance();
        $this->flash = Flash::getInstance();
        $this->utilities = Utilities::getInstance();
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
            self::$instance = new Session();
        }

        return self::$instance;
    }

    /** Create a user session after successful login
     *
     * @since Release 0.1.0
     * @var array Array containing details about user to store
     * @return boolean Result of a method
     */
    public function create($array)
    {
        $_SESSION['user']['displayname'] = $array['displayname'];
        $_SESSION['user']['avatar'] = $array['avatar'] ?? 'default';

        $_SESSION['account']['id'] = $array['id'];
        $_SESSION['account']['username'] = $array['username'];
        $_SESSION['account']['type'] = $array['account_type'];
        $_SESSION['account']['standing'] = $array['account_standing'];

        $_SESSION['account']['reason_readonly'] = null;
        $_SESSION['account']['isModerator'] = false;

        if ($_SESSION['account']['type'] == 0) {
            $_SESSION['account']['reason_readonly'] = 'unverified';
        }

        if ($_SESSION['account']['standing'] == 1) {
            $_SESSION['account']['reason_readonly'] = 'muted';
        }

        if (($_SESSION['account']['type'] == 8 || $_SESSION['account']['type'] == 9) && $_SESSION['account']['standing'] == 0) {
            $_SESSION['account']['isModerator'] = true;
        }

        return true;
    }

    /** Verify if a user session is still valid
     *
     * @since Release 0.1.0
     * @return boolean Result of a method
     */
    public function verify()
    {
        if (empty($_SESSION['account']) || empty($_SESSION['user'])) {
            return false;
        }

        // Get details about selected account from database
		$details = $this->database->read(
			'id, display_name, username, avatar, account_type, account_standing',
			'users',
			'WHERE id = ?',
			[$_SESSION['account']['id']],
            false
		);

        if (empty($details)) {
            return false;
        }

        $_SESSION['user']['displayname'] = $details['display_name'];
        $_SESSION['user']['avatar'] = $details['avatar'] ?? 'default';

        $_SESSION['account']['username'] = $details['username'];
        $_SESSION['account']['type'] = $details['account_type'];
        $_SESSION['account']['standing'] = $details['account_standing'];

        $_SESSION['account']['reason_readonly'] = null;
        $_SESSION['account']['isModerator'] = false;

        if ($_SESSION['account']['type'] == 0) {
            $_SESSION['account']['reason_readonly'] = 'unverified';
        }

        if ($_SESSION['account']['standing'] == 1) {
            $_SESSION['account']['reason_readonly'] = 'muted';
        }

        if (($_SESSION['account']['type'] == 8 || $_SESSION['account']['type'] == 9) && $_SESSION['account']['standing'] == 0) {
            $_SESSION['account']['isModerator'] = true;
        }

        // Update datetime of when account has been last seen logged
        $this->database->update(
			'last_online',
			'users',
			'WHERE id = ?',
			[$this->utilities->getDatetime(), $details['id']]
		);

        return true;
    }

    /** Get a history of user logins
     *
     * @since Release 0.1.0
     * @return array Array of user logins
     */
    public function getHistory()
    {
        $array = $this->database->read(
            'ip, datetime, agent',
            'log_logins',
            'WHERE user_id = ? ORDER BY id DESC LIMIT 10',
            [$_SESSION['account']['id']]
        );

        return $array;
    }

    /** Destroy a session to log out a user
     *
     * @since Release 0.1.0
     * @return boolean Result of a method
     */
    public function destroy()
    {
        session_destroy();
        session_start();

        return true;
    }
}
