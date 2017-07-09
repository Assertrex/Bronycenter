<?php

/**
 * Class used for handling files, formatting dates etc.
 *
 * @copyright 2017 BronyCenter
 * @author Assertrex <norbert.gotowczyc@gmail.com>
 * @since 0.1.0
 */
class System
{
    /**
     * Array of settings from configuration file.
     *
     * @since 0.1.0
     * @var array Settings divided into sections.
     */
    private $settings = null;

    /**
     * Is server set up as development or production server.
     * To set up a server as a development server, change it in settings.ini.
     *
     * @since 0.1.0
     * @var boolean
     */
    public $development = false;

    /**
     * Parse configuration file, set up server type and set default timezone.
     *
     * @since 0.1.0
     */
    public function __construct()
    {
        // Parse configuration file and store settings in a variable.
        $this->settings = parse_ini_file(__DIR__ . '/../config/settings.ini', true);

        // Set up server type (development or production).
        $this->development = $this->settings['system']['development'];

        // Set up a timezone for PHP
        date_default_timezone_set($this->settings['system']['timezone']);
    }

    /**
     * Get selected settings section.
     * @todo Display error if section has not been found.
     *
     * @since 0.1.0
     * @var null|array $section Selected settings section.
     * @return array Settings from selected section.
     */
    public function getSettings($section = null) {
        // Take selected section.
        if (!is_null($section)) {
            return $this->settings[$section];
        }

        // Return all settings if not specified.
        return $this->settings;
    }

    /**
     * Store a system message in a session.
     *
     * @since 0.1.0
     * @var string $status Status of messagee (eg. success, error, warning).
     * @var string $message Description of an error.
     * @return boolean Result of this method.
     */
    public function setMessage($status, $message) {
        // Set class name and title for each error type.
        switch($status) {
            case 'success':
                $alertClass = 'alert-success';
                $alertTitle = 'Success!';
                break;
            case 'info':
                $alertClass = 'alert-info';
                $alertTitle = 'Info!';
                break;
            case 'warning':
                $alertClass = 'alert-warning';
                $alertTitle = 'Warning!';
                break;
            case 'error':
                $alertClass = 'alert-danger';
                $alertTitle = 'Error!';
                break;
            default:
                $alertClass = 'alert-danger';
                $alertTitle = 'Unknown error type!';
        }

        // Insert system message into an array.
        $_SESSION['messages'][] = [
            'status' => $status,
            'message' => $message,
            'datetime' => $this->getDatetime(),
            'alert-class' => $alertClass,
            'alert-title' => $alertTitle
        ];

		return true;
    }

    /**
     * Get an array with system messages from session.
     *
     * @since 0.1.0
     * @return array Contains system messages.
     */
	public function getMessages()
	{
        // Store system messages array in a variable.
        $messages = $_SESSION['messages'] ?? [];

        // Return an array with system messages.
		return $messages;
	}

    /**
     * Clear an array containing system messages.
     *
     * @since 0.1.0
     * @return boolean Result of this method.
     */
	public function clearMessages()
	{
        // Empty messages value in a session.
		$_SESSION['messages'] = [];

		return true;
	}

    /**
     * Get current datetime in MySQL format.
     * Returns a datetime in a format: YYYY-MM-DD HH:MM:SS.
     *
     * @since 0.1.0
     * @return string Datetime in MySQL format.
     */
    public function getDatetime()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * Get visitor's IP address.
     * @todo Check if there's better way to get the right IP (for proxy and VPN).
     *
     * @since 0.1.0
     * @return string Visitor's IP address.
     */
    public function getVisitorIP()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Get visitor's browser's user agent.
     *
     * @since 0.1.0
     * @return string Visitor's browser's user agent.
     */
    public function getVisitorAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * Generate random hash with MD5 and uniqid.
     *
     * @since 0.1.0
     * @var int $length Amount of characters (32 is max).
     * @return string Hash with a requested amount of characters.
     */
    public function getRandomHash($length)
	{
		return substr(md5(uniqid(rand(), true)), 0, $length);
	}

    /**
     * Count interval between two dates.
     *
     * @since 0.1.0
     * @var string $date1 Older date/datetime.
     * @var null|string $date2 Newer date/datetime (or null for current datetime).
     * @return integer Interval between two dates in seconds.
     */
    public function countDateInterval($date1, $date2 = null) {
        // Get current datetime if second date has not been specified.
		if (is_null($date2)) {
			$date2 = $this->getDatetime();
		}

        // Count interval between two datetimes.
		$date1 = new DateTime($date1);
		$date2 = new DateTime($date2);
		$interval = $date2->getTimestamp() - $date1->getTimestamp();

        // Return an interval in seconds.
		return $interval;
	}

    /**
     * Make an date interval string (from seconds ago to days ago).
     *
     * @since 0.1.0
     * @var integer $seconds Date interval in seconds.
     * @return string Interval with text between two dates.
     */
    public function getDateIntervalString($seconds) {
        // Return just now for 0 seconds.
		if ($seconds === 0) {
			$string = 'Just now';
		}
        // Return second/seconds ago for less than 60 seconds.
        else if ($seconds < 60) {
            if ($seconds === 1) {
                $string = intval($seconds) . ' second ago';
            } else {
                $string = intval($seconds) . ' seconds ago';
            }
		}
        // Return minute/minutes ago for less than 3600 seconds.
        else if ($seconds < 3600) {
            if ($seconds >= 60 && $seconds < 120) {
                $string = intval($seconds / 60) . ' minute ago';
            } else {
                $string = intval($seconds / 60) . ' minutes ago';
            }
		}
        // Return hour/hours ago for less than 86400 seconds.
        else if ($seconds < 86400) {
            if ($seconds >= 3600 && $seconds < 7200) {
                $string = intval($seconds / 3600) . ' hour ago';
            } else {
                $string = intval($seconds / 3600) . ' hours ago';
            }
		}
        // Return day/days ago for more than 86400 seconds.
        else {
            if ($seconds < 172800) {
                $string = intval($seconds / 86400) . ' day ago';
            } else {
                $string = intval($seconds / 86400) . ' days ago';
            }
		}

        // Return interval string from matching if statement.
		return $string;
	}
}
