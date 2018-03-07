<?php

/**
* Class used for getting details about visitors IP addresses
*
* @since Release 0.1.0
*/

namespace BronyCenter;

class GeoIP
{
    /**
     * Singleton instance of a current class
     *
     * @since Release 0.1.0
     */
    private static $instance = null;

    /**
     * Details about selected IP
     *
     * @since Release 0.1.0
     */
    private $details = null;

    /**
     * Does nothing yet... D:
     *
     * @since Release 0.1.0
     */
    public function __construct()
    {

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
            self::$instance = new GeoIP();
        }

        return self::$instance;
    }

    /**
     * Get details about selected IP address
     *
     * @since Release 0.1.0
     * @var string $ip IP address to be checked
     * @return array|boolean Details about selected IP address
     */
    public function getDetails($ip) {
        // Get details about selected IP address
        $json = file_get_contents('http://ip-api.com/json/' . $ip);

        // Decode received JSON
		$this->details = json_decode($json, true);

        // Check if IP details has been successfully fetched
        if ($this->details['status'] != 'success') {
            // Clear failed details about IP address
            $this->details = null;
            return false;
        }

        return true;
    }

    /**
     * Get two letters country code from an IP address
     *
     * @since Release 0.1.0
     * @return array|boolean Two letters country code
     */
    public function getCountryCode() {
        return $this->details['countryCode'] ?? null;
    }

    /**
     * Get timezone as IANA time zones format
     *
     * @since Release 0.1.0
     * @return array|boolean IANA's time zone format
     */
    public function getTimezone() {
        return $this->details['timezone'] ?? null;
    }
}
