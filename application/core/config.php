<?php

/**
* Used for parsing configuration file and reading it's content
*
* @since Release 0.1.0
*/

namespace BronyCenter;

class Config
{
    /**
     * Singleton instance of a current class
     *
     * @since Release 0.1.0
     */
    private static $instance = null;

    /**
     * Place for array of configuration file contents
     *
     * @since Release 0.1.0
     */
    private $configuration = null;

    /**
     * Parse a configuration file and store it's content in a property
     *
     * @since Release 0.1.0
     */
    public function __construct()
    {
        $this->configuration = parse_ini_file(__DIR__ . '/../../settings.ini', true);
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
            self::$instance = new Config();
        }

        return self::$instance;
    }

    /**
     * Return selected section of a configuration file
     *
     * @since Release 0.1.0
     * @var string $section Name of a selected section
     * @return boolean|array Content of existing array
     */
    public function getSection($section)
    {
        // Return content of a section if it exists
        if (array_key_exists($section, $this->configuration)) {
            return $this->configuration[$section];
        }

        return false;
    }

    /**
     * Return website version details
     *
     * @since Release 0.1.0
     * @return array Content of an array
     */
    public function getVersion()
    {
        return parse_ini_file(__DIR__ . '/../../version.ini', false);
    }
}
