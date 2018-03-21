<?php

/**
 * Used for counting and getting conters of users actions
 *
 * @since Release 0.1.0
**/

namespace BronyCenter;

class Statistics
{
    /**
     * Singleton instance of a current class
     *
     * @since Release 0.1.0
    **/
    private static $instance = null;

    /**
     * Place for instance of a database class
     *
     * @since Release 0.1.0
    **/
    private $database = null;

    /**
     * Place for instance of an utilities class
     *
     * @since Release 0.1.0
    **/
    private $utilities = null;

    /**
     * Get instances of required classes
     *
     * @since Release 0.1.0
    **/
    public function __construct()
    {
        $this->database = Database::getInstance();
        $this->utilities = Utilities::getInstance();
    }

    /**
     * Check if instance of current class is existing and create and/or return it
     *
     * @since Release 0.1.0
     * @var boolean $reset Set as true to reset class instance
     * @return object Instance of a current class
    **/
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
     * @var integer|null $id Optional ID of a user for which statistics will be generated
     * @return array|boolean User's statistics
    **/
    public function get($id = null)
    {
        // Get an ID of a user for which statistics will be fetched
        if (empty(intval($id))) {
            $id = $_SESSION['account']['id'];
        }

        // Get user's statistics from a database
        $array = $this->database->read(
            'user_points, posts_created, posts_removed, posts_removed_mod,' .
            'posts_likes_given, posts_comments_given, posts_likes_received,' .
            'posts_comments_received',
            'users_statistics',
            'WHERE user_id = ?',
            [intval($id)],
            false
        );

        // Check if user statistics have been found
        if (empty($array)) {
            return false;
        }

        return $array;
    }

    /**
     * Count user's action points
     *
     * @since Release 0.1.0
     * @var integer $userID ID of a user that should receive or lose points
     * @var integer $amount Amount of actions to count
     * @var integer $value Points value of an action
     * @var string $column Name of a database column used by this action
     * @return boolean Result of this method
    **/
    private function countAction($userID, $amount, $value, $column)
    {
        // Read current value of a field
        $currentValues = $this->database->read(
            "user_points, $column",
            'users_statistics',
            'WHERE user_id = ?',
            [$userID],
            false
        );

        // Update user points values
        $currentValues['user_points'] = $currentValues['user_points'] + $value;
        $currentValues[$column] = $currentValues[$column] + $amount;

        // Update fields with new values
        $updatedID = $this->database->update(
            "user_points, $column",
            'users_statistics',
            'WHERE user_id = ?',
            [$currentValues['user_points'], $currentValues[$column], $userID]
        );

        // Check if values have been updated successfully
        if (empty(intval($updatedID))) {
            return false;
        }

        return true;
    }

    /**
     * Count an action when user has created a post
     *
     * @since Release 0.1.0
     * @return boolean Result of this method
    **/
    public function userPostCreate()
    {
        // Define action details
        $userID = intval($_SESSION['account']['id']);
        $amount = 1;
        $value = 10;
        $column = 'posts_created';

        // Pass details into a main method
        if ($this->countAction($userID, $amount, $value, $column)) {
            return true;
        }

        return false;
    }

    /**
     * Count an action when user has deleted a post
     *
     * @since Release 0.1.0
     * @return boolean Result of this method
    **/
    public function userPostDelete()
    {
        // Define action details
        $userID = intval($_SESSION['account']['id']);
        $amount = 1;
        $value = -10;
        $column = 'posts_removed';

        // Pass details into a main method
        if ($this->countAction($userID, $amount, $value, $column)) {
            return true;
        }

        return false;
    }

    /**
     * Count an action when moderator has deleted another user's post
     *
     * @since Release 0.1.0
     * @var integer $userID ID of a user that should lose points
     * @return boolean Result of this method
    **/
    public function moderatorPostDelete($userID = 0)
    {
        // Check if user ID has been defined
        if (empty(intval($userID))) {
            return false;
        }

        // Define action details
        $userID = intval($userID);
        $amount = 1;
        $value = -10;
        $column = 'posts_removed_mod';

        // Pass details into a main method
        if ($this->countAction($userID, $amount, $value, $column)) {
            return true;
        }

        return false;
    }

