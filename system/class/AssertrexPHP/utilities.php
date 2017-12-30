<?php

/**
* Contains many commonly used methods
*
* @since Release 0.1.0
*/

namespace AssertrexPHP;

use DateTime;

class Utilities
{
    /**
     * Singleton instance of a current class
     *
     * @since Release 0.1.0
     */
    private static $instance = null;

    /**
     * Initialize required classes
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
            self::$instance = new Utilities();
        }

        return self::$instance;
    }

    /**
     * Get current datetime in MySQL format
     * Returns a datetime in a format: YYYY-MM-DD HH:MM:SS
     *
     * @since Release 0.1.0
     * @return string Datetime in MySQL format
     */
    public function getDatetime()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * Get visitor's IP address
     *
     * @since Release 0.1.0
     * @return string Visitor's IP address
     */
    public function getVisitorIP()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Get visitor's browser's user agent
     *
     * @since Release 0.1.0
     * @return string Visitor's browser's user agent
     */
    public function getVisitorAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * Generate random hash with MD5 and UniqID
     *
     * @since 0.1.0
     * @var int $length Amount of characters (32 is max)
     * @return string Hash with a requested amount of characters
     */
    public function getRandomHash($length)
	{
		return substr(md5(uniqid(rand(), true)), 0, $length);
	}

    /**
     * Redirect to selected page and stop script execution
     *
     * @since 0.1.0
     * @var string Redirect path
     * @return boolean
     */
    public function redirect($path)
	{
		header('Location: ' . $path);
        die();
	}

    /**
     * Escape not escaped user value from database
     *
     * @since 0.1.0
     * @var string $string Not escaped user value
     * @return string Escaped user value
     */
    public function doEscapeString($string)
	{
        // Escape HTML tags into HTML entities
        $string = htmlspecialchars($string);

        // Replace textarea new lines with HTML new lines
        $string = nl2br($string);

        // Return escaped string
		return $string;
	}

    /**
     * Count interval between two dates
     *
     * @since 0.1.0
     * @var string $date1 Older date/datetime
     * @var null|string $date2 Newer date/datetime (or null for current datetime)
     * @return integer Interval between two dates in seconds
     */
    public function countDateInterval($date1, $date2 = null) {
        // Get current datetime if second date has not been specified
		if (is_null($date2)) {
			$date2 = $this->getDatetime();
		}

        // Count interval between two datetimes
		$date1 = new DateTime($date1);
		$date2 = new DateTime($date2);
		$interval = $date2->getTimestamp() - $date1->getTimestamp();

        // Return interval in seconds
		return $interval;
	}

    /**
     * Make an date interval string (from seconds ago to days ago)
     *
     * @since 0.1.0
     * @var integer $seconds Date interval in seconds
     * @var string $lastFormat Name of the last form
     * @return string Interval with text between two dates
     */
    public function getDateIntervalString($seconds, $lastFormat = null) {
        // Return just now for 0 seconds
		if ($seconds === 0) {
			$string = 'Just now';
		}

        // Return second/seconds ago for less than 60 seconds
        else if ($seconds < 60) {
            if ($seconds === 1) {
                $string = intval($seconds) . ' second ago';
            } else {
                $string = intval($seconds) . ' seconds ago';
            }
		}

        // Return minute/minutes ago for less than 3600 seconds
        else if ($seconds < 3600 && $lastFormat != 'seconds') {
            if ($seconds >= 60 && $seconds < 120) {
                $string = intval($seconds / 60) . ' minute ago';
            } else {
                $string = intval($seconds / 60) . ' minutes ago';
            }
		}

        // Return hour/hours ago for less than 86400 seconds
        else if ($seconds < 86400 && $lastFormat != 'minutes') {
            if ($seconds >= 3600 && $seconds < 7200) {
                $string = intval($seconds / 3600) . ' hour ago';
            } else {
                $string = intval($seconds / 3600) . ' hours ago';
            }
		}

        // Return day/days ago for more than 86400 seconds
        else {
            if ($seconds < 172800 && $lastFormat != 'hours') {
                $string = intval($seconds / 86400) . ' day ago';
            } else {
                $string = intval($seconds / 86400) . ' days ago';
            }
		}

        // Return interval string from matching if statement
		return $string;
	}

