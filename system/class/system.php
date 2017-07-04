<?php

/**
 * Class used for handling files, formatting dates etc.
 *
 * @since 0.1.0
 */
class System
{
    /**
     * Array of settings from configuration file
     *
     * @since 0.1.0
     * @var array Settings divided into sections
     */
    private $settings = null;

    /**
     * Enable debugging on testing environment
     *
     * @since 0.1.0
     * @var boolean
     */
    public $testing = false;

    public function __construct()
    {
        // Parse configuration file and store settings in a variable
        $this->settings = parse_ini_file(__DIR__ . '/../config/settings.ini', true);

        // Check if debugging should be enabled
        $this->testing = $this->settings['system']['testing'];

        // Set up timezone for PHP
        date_default_timezone_set($this->settings['system']['timezone']);
    }

    /**
     * Get selected settings section
     *
     * @since 0.1.0
     * @var array|null $section Selected settings section
     * @return array Settings from selected section
     */
    public function getSettings($section = null) {
        if (!is_null($section)) {
            return $this->settings[$section];
        }

        return $this->settings;
    }

    /**
     * Store a system message in a session
     *
     * @since 0.1.0
     * @var string $status Status of messagee (eg. success, error, warning)
     * @var string $message Description of an error
     * @return bool Return "true", if everything went fine
     */
    public function setMessage($status, $message) {
        $currentDatetime = $this->getDatetime();

        switch($status) {
            case 'success':
                $alertClass = 'alert-success';
                $alertTitle = 'Success!';
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

        // Insert system message into an array
        $_SESSION['messages'][] = ['status' => $status, 'message' => $message, 'datetime' => $currentDatetime, 'alert-class' => $alertClass, 'alert-title' => $alertTitle];

		return true;
    }

    /**
     * Get an array with system messages from session
     *
     * @since 0.1.0
     * @return array Contains system messages
     */
	public function getMessages()
	{
        // Store messages in a variable
        $messages = $_SESSION['messages'] ?? [];

        // // Return empty array if no messages has been found
        // if (empty($messages)) {
        //     return [];
        // }

		// // Sort array by message status
		// uasort($messages, function ($i, $j) {
		// 	$a = $i['status'];
		// 	$b = $j['status'];
		// 	if ($a == $b) return 0;
		// 	elseif ($a > $b) return 1;
		// 	else return -1;
		// });

		return $messages;
	}

    /**
     * Clear an array containing system messages
     *
     * @since 0.1.0
     * @return bool Return "true", if everything went fine
     */
	public function clearMessages()
	{
		$_SESSION['messages'] = [];

		return true;
	}

    /**
     * Get current datetime in MySQL format
     *
     * @since 0.1.0
     * @return string Datetime in MySQL format
     */
    public function getDatetime()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * Get visitor's IP address
     *
     * @since 0.1.0
     * @return string Visitor's IP address
     */
    public function getVisitorIP()
    {
        // TODO Check if there's better way to get the right IP (proxy, VPN)

        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Get visitor's user agent
     *
     * @since 0.1.0
     * @return string Visitor's user agent
     */
    public function getVisitorAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * Generate random hash with MD5
     *
     * @since 0.1.0
     * @var int $length Number of characters (32 is max)
     * @return string Visitor's user agent
     */
    public function getRandomHash($length)
	{
		return substr(md5(uniqid(rand(), true)), 0, $length);
	}

    /**
     * Count interval between two dates
     *
     * @since 0.1.0
     * @var string $date1 Older date
     * @var string|null $date2 Newer date (or null for current datetime)
     * @return int Interval between two dates in seconds
     */
    public function countDateInterval($date1, $date2 = null) {
		if (is_null($date2)) {
			$date2 = $this->getDatetime();
		}

		$date1 = new DateTime($date1);
		$date2 = new DateTime($date2);
		$interval = $date2->getTimestamp() - $date1->getTimestamp();

		return $interval;
	}

    /**
     * Make an date interval string from seconds to days ago
     *
     * @since 0.1.0
     * @var int $seconds Date interval in seconds
     * @return string Interval between two dates with additional text
     */
    public function getDateIntervalString($seconds) {
		if ($seconds === 0) {
			$string = 'Just now';
		} else if ($seconds < 60) {
            if ($seconds === 1) {
                $string = intval($seconds) . ' second ago';
            } else {
                $string = intval($seconds) . ' seconds ago';
            }
		} else if ($seconds < 3600) {
            if ($seconds >= 60 && $seconds < 120) {
                $string = intval($seconds / 60) . ' minute ago';
            } else {
                $string = intval($seconds / 60) . ' minutes ago';
            }
		} else if ($seconds < 86400) {
            if ($seconds >= 3600 && $seconds < 7200) {
                $string = intval($seconds / 3600) . ' hour ago';
            } else {
                $string = intval($seconds / 3600) . ' hours ago';
            }
		} else {
            if ($seconds >= 86400 && $seconds < 172800) {
                $string = intval($seconds / 86400) . ' day ago';
            } else {
                $string = intval($seconds / 86400) . ' days ago';
            }
		}

		return $string;
	}
}
