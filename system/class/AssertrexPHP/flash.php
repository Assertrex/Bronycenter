<?php

/**
* Manages session flash messages
*
* @since Release 0.1.0
*/

namespace AssertrexPHP;

class Flash
{
    /**
     * Singleton instance of a current class
     *
     * @since Release 0.1.0
     */
    private static $instance = null;

    /**
     * Check if instance of current class is existing and create and/or return it
     *
     * @since Release 0.1.0
     * @var boolean Set as true to reset class instance
     * @return object Instance of a current class
     */
    public static function getInstance($reset = false) {
        if (!self::$instance || $reset === true) {
            self::$instance = new Flash();
        }

        return self::$instance;
    }

    /** Function for getting flash messages
     *
     * @since Release 0.1.0
     */
    public function get()
    {
        // Get flash messages if any exists
        if (!empty($_SESSION['flash'])) {
            // Get session messages
            $messages = $_SESSION['flash'];

            // Clear all messages
            $this->clear();

            return $messages;
        }

        // Return empty array if no flash messages has been found
        return array();
    }

    /**
     * General function for creating flash messages
     *
     * @since Release 0.1.0
     * @var string $type Type of a message
     * @var string $class Name of an alert class
     * @var string $message Content of a message
     */
    private function create($type, $class, $message)
    {
        $_SESSION['flash'][] = [$type, $class, $message];

        return true;
    }

    /**
     * Function for creating success flash messages
     *
     * @since Release 0.1.0
     * @var string $message Content of a message
     */
    public function success($message) {
        $this->create('success', 'alert-success', $message);

        return true;
    }

    /**
     * Function for creating info flash messages
     *
     * @since Release 0.1.0
     * @var string $message Content of a message
     */
    public function info($message) {
        $this->create('info', 'alert-info', $message);

        return true;
    }

    /**
     * Function for creating warning flash messages
     *
     * @since Release 0.1.0
     * @var string $message Content of a message
     */
    public function warning($message) {
        $this->create('warning', 'alert-warning', $message);

        return true;
    }

    /**
     * Function for creating error flash messages
     *
     * @since Release 0.1.0
     * @var string $message Content of a message
     */
    public function error($message) {
        $this->create('error', 'alert-danger', $message);

        return true;
    }

    /**
     * Clear all session flash messages
     *
     * @since Release 0.1.0
     */
    public function clear()
    {
        $_SESSION['flash'] = [];

        return true;
    }
}
