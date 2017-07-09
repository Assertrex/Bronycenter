<?php

/**
 * Class used for creating and handling account sessions.
 *
 * @copyright 2017 BronyCenter
 * @author Assertrex <norbert.gotowczyc@gmail.com>
 * @since 0.1.0
 */
class Session
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
     * Extend existing PHP session or create new one.
     *
     * @since 0.1.0
     * @var object $o_system Object of a system class.
     * @var object $o_database Object of a database class.
     */
    public function __construct($o_system, $o_database)
	{
        session_start();

        // Store required class objects in a properties.
        $this->system = $o_system;
        $this->database = $o_database;
	}

    /**
     * Create new account session on user login.
     * It's not very secure yet, but it should be enough for now.
     *
     * @since 0.1.0
     * @var array $details Details of user that has just logged in with login form.
     * @var string $ip IP of user that has just logged in with login form.
     * @var string $datetime Datetime of login function.
     * @return boolean Result of this method.
     */
    public function create($details, $ip, $datetime) {
        // Destroy session first to make sure that new account session will be clean.
        session_destroy();
        session_start();

        // Store user details in an account session.
		$_SESSION['user']['displayName'] = $details['display_name'];
        $_SESSION['user']['username'] = $details['username'];
        $_SESSION['user']['email'] = $details['email'];
        $_SESSION['user']['avatar'] = $details['avatar'];

        // Store account details in an account session.
        $_SESSION['account']['id'] = $details['id'];
        $_SESSION['account']['type'] = $details['account_type'];
        $_SESSION['account']['standing'] = $details['account_standing'];

        // Store login details in an account session.
        $_SESSION['login']['ip'] = $ip;
        $_SESSION['login']['datetime'] = $datetime;

        // Always return true on finish (as there shouldn't go anything wrong).
        return true;
	}

    /**
     * Verify if account session is existing and valid.
     * Note, that switching networks will change IP and return invalid session.
     * @todo Rewrite method for better security and to allow IP changes.
     *
     * @since 0.1.0
     * @return boolean State of an account session.
     */
    public function verify() {
        // Check if account session is existing.
        if (empty($_SESSION['account'])) {
            return false;
        }

        // Check if IP address has changed (try to prevent session stealing).
        if (empty($_SESSION['login']['ip']) || $_SESSION['login']['ip'] !== $this->system->getVisitorIP()) {
            $this->system->setMessage(
                'error',
                'You have probably switched between networks and your IP address has changed. You need to log in again.'
            );

            return false;
        }

        // Get details about selected account from database.
		$details = $this->database->read(
			'id, email, account_type, account_standing',
			'users',
			'WHERE id = ?',
			[$_SESSION['account']['id']]
		)[0];

        // Check if account has been banned.
        // TODO Check if ban has ended and switch account standing value back to 0.
        // TODO Show time left to end ban.
        if ($details['account_standing'] == 2) {
            $this->system->setMessage(
                'error',
                'Your account has been banned for some time.'
            );

            return false;
        }

        // Update dynamic details in an account session.
        $_SESSION['user']['email'] = $details['email'];
        $_SESSION['account']['type'] = $details['account_type'];
        $_SESSION['account']['standing'] = $details['account_standing'];

        // Update datetime of when account has been last seen logged.
        $this->database->update(
			'last_online',
			'users',
			'WHERE id = ?',
			[$this->system->getDatetime(), $details['id']]
		);

        return true;
	}
}
