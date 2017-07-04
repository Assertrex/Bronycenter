<?php

/**
 * Class used for getting details about visitor IP address
 *
 * @since 0.1.0
 */
class GeoIP
{
    /**
     * Store an array with details about visitor IP from decoded JSON in constructor
     *
     * @since 0.1.0
     * @var array
     */
    public $details = null;

    /**
     * Get details about visitor IP and store it in an array
     *
     * @since 0.1.0
     * @var string $ip Visitor's IP address
     */
    public function __construct($ip)
	{
        // TODO Stop execution after 2 seconds if can't connect to API
		$json = file_get_contents('http://ip-api.com/json/' . $ip);
		$this->details = json_decode($json, true);
	}

    /**
     * Check if timezone is valid
     *
     * @since 0.1.0
     * @var string Timezone returned from API
     * @return boolean
     */
    private function isValidTimezone($timezone) {
        if (is_null($timezone)) {
            return false;
        }

		return in_array($timezone, timezone_identifiers_list());
	}

    /**
     * Get country code
     *
     * @since 0.1.0
     * @return string|null Two characters long country code or null on error
     */
    public function getCountryCode() {
		return $this->details['countryCode'] ?? null;
	}

    /**
     * Get timezone offset in minutes
     *
     * @since 0.1.0
     * @return int|null Timezone offset in minutes
     */
    public function getTimezoneOffset() {
		$timezone = $this->json['timezone'] ?? null;
		$offset = 0;

		// Get timestamp difference in minutes if timezone is valid
		if (!is_null($timezone) && $this->isValidTimezone($timezone)) {
			$usertime = new DateTimeZone($timezone);
			$usertime = new DateTime('now', $usertime);

			$offset = $usertime->getOffset() / 60;
		}

		return $offset;
	}
}
