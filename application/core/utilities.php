<?php

// Contains many commonly used methods

namespace BronyCenter;

use DateTime;

class Utilities
{
    private static $instance = null;
    private $o_config = null;
    private $o_translation = null;

    public function __construct()
    {
        $this->o_config = Config::getInstance();
        $this->o_translation = Translation::getInstance();
    }

    public static function getInstance($reset = false) {
        if (!self::$instance || $reset === true) {
            self::$instance = new Utilities();
        }

        return self::$instance;
    }

    public function getDatetime() : string
    {
        return date('Y-m-d H:i:s');
    }

    public function getVisitorIP() : string
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    public function getVisitorAgent() : string
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    public function getRandomHash(int $length = 32) : string
	{
		return substr(md5(uniqid(rand(), true)), 0, $length);
	}

    public function redirect(string $path = 'index.php')
	{
		header('Location: ' . $path);
        die();
	}

    public function doHashPassword(string $password) : string
    {
        $websiteSettings = $this->o_config->getSettings('system');

        // Hash a password, fallback to BCrypt if PHP version is older than 7.2
        if (version_compare(PHP_VERSION, '7.2.0') >= 0) {
            return password_hash($password, PASSWORD_ARGON2I, [
                'memory_cost' => $websiteSettings['argon2_memory_cost'],
                'time_cost' => $websiteSettings['argon2_time_cost'],
                'threads' => $websiteSettings['argon2_threads']
            ]);
        } else {
            return password_hash($password, PASSWORD_BCRYPT, [
                'cost' => $websiteSettings['bcrypt_cost']
            ]);
        }
    }

    public function doEscapeString(string $string, bool $linebreaks = true) : string
	{
        $string = htmlspecialchars($string);

        if ($linebreaks) {
            $string = preg_replace('/[\r\n][\r\n]+/', '<br /><br />', $string);
            $string = nl2br($string);
        }

		return $string;
	}

    public function countDateInterval(string $date1, string $date2 = '') : int
    {
		if (empty($date2)) {
			$date2 = $this->getDatetime();
		}

		$date1 = new DateTime($date1);
		$date2 = new DateTime($date2);
		$interval = $date2->getTimestamp() - $date1->getTimestamp();

		return $interval;
	}

    public function getDateIntervalString(int $seconds = 0, string $lastFormat = '') : string
    {
        $seconds = intval($seconds);

		if ($seconds === 0) {
			$string = $this->o_translation->getString('dates', 'justNow');
		} else if ($seconds < 60) {
            if ($seconds === 1) {
                $string = $seconds . ' ' . $this->o_translation->getString('dates', 'secondAgo');
            } else {
                $string = $seconds . ' ' . $this->o_translation->getString('dates', 'secondsAgo');
            }
		} else if ($seconds < 3600 && $lastFormat != 'seconds') {
            if ($seconds >= 60 && $seconds < 120) {
                $string = intval($seconds / 60) . ' ' . $this->o_translation->getString('dates', 'minuteAgo');
            } else {
                $string = intval($seconds / 60) . ' ' . $this->o_translation->getString('dates', 'minutesAgo');
            }
		} else if ($seconds < 86400 && $lastFormat != 'minutes') {
            if ($seconds >= 3600 && $seconds < 7200) {
                $string = intval($seconds / 3600) . ' ' . $this->o_translation->getString('dates', 'hourAgo');
            } else {
                $string = intval($seconds / 3600) . ' ' . $this->o_translation->getString('dates', 'hoursAgo');
            }
		} else {
            if ($seconds < 172800 && $lastFormat != 'hours') {
                $string = intval($seconds / 86400) . ' ' . $this->o_translation->getString('dates', 'dayAgo');
            } else {
                $string = intval($seconds / 86400) . ' ' . $this->o_translation->getString('dates', 'daysAgo');
            }
		}

		return $string;
	}