    /**
     * Count an action when user has liked a post
     *
     * @since Release 0.1.0
     * @return boolean Result of this method
    **/
    public function userPostLike()
    {
        // Define action details
        $userID = intval($_SESSION['account']['id']);
        $amount = 1;
        $value = 1;
        $column = 'posts_likes_given';

        // Pass details into a main method
        if ($this->countAction($userID, $amount, $value, $column)) {
            return true;
        }

        return false;
    }

    /**
     * Count an action when user has unliked a post
     *
     * @since Release 0.1.0
     * @return boolean Result of this method
    **/
    public function userPostUnlike()
    {
        // Define action details
        $userID = intval($_SESSION['account']['id']);
        $amount = -1;
        $value = -1;
        $column = 'posts_likes_given';

        // Pass details into a main method
        if ($this->countAction($userID, $amount, $value, $column)) {
            return true;
        }

        return false;
    }

    /**
     * Count an action when user receives a like on own post
     *
     * @since Release 0.1.0
     * @var integer $userID ID of a user that should receive points
     * @return boolean Result of this method
    **/
    public function userPostLikeReceive($userID = 0)
    {
        // Define action details
        $userID = intval($userID);
        $amount = 1;
        $value = 5;
        $column = 'posts_likes_received';

        // Pass details into a main method
        if ($this->countAction($userID, $amount, $value, $column)) {
            return true;
        }

        return false;
    }

    /**
     * Count an action when user lose a like on own post
     *
     * @since Release 0.1.0
     * @var integer $userID ID of a user that should lose points
     * @return boolean Result of this method
    **/
    public function userPostLikeLose($userID = 0)
    {
        // Define action details
        $userID = intval($userID);
        $amount = -1;
        $value = -5;
        $column = 'posts_likes_received';

        // Pass details into a main method
        if ($this->countAction($userID, $amount, $value, $column)) {
            return true;
        }

        return false;
    }

    /**
     * Count an action when user has commented a post
     *
     * @since Release 0.1.0
     * @return boolean Result of this method
    **/
    public function userPostComment()
    {
        // Define action details
        $userID = intval($_SESSION['account']['id']);
        $amount = 1;
        $value = 1;
        $column = 'posts_comments_given';

        // Pass details into a main method
        if ($this->countAction($userID, $amount, $value, $column)) {
            return true;
        }

        return false;
    }

    /**
     * Count an action when user has removed a post comment
     *
     * @since Release 0.1.0
     * @return boolean Result of this method
    **/
    public function userPostCommentRemove()
    {
        // Define action details
        $userID = intval($_SESSION['account']['id']);
        $amount = -1;
        $value = -1;
        $column = 'posts_comments_given';

        // Pass details into a main method
        if ($this->countAction($userID, $amount, $value, $column)) {
            return true;
        }

        return false;
    }

    /**
     * Count an action when moderator has removed a post comment of another user
     *
     * @since Release 0.1.0
     * @var integer $userID ID of a user that should lose points
     * @return boolean Result of this method
    **/
    public function moderatorPostCommentRemove($userID = 0)
    {
        // Check if user ID has been defined
        if (empty(intval($userID))) {
            return false;
        }

        // Define action details
        $userID = intval($userID);
        $amount = -1;
        $value = -1;
        $column = 'posts_comments_given'; // FIXME It was still given, so not really

        // Pass details into a main method
        if ($this->countAction($userID, $amount, $value, $column)) {
            return true;
        }

        return false;
    }

    /**
     * Count an action when user receives a comment on own post
     *
     * @since Release 0.1.0
     * @var integer $userID ID of a user that should receive points
     * @return boolean Result of this method
    **/
    public function userPostCommentReceive($userID = 0)
    {
        // Define action details
        $userID = intval($userID);
        $amount = 1;
        $value = 3;
        $column = 'posts_comments_received';

        // Pass details into a main method
        if ($this->countAction($userID, $amount, $value, $column)) {
            return true;
        }

        return false;
    }

    /**
     * Count an action when user lose a comment on own post
     *
     * @since Release 0.1.0
     * @var integer $userID ID of a user that should lose points
     * @return boolean Result of this method
    **/
    public function userPostCommentLose($userID = 0)
    {
        // Define action details
        $userID = intval($userID);
        $amount = -1;
        $value = -3;
        $column = 'posts_comments_received';

        // Pass details into a main method
        if ($this->countAction($userID, $amount, $value, $column)) {
            return true;
        }

        return false;
    }
}
