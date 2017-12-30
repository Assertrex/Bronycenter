<?php

/**
* Used for counting and getting conters of users actions
*
* @since Release 0.1.0
*/

namespace BronyCenter;

use AssertrexPHP\Database;
use AssertrexPHP\Utilities;

class Statistics
{
    /**
     * Singleton instance of a current class
     *
     * @since Release 0.1.0
     */
    private static $instance = null;

    /**
     * Place for instance of a database class
     *
     * @since Release 0.1.0
     */
    private $database = null;

    /**
     * Place for instance of an utilities class
     *
     * @since Release 0.1.0
     */
    private $utilities = null;

    /**
     * Get instances of required classes
     *
     * @since Release 0.1.0
     */
    public function __construct()
    {
        $this->database = Database::getInstance();
        $this->utilities = Utilities::getInstance();
    }

    /**
     * Check if instance of current class is existing and create and/or return it
     *
     * @since Release 0.1.0
     * @var boolean Set as true to reset class instance
     * @return object Instance of a current class
     */
    public static function getInstance($reset = false)
    {
        if (!self::$instance || $reset === true) {
            self::$instance = new Statistics();
        }

        return self::$instance;
    }

    /**
     * Get user's statistics
     *
     * @since Release 0.1.0
     * @return array User's statistics
     */
    public function get()
    {
        $array = $this->database->read(
            'user_points, posts_created, posts_likes_given, posts_comments_given,' .
            'posts_deleted, posts_likes_received, posts_comments_received',
            'users_statistics',
            'WHERE user_id = ?',
            [$_SESSION['account']['id']],
            false
        );

        return $array;
    }

    /**
     * Count user's action
     *
     * @since Release 0.1.0
     * @var string $action Name of an action done by user
     * @var null|integer $amount Amount of actions done by user
     * @return string Current creations links
     */
    public function countAction($action, $amount = 1)
    {
        $availableFields = ['posts_created', 'posts_likes_given', 'posts_comments_given', 'posts_deleted', 'posts_likes_received', 'posts_comments_received'];
        if (!in_array($action, $availableFields)) {
            return false;
        }

        // Read current value of a field
        $currentValue = $this->database->read(
            "user_points, $action",
            'users_statistics',
            'WHERE user_id = ?',
            [$_SESSION['account']['id']],
            false
        );

        // TODO Track achievements here

        // Check how much user points is this action worth
        switch ($action) {
            case 'posts_created':
                $pointsValue = 5;
                break;
            case 'posts_likes_received':
            case 'posts_comments_received':
                $pointsValue = 2;
                break;
            case 'posts_likes_given':
            case 'posts_comments_given':
                $pointsValue = 1;
                break;
            case 'posts_deleted':
                $pointsValue = -5;
                break;
            default:
                $pointsValue = 0;
        }

        // Update value with a new amount
        $currentValue['user_points'] = $currentValue['user_points'] + $pointsValue * $amount;
        $currentValue[$action] = $currentValue[$action] + $amount;

        // Don't update a field with a negative integer as fields are of an unsigned type
        // Temporiary fix for a broken statistics system
        if ($currentValue['user_points'] < 0) {
            $currentValue['user_points'] = 0;
        }

        // Don't update a field with a negative integer as fields are of an unsigned type
        // Temporiary fix for a broken statistics system
        if ($currentValue[$action] < 0) {
            $currentValue[$action] = 0;
        }

        // Update field with new value
        $updatedID = $this->database->update(
            "user_points, $action",
            'users_statistics',
            'WHERE user_id = ?',
            [$currentValue['user_points'], $currentValue[$action], $_SESSION['account']['id']]
        );

        // Check if value has been updated successfully
        if (intval($updatedID) === 0) {
            return false;
        }

        return true;
    }
}