    public function generateUserBadges(array $details, string $class = 'badge badge', string $style = '') : array
    {
        $badges = [];

        if ($details['is_online']) {
            $badges['is_online_badge'] = '<span class="' . $class . '-success" style="' . $style . '">' . $this->o_translation->getString('common', 'online') . '</span>';
        } else {
            $badges['is_online_badge'] = '<span class="' . $class . '-secondary" style="' . $style . '">' . $this->o_translation->getString('common', 'offline') . '</span>';
        }

        switch ($details['account_type']) {
            case '9':
                $badges['account_type_badge'] = '<span class="' . $class . '-danger" style="' . $style . '">' . $this->o_translation->getString('common', 'admin') . '</span>';
                break;
            case '8':
                $badges['account_type_badge'] = '<span class="' . $class . '-purple" style="' . $style . '">' . $this->o_translation->getString('common', 'mod') . '</span>';
                break;
            case '1':
                $badges['account_type_badge'] = '<span class="' . $class . '-primary" style="' . $style . '">' . $this->o_translation->getString('common', 'member') . '</span>';
                break;
            case '0':
                $badges['account_type_badge'] = '<span class="' . $class . '-secondary" style="' . $style . '">' . $this->o_translation->getString('common', 'unverified') . '</span>';
                break;
            default:
                $badges['account_type_badge'] = '<span class="' . $class . '-dark" style="' . $style . '">???</span>';
        }

        switch ($details['account_standing']) {
            case '1':
                $badges['account_standing_badge'] = '<span class="' . $class . '-warning" style="' . $style . '">Muted</span>';
                break;
            case '2':
                $badges['account_standing_badge'] = '<span class="' . $class . '-danger" style="' . $style . '">Banned</span>';
                break;
            case '3':
                $badges['account_standing_badge'] = '<span class="' . $class . '-muted" style="' . $style . '">Hidden</span>';
                break;
            case '4':
                $badges['account_standing_badge'] = '<span class="' . $class . '-muted" style="' . $style . '">Deleted</span>';
                break;
        }

        return $badges;
    }