    /**
     * Get a full country name from country code
     *
     * @since 0.1.0
     * @var string $code Country code
     * @return string Full country name
     */
    public function getCountryName($code) {
        // Check if country code format is valid
        if (strlen($code) != 2) {
            return null;
        }

        // Store countries country codes
        $codes = [
            'AF' => 'Afghanistan',
            'AX' => 'Aland Islands',
            'AL' => 'Albania',
            'DZ' => 'Algeria',
            'AS' => 'American Samoa',
            'AD' => 'Andorra',
            'AO' => 'Angola',
            'AI' => 'Anguilla',
            'AQ' => 'Antarctica',
            'AG' => 'Antigua And Barbuda',
            'AR' => 'Argentina',
            'AM' => 'Armenia',
            'AW' => 'Aruba',
            'AU' => 'Australia',
            'AT' => 'Austria',
            'AZ' => 'Azerbaijan',
            'BS' => 'Bahamas',
            'BH' => 'Bahrain',
            'BD' => 'Bangladesh',
            'BB' => 'Barbados',
            'BY' => 'Belarus',
            'BE' => 'Belgium',
            'BZ' => 'Belize',
            'BJ' => 'Benin',
            'BM' => 'Bermuda',
            'BT' => 'Bhutan',
            'BO' => 'Bolivia',
            'BA' => 'Bosnia And Herzegovina',
            'BW' => 'Botswana',
            'BV' => 'Bouvet Island',
            'BR' => 'Brazil',
            'IO' => 'British Indian Ocean Territory',
            'BN' => 'Brunei Darussalam',
            'BG' => 'Bulgaria',
            'BF' => 'Burkina Faso',
            'BI' => 'Burundi',
            'KH' => 'Cambodia',
            'CM' => 'Cameroon',
            'CA' => 'Canada',
            'CV' => 'Cape Verde',
            'KY' => 'Cayman Islands',
            'CF' => 'Central African Republic',
            'TD' => 'Chad',
            'CL' => 'Chile',
            'CN' => 'China',
            'CX' => 'Christmas Island',
            'CC' => 'Cocos (Keeling) Islands',
            'CO' => 'Colombia',
            'KM' => 'Comoros',
            'CG' => 'Congo',
            'CD' => 'Congo, Democratic Republic',
            'CK' => 'Cook Islands',
            'CR' => 'Costa Rica',
            'CI' => 'Cote D\'Ivoire',
            'HR' => 'Croatia',
            'CU' => 'Cuba',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic',
            'DK' => 'Denmark',
            'DJ' => 'Djibouti',
            'DM' => 'Dominica',
            'DO' => 'Dominican Republic',
            'EC' => 'Ecuador',
            'EG' => 'Egypt',
            'SV' => 'El Salvador',
            'GQ' => 'Equatorial Guinea',
            'ER' => 'Eritrea',
            'EE' => 'Estonia',
            'ET' => 'Ethiopia',
            'FK' => 'Falkland Islands (Malvinas)',
            'FO' => 'Faroe Islands',
            'FJ' => 'Fiji',
            'FI' => 'Finland',
            'FR' => 'France',
            'GF' => 'French Guiana',
            'PF' => 'French Polynesia',
            'TF' => 'French Southern Territories',
            'GA' => 'Gabon',
            'GM' => 'Gambia',
            'GE' => 'Georgia',
            'DE' => 'Germany',
            'GH' => 'Ghana',
            'GI' => 'Gibraltar',
            'GR' => 'Greece',
            'GL' => 'Greenland',
            'GD' => 'Grenada',
            'GP' => 'Guadeloupe',
            'GU' => 'Guam',
            'GT' => 'Guatemala',
            'GG' => 'Guernsey',
            'GN' => 'Guinea',
            'GW' => 'Guinea-Bissau',
            'GY' => 'Guyana',
            'HT' => 'Haiti',
            'HM' => 'Heard Island & Mcdonald Islands',
            'VA' => 'Holy See (Vatican City State)',
            'HN' => 'Honduras',
            'HK' => 'Hong Kong',
            'HU' => 'Hungary',
            'IS' => 'Iceland',
            'IN' => 'India',
            'ID' => 'Indonesia',
            'IR' => 'Iran, Islamic Republic Of',
            'IQ' => 'Iraq',
            'IE' => 'Ireland',
            'IM' => 'Isle Of Man',
            'IL' => 'Israel',
            'IT' => 'Italy',
            'JM' => 'Jamaica',
            'JP' => 'Japan',
            'JE' => 'Jersey',
            'JO' => 'Jordan',
            'KZ' => 'Kazakhstan',
            'KE' => 'Kenya',
            'KI' => 'Kiribati',
            'KR' => 'Korea',
            'KW' => 'Kuwait',
            'KG' => 'Kyrgyzstan',
            'LA' => 'Lao People\'s Democratic Republic',
            'LV' => 'Latvia',
            'LB' => 'Lebanon',
            'LS' => 'Lesotho',
            'LR' => 'Liberia',
            'LY' => 'Libyan Arab Jamahiriya',
            'LI' => 'Liechtenstein',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'MO' => 'Macao',
            'MK' => 'Macedonia',
            'MG' => 'Madagascar',
            'MW' => 'Malawi',
            'MY' => 'Malaysia',
            'MV' => 'Maldives',
            'ML' => 'Mali',
            'MT' => 'Malta',
            'MH' => 'Marshall Islands',
            'MQ' => 'Martinique',
            'MR' => 'Mauritania',
            'MU' => 'Mauritius',
            'YT' => 'Mayotte',
            'MX' => 'Mexico',
            'FM' => 'Micronesia, Federated States Of',
            'MD' => 'Moldova',
            'MC' => 'Monaco',
            'MN' => 'Mongolia',
            'ME' => 'Montenegro',
            'MS' => 'Montserrat',
            'MA' => 'Morocco',
            'MZ' => 'Mozambique',
            'MM' => 'Myanmar',
            'NA' => 'Namibia',
            'NR' => 'Nauru',
            'NP' => 'Nepal',
            'NL' => 'Netherlands',
            'AN' => 'Netherlands Antilles',
            'NC' => 'New Caledonia',
            'NZ' => 'New Zealand',
            'NI' => 'Nicaragua',
            'NE' => 'Niger',
            'NG' => 'Nigeria',
            'NU' => 'Niue',
            'NF' => 'Norfolk Island',
            'MP' => 'Northern Mariana Islands',
            'NO' => 'Norway',
            'OM' => 'Oman',
            'PK' => 'Pakistan',
            'PW' => 'Palau',
            'PS' => 'Palestinian Territory, Occupied',
            'PA' => 'Panama',
            'PG' => 'Papua New Guinea',
            'PY' => 'Paraguay',
            'PE' => 'Peru',
            'PH' => 'Philippines',
            'PN' => 'Pitcairn',
            'PL' => 'Poland',
            'PT' => 'Portugal',
            'PR' => 'Puerto Rico',
            'QA' => 'Qatar',
            'RE' => 'Reunion',
            'RO' => 'Romania',
            'RU' => 'Russian Federation',
            'RW' => 'Rwanda',
            'BL' => 'Saint Barthelemy',
            'SH' => 'Saint Helena',
            'KN' => 'Saint Kitts And Nevis',
            'LC' => 'Saint Lucia',
            'MF' => 'Saint Martin',
            'PM' => 'Saint Pierre And Miquelon',
            'VC' => 'Saint Vincent And Grenadines',
            'WS' => 'Samoa',
            'SM' => 'San Marino',
            'ST' => 'Sao Tome And Principe',
            'SA' => 'Saudi Arabia',
            'SN' => 'Senegal',
            'RS' => 'Serbia',
            'SC' => 'Seychelles',
            'SL' => 'Sierra Leone',
            'SG' => 'Singapore',
            'SK' => 'Slovakia',
            'SI' => 'Slovenia',
            'SB' => 'Solomon Islands',
            'SO' => 'Somalia',
            'ZA' => 'South Africa',
            'GS' => 'South Georgia And Sandwich Isl.',
            'ES' => 'Spain',
            'LK' => 'Sri Lanka',
            'SD' => 'Sudan',
            'SR' => 'Suriname',
            'SJ' => 'Svalbard And Jan Mayen',
            'SZ' => 'Swaziland',
            'SE' => 'Sweden',
            'CH' => 'Switzerland',
            'SY' => 'Syrian Arab Republic',
            'TW' => 'Taiwan',
            'TJ' => 'Tajikistan',
            'TZ' => 'Tanzania',
            'TH' => 'Thailand',
            'TL' => 'Timor-Leste',
            'TG' => 'Togo',
            'TK' => 'Tokelau',
            'TO' => 'Tonga',
            'TT' => 'Trinidad And Tobago',
            'TN' => 'Tunisia',
            'TR' => 'Turkey',
            'TM' => 'Turkmenistan',
            'TC' => 'Turks And Caicos Islands',
            'TV' => 'Tuvalu',
            'UG' => 'Uganda',
            'UA' => 'Ukraine',
            'AE' => 'United Arab Emirates',
            'GB' => 'United Kingdom',
            'US' => 'United States',
            'UM' => 'United States Outlying Islands',
            'UY' => 'Uruguay',
            'UZ' => 'Uzbekistan',
            'VU' => 'Vanuatu',
            'VE' => 'Venezuela',
            'VN' => 'Viet Nam',
            'VG' => 'Virgin Islands, British',
            'VI' => 'Virgin Islands, U.S.',
            'WF' => 'Wallis And Futuna',
            'EH' => 'Western Sahara',
            'YE' => 'Yemen',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe'
        ];

        // Return found country name
        return $codes[$code] ?? null;
    }
}
