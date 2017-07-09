<?php

/**
 * Class used for getting details about visitors IP addresses.
 *
 * @copyright 2017 BronyCenter
 * @author Assertrex <norbert.gotowczyc@gmail.com>
 * @since 0.1.0
 */
class GeoIP
{
    /**
     * Store an array with details about a visitor's IP address.
     *
     * @since 0.1.0
     * @var null|array
     */
    public $details = null;

    /**
     * Get details about visitor's IP and store it in an array.
     *
     * @since 0.1.0
     * @var string $ip Visitor's IP address.
     */
    public function __construct($ip)
	{
        // TODO Stop execution after 2 seconds if can't connect to API
		$json = file_get_contents('http://ip-api.com/json/' . $ip);
		$this->details = json_decode($json, true);
	}

    /**
     * Check if timezone from IP details is valid.
     *
     * @since 0.1.0
     * @var string Timezone returned from API.
     * @return boolean Result of a timezone validation.
     */
    private function isValidTimezone($timezone) {
        // Return false if timezone value is empty.
        if (is_null($timezone)) {
            return false;
        }

		return in_array($timezone, timezone_identifiers_list());
	}

    /**
     * Get country code from IP details.
     *
     * @since 0.1.0
     * @return null|string Two-characters long country code or null.
     */
    public function getCountryCode() {
		return $this->details['countryCode'] ?? null;
	}

    /**
     * Get timezone offset to UTC in minutes.
     *
     * @since 0.1.0
     * @return int|null Time offset in minutes.
     */
    public function getTimezoneOffset() {
		$timezone = $this->details['timezone'] ?? null;
		$offset = 0;

		// Get time difference in minutes if timezone is valid.
		if (!is_null($timezone) && $this->isValidTimezone($timezone)) {
			$usertime = new DateTimeZone($timezone);
			$usertime = new DateTime('now', $usertime);

			$offset = $usertime->getOffset() / 60;
		}

		return $offset;
	}
}