    public function getCountryName(string $code = '') : string
    {
        if (strlen($code) != 2) {
            return '';
        }

        $codes = [
            'AF' => 'Afghanistan', 'AX' => 'Aland Islands', 'AL' => 'Albania', 'DZ' => 'Algeria', 'AS' => 'American Samoa', 'AD' => 'Andorra', 'AO' => 'Angola', 'AI' => 'Anguilla', 'AQ' => 'Antarctica', 'AG' => 'Antigua And Barbuda', 'AR' => 'Argentina', 'AM' => 'Armenia', 'AW' => 'Aruba', 'AU' => 'Australia', 'AT' => 'Austria', 'AZ' => 'Azerbaijan', 'BS' => 'Bahamas', 'BH' => 'Bahrain', 'BD' => 'Bangladesh', 'BB' => 'Barbados', 'BY' => 'Belarus', 'BE' => 'Belgium', 'BZ' => 'Belize', 'BJ' => 'Benin', 'BM' => 'Bermuda', 'BT' => 'Bhutan', 'BO' => 'Bolivia', 'BA' => 'Bosnia And Herzegovina', 'BW' => 'Botswana', 'BV' => 'Bouvet Island', 'BR' => 'Brazil', 'IO' => 'British Indian Ocean Territory', 'BN' => 'Brunei Darussalam', 'BG' => 'Bulgaria', 'BF' => 'Burkina Faso', 'BI' => 'Burundi', 'KH' => 'Cambodia', 'CM' => 'Cameroon', 'CA' => 'Canada', 'CV' => 'Cape Verde', 'KY' => 'Cayman Islands', 'CF' => 'Central African Republic', 'TD' => 'Chad', 'CL' => 'Chile', 'CN' => 'China', 'CX' => 'Christmas Island', 'CC' => 'Cocos (Keeling) Islands', 'CO' => 'Colombia', 'KM' => 'Comoros', 'CG' => 'Congo', 'CD' => 'Congo, Democratic Republic', 'CK' => 'Cook Islands', 'CR' => 'Costa Rica', 'CI' => 'Cote D\'Ivoire', 'HR' => 'Croatia', 'CU' => 'Cuba', 'CY' => 'Cyprus', 'CZ' => 'Czech Republic', 'DK' => 'Denmark', 'DJ' => 'Djibouti', 'DM' => 'Dominica', 'DO' => 'Dominican Republic', 'EC' => 'Ecuador', 'EG' => 'Egypt', 'SV' => 'El Salvador', 'GQ' => 'Equatorial Guinea', 'ER' => 'Eritrea', 'EE' => 'Estonia', 'ET' => 'Ethiopia', 'FK' => 'Falkland Islands (Malvinas)', 'FO' => 'Faroe Islands', 'FJ' => 'Fiji', 'FI' => 'Finland', 'FR' => 'France', 'GF' => 'French Guiana', 'PF' => 'French Polynesia', 'TF' => 'French Southern Territories', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Georgia', 'DE' => 'Germany', 'GH' => 'Ghana', 'GI' => 'Gibraltar', 'GR' => 'Greece', 'GL' => 'Greenland', 'GD' => 'Grenada', 'GP' => 'Guadeloupe', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GG' => 'Guernsey', 'GN' => 'Guinea', 'GW' => 'Guinea-Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HM' => 'Heard Island & Mcdonald Islands', 'VA' => 'Holy See (Vatican City State)', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Hungary', 'IS' => 'Iceland', 'IN' => 'India', 'ID' => 'Indonesia', 'IR' => 'Iran, Islamic Republic Of', 'IQ' => 'Iraq', 'IE' => 'Ireland', 'IM' => 'Isle Of Man', 'IL' => 'Israel', 'IT' => 'Italy', 'JM' => 'Jamaica', 'JP' => 'Japan', 'JE' => 'Jersey', 'JO' => 'Jordan', 'KZ' => 'Kazakhstan', 'KE' => 'Kenya', 'KI' => 'Kiribati', 'KR' => 'Korea', 'KW' => 'Kuwait', 'KG' => 'Kyrgyzstan', 'LA' => 'Lao People\'s Democratic Republic', 'LV' => 'Latvia', 'LB' => 'Lebanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libyan Arab Jamahiriya', 'LI' => 'Liechtenstein', 'LT' => 'Lithuania', 'LU' => 'Luxembourg', 'MO' => 'Macao', 'MK' => 'Macedonia', 'MG' => 'Madagascar', 'MW' => 'Malawi', 'MY' => 'Malaysia', 'MV' => 'Maldives', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshall Islands', 'MQ' => 'Martinique', 'MR' => 'Mauritania', 'MU' => 'Mauritius', 'YT' => 'Mayotte', 'MX' => 'Mexico', 'FM' => 'Micronesia, Federated States Of', 'MD' => 'Moldova', 'MC' => 'Monaco', 'MN' => 'Mongolia', 'ME' => 'Montenegro', 'MS' => 'Montserrat', 'MA' => 'Morocco', 'MZ' => 'Mozambique', 'MM' => 'Myanmar', 'NA' => 'Namibia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Netherlands', 'AN' => 'Netherlands Antilles', 'NC' => 'New Caledonia', 'NZ' => 'New Zealand', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'NU' => 'Niue', 'NF' => 'Norfolk Island', 'MP' => 'Northern Mariana Islands', 'NO' => 'Norway', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestinian Territory, Occupied', 'PA' => 'Panama', 'PG' => 'Papua New Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Philippines', 'PN' => 'Pitcairn', 'PL' => 'Poland', 'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Qatar', 'RE' => 'Reunion', 'RO' => 'Romania', 'RU' => 'Russian Federation', 'RW' => 'Rwanda', 'BL' => 'Saint Barthelemy', 'SH' => 'Saint Helena', 'KN' => 'Saint Kitts And Nevis', 'LC' => 'Saint Lucia', 'MF' => 'Saint Martin', 'PM' => 'Saint Pierre And Miquelon', 'VC' => 'Saint Vincent And Grenadines', 'WS' => 'Samoa', 'SM' => 'San Marino', 'ST' => 'Sao Tome And Principe', 'SA' => 'Saudi Arabia', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seychelles', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SK' => 'Slovakia', 'SI' => 'Slovenia', 'SB' => 'Solomon Islands', 'SO' => 'Somalia', 'ZA' => 'South Africa', 'GS' => 'South Georgia And Sandwich Isl.', 'ES' => 'Spain', 'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Suriname', 'SJ' => 'Svalbard And Jan Mayen', 'SZ' => 'Swaziland', 'SE' => 'Sweden', 'CH' => 'Switzerland', 'SY' => 'Syrian Arab Republic', 'TW' => 'Taiwan', 'TJ' => 'Tajikistan', 'TZ' => 'Tanzania', 'TH' => 'Thailand', 'TL' => 'Timor-Leste', 'TG' => 'Togo', 'TK' => 'Tokelau', 'TO' => 'Tonga', 'TT' => 'Trinidad And Tobago', 'TN' => 'Tunisia', 'TR' => 'Turkey', 'TM' => 'Turkmenistan', 'TC' => 'Turks And Caicos Islands', 'TV' => 'Tuvalu', 'UG' => 'Uganda', 'UA' => 'Ukraine', 'AE' => 'United Arab Emirates', 'GB' => 'United Kingdom', 'US' => 'United States', 'UM' => 'United States Outlying Islands', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistan', 'VU' => 'Vanuatu', 'VE' => 'Venezuela', 'VN' => 'Viet Nam', 'VG' => 'Virgin Islands, British', 'VI' => 'Virgin Islands, U.S.', 'WF' => 'Wallis And Futuna', 'EH' => 'Western Sahara', 'YE' => 'Yemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe'
        ];

        return $codes[$code] ?? '';
    }
}
