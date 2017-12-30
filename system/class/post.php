<?php

/**
* Used for creating and reading user's posts
*
* @since Release 0.1.0
*/

namespace BronyCenter;

use AssertrexPHP\Database;
use AssertrexPHP\Flash;
use AssertrexPHP\Utilities;
use AssertrexPHP\Validator;

class Post
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
     * Place for instance of a flash class
     *
     * @since Release 0.1.0
     */
    private $flash = null;

    /**
     * Place for instance of an utilities class
     *
     * @since Release 0.1.0
     */
    private $utilities = null;

    /**
     * Place for instance of a validator class
     *
     * @since Release 0.1.0
     */
    private $validator = null;

    /**
     * Place for instance of a statistics class
     *
     * @since Release 0.1.0
     */
    private $statistics = null;

    /**
     * Get instances of required classes
     *
     * @since Release 0.1.0
     */
    public function __construct()
    {
        $this->database = Database::getInstance();
        $this->flash = Flash::getInstance();
        $this->utilities = Utilities::getInstance();
        $this->validator = Validator::getInstance();
        $this->statistics = Statistics::getInstance();
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
            self::$instance = new Post();
        }

        return self::$instance;
    }

    /**
     * Validate and create a new post
     *
     * @since Release 0.1.0
     * @var string $postContent Post's content message
     * @var string $postType Post's type ID
     * @return integer ID of a created post
     */
    public function add($postContent, $postType)
    {
        // Store common system values
        $currentIP = $this->utilities->getVisitorIP();
        $currentDatetime = $this->utilities->getDatetime();

        // Validate content of a post
        if (!$this->validator->checkPostContent($postContent, $postType)) {
            return false;
        }

        // Insert post into database
        $postID = $this->database->create(
            'user_id, ip, datetime, content, type',
            'posts',
            '',
            [$_SESSION['account']['id'], $currentIP, $currentDatetime, $postContent, $postType]
        );

        // Return a post ID if post has been successfully added
        if (intval($postID) != 0) {
            // Add one to user's statistics posts created counter
            if ($postType == 1) {
                $this->statistics->countAction('posts_created', 1);
            }

            return $postID;
        } else {
            return false;
        }
    }

    /**
     * Validate and change content of a post
     *
     * @since Release 0.1.0
     * @var string $postID ID of an edited post
     * @var string $postContent Post's content message
     * @return boolean Status of a method
     */
    public function edit($postID, $postContent)
    {
        // Store common system values
        $currentIP = $this->utilities->getVisitorIP();
        $currentDatetime = $this->utilities->getDatetime();

        // Get few details about a post
        $postDetails = $this->database->read(
            'user_id, content, edit_count, type',
            'posts',
            'WHERE id = ?',
            [$postID],
            false
        );

        // Check if post exists
        if (empty($postDetails)) {
            $this->flash->error('Post that you\'re trying to edit doesn\'t exist.');
            return false;
        }

        // Don't edit if contents are the same
        if ($postContent == $postDetails['content']) {
            $this->flash->info('Post has not been modified as it\'s content is the same.');
            return false;
        }

        // Validate content of a post
        if (!$this->validator->checkPostContent($postContent, $postDetails['type'])) {
            return false;
        }

        // Check if user is authorized to edit this post
        if ($postDetails['user_id'] != $_SESSION['account']['id']) {
            $this->flash->error('You can only edit your own posts.');
            return false;
        }

        // Modify a post in a database
        $postID = $this->database->update(
            'content, edit_count',
            'posts',
            'WHERE id = ?',
            [$postContent, $postDetails['edit_count'] + 1, $postID]
        );

        // Check if post has been successfully modified
        if (intval($postID) != 0) {
            // Store previous version of a post
            $this->database->create(
                'post_id, user_id, ip, datetime, content',
                'posts_edits',
                '',
                [$postID, $postDetails['user_id'], $currentIP, $currentDatetime, $postDetails['content']]
            );

            return $postContent;
        } else {
            $this->flash->error('Post has not been edited because of an unknown error.');
            return false;
        }
    }

    /**
     * Remove an own post or someone's as an moderator
     *
     * @since Release 0.1.0
     * @var string $postID ID of a post to remove
     * @var string|null $reason Moderator's reason for removing a post
     * @return boolean Result of a function
     */
    public function delete($postID, $reason)
    {
        // Remove everything from a string except integer
        $postID = intval($postID);

        // Store common system values
        $currentIP = $this->utilities->getVisitorIP();
        $currentDatetime = $this->utilities->getDatetime();

        // Check if post ID is valid
        if (empty($postID)) {
            return false;
        }

        // Get details about post author
        $postDetails = $this->database->read(
            'p.id, p.user_id, u.account_type',
            'posts p',
            'INNER JOIN users u ON p.user_id = u.id WHERE p.id = ?',
            [$postID]
        );

        // Check if post exists
        if (!count($postDetails)) {
            $this->flash->error('Post that you\'re trying to delete doesn\'t exist.');
            return false;
        }

        // Check if user is allowed to delete a post
        if ($_SESSION['account']['id'] != $postDetails[0]['user_id'] && (
            $_SESSION['account']['type'] != 8 && $_SESSION['account']['type'] != 9)) {
            $this->flash->error('You\'re not allowed to remove this post.');
            return false;
        }

        // Check if post has been created by a moderator
        if ($_SESSION['account']['id'] != $postDetails[0]['user_id'] && (
            $postDetails[0]['account_type'] == 8 || $postDetails[0]['account_type'] == 9)) {
            $this->flash->error('You can\'t remove post created by a different moderator.');
            return false;
        }

        // Turn post into a removed state
        $postRemovedID = $this->database->update(
            'status, delete_id, delete_ip, delete_datetime, delete_reason',
            'posts',
            'WHERE id = ?',
            [9, $_SESSION['account']['id'], $currentIP, $currentDatetime, $reason ?? null, $postID]
        );

        // Check if post has been successfully removed
        if (!empty(intval($postRemovedID))) {
            // Add one to user's statistics posts deleted counter
            // FIXME Remove points from an post author if deleted by a moderator
            $this->statistics->countAction('posts_deleted', 1);
            return intval($postRemovedID);
        }

        // Return post deletion result
        return false;
    }

    /**
     * Get selected post/posts
     *
     * @since Release 0.1.0
     * @var array Search criteria
     * @return array Array with a post/posts
     */
    public function get($array)
    {
        $fetchColumns = 'p.id, p.user_id, p.datetime, p.content, p.like_count, p.comment_count, p.edit_count, p.type, u.display_name, u.username, u.last_online, u.country_code, u.avatar, u.account_type, d.birthdate, d.gender, l.user_id AS ownlike_id';

        // Use selected fetch mode
        switch ($array['fetchMode']) {
            // Fetch available posts from newest to oldest
            case 'getNewest':
                $posts = $this->database->read(
                    $fetchColumns,
                    'posts p',
                    'INNER JOIN users u ON p.user_id = u.id
                     INNER JOIN users_details d ON d.user_id = u.id
                     LEFT JOIN (SELECT id, post_id, user_id FROM posts_likes WHERE user_id = ? AND active = 1) AS l ON p.id = l.post_id
                     WHERE status != 9 ORDER BY id DESC LIMIT ? OFFSET ?',
                    [$_SESSION['account']['id'], $array['fetchAmount'] ?? 10, $array['fetchOffset'] ?? 0]
                );
                break;

            // Fetch all posts that appeared after last fetched
            case 'getLastest':
                // Stop executing if ID is invalid
                if (empty(intval($array['fetchFromID']))) {
                    return false;
                }

                $posts = $this->database->read(
                    $fetchColumns,
                    'posts p',
                    'INNER JOIN users u ON p.user_id = u.id
                     INNER JOIN users_details d ON d.user_id = u.id
                     LEFT JOIN (SELECT id, post_id, user_id FROM posts_likes WHERE user_id = ? AND active = 1) AS l ON p.id = l.post_id
                     WHERE p.id > ? AND status != 9 ORDER BY id DESC',
                    [$_SESSION['account']['id'], $array['fetchFromID']]
                );
                break;

            // Return empty array if fetch mode is not valid
            default:
                $posts = [];
        }

        // Modify and format post details
        for ($i = 0; $i < count($posts); $i++) {
            // Check if current user is an author of a post
            $posts[$i]['ownPost'] = $posts[$i]['user_id'] == $_SESSION['account']['id'];

            // Check if post contains any likes
            $posts[$i]['hasLikes'] = $posts[$i]['like_count'] > 0;

            // Check if current user has liked a post
            $posts[$i]['hasLiked'] = $posts[$i]['ownlike_id'] != 0;

            // Set user's avatar or get the default one if not existing
            $posts[$i]['avatar'] = '../media/avatars/' . ($posts[$i]['avatar'] ?? 'default') . '/minres.jpg';

            // Make a named interval of when a post has been published
            $posts[$i]['datetimeInterval'] = $this->utilities->getDateIntervalString($this->utilities->countDateInterval($posts[$i]['datetime']));

            // Store user badge depending on it's account type
            switch ($posts[$i]['account_type']) {
                case '9':
                    $posts[$i]['userBadge'] = '<span class="d-block badge badge-danger mt-2">Admin</span>';
                    break;
                case '8':
                    $posts[$i]['userBadge'] = '<span class="d-block badge badge-info mt-2">Mod</span>';
                    break;
                default:
                    $posts[$i]['userBadge'] = '';
            }
        }

        // Return an array of posts
        if (!empty($posts)) {
            return $posts;
        } else {
            return [];
        }
    }

    /**
     * Count created posts
     *
     * @since 0.1.0
     * @var string $fetchMode Mode of posts fetching
     * @var array $fetchSettings Settings for other fetch modes
     * @return string Amount of available posts
     */
    public function getPostsAmount($fetchMode = 'available', $fetchSettings = []) {
        switch ($fetchMode) {
            case 'available':
                $postsCount = $this->database->read(
                    'COUNT(*) AS posts',
                    'posts',
                    'WHERE status = 0',
                    []
                )[0]['posts'];
                break;
            case 'available_lastest':
                $postsCount = $this->database->read(
                    'COUNT(*) AS posts',
                    'posts',
                    'WHERE id > ? AND status = 0',
                    [$fetchSettings['fetchFromID']]
                )[0]['posts'];
                break;
        }

        return $postsCount;
    }

    /**
    * Get users that has liked a post
    *
    * @since Release 0.1.0
    * @var integer $postId ID of a post
    * @return array Array of users that have liked a post
    */
    public function getLikes($postID)
    {
        $postID = intval($postID);

        // Check if ID of a post is a valid integer
        if (empty($postID)) {
            return false;
        }

        // Get an array of users that has liked a post
        $likes = $this->database->read(
            'u.id, u.display_name, u.username, u.avatar',
            'posts_likes p',
            'INNER JOIN users u ON p.user_id = u.id WHERE p.post_id = ? AND p.active = 1',
            [$postID]
        );

        // Add more elements to an array if any like has been found
        for ($i = 0; $i < count($likes); $i++) {
            // Set user's avatar or get the default one if not existing
            $likes[$i]['avatar'] = $likes[$i]['avatar'] ?? 'default';
        }

        return $likes;
    }

    /**
    * Like or unlike (if already liked) the selected post
    *
    * @since Release 0.1.0
    * @var integer $postId ID of a post
    * @return boolean Result of a method
    */
    public function addLike($postID)
    {
        // Transform ID of a post into integer for security
        $postID = intval($postID);

        // Check if ID of a post is a valid integer
        if (empty(intval($postID))) {
            return false;
        }

        // Find current user's like
        $post = $this->database->read(
            'pst.id, pst.like_count, lik.id AS like_id, lik.user_id, lik.active',
            'posts pst',
            'LEFT JOIN (SELECT id, post_id, user_id, active FROM posts_likes WHERE post_id = ? AND user_id = ?) AS lik ON pst.id = lik.post_id WHERE pst.id = ?',
            [$postID, $_SESSION['account']['id'], $postID]
        );

       // Return error if post have not been found
       if (count($post) === 0) {
           return false;
       }

       // Add like if current user has not liked this post before
        if (is_null($post[0]['user_id'])) {
            // Add new like to the database
            $hasLiked = $this->database->create(
                'post_id, user_id',
                'posts_likes',
                '',
                [$postID, $_SESSION['account']['id']]
            );

            // Return error if current user couldn't like a post
            if (empty($hasLiked)) {
                return false;
            }

            // Add a like to post likes counter
            $this->database->update(
                'like_count',
                'posts',
                'WHERE id = ?',
                [$post[0]['like_count'] + 1, $postID]
            );

            // Add one to user's statistics posts likes given counter
            $this->statistics->countAction('posts_likes_given', 1);
        }

        // Add a like again if current user has unliked it before
        else if ($post[0]['active'] == false) {
            // Change the active status to true for the like in the database
            $this->database->update(
                'active',
                'posts_likes',
                'WHERE id = ?',
                [1, $post[0]['like_id']]
            );

            // Add one to post like counter
            $this->database->update(
                'like_count',
                'posts',
                'WHERE id = ?',
                [$post[0]['like_count'] + 1, $postID]
            );

            // Add one to user's statistics posts likes given counter
            $this->statistics->countAction('posts_likes_given', 1);
        }

        // Remove like if current user has already liked a post
        else {
            // Change the active status to false for the like in the database
            $this->database->update(
                'active',
                'posts_likes',
                'WHERE id = ?',
                [0, $post[0]['like_id']]
            );

            // Subtract one from post like counter
            $this->database->update(
                'like_count',
                'posts',
                'WHERE id = ?',
                [$post[0]['like_count'] - 1, $postID]
            );

            // Remove one from user's statistics posts likes given counter
            $this->statistics->countAction('posts_likes_given', -1);
        }

        return true;
    }

    /**
     * Get string showing users that has liked a post
     *
     * @since Release 0.1.0
     * @var integer $array ID of a post
     * @var null|array $array Array with likes from getLikes method
     * @var boolean $userLiked Boolean if current user has liked a post
     * @return null|string Text showing who has liked a post
     */
    public function getLikesString($postID, $array, $hasLiked)
    {
        // Store required variables
        $likesAmount = count($array);
        $randomUsers = [];

        // Check if post has any likes
        if ($likesAmount === 0) {
            return false;
        }

        // Get details about random users that have liked a post
        $loopAmount = 3;

        for ($i = 1; $i < $loopAmount; $i++) {
            // Stop the loop if no users has left
            if (empty($array[$i - 1])) {
                break;
            }

            // Skip current user and get one more person
            if ($array[$i - 1]['id'] != $_SESSION['account']['id']) {
                $randomUsers[] = $array[$i - 1];
            } else {
                $loopAmount = 4;
            }
        }

        // Check if current user has liked a post
        if ($hasLiked == 'true') {
            // Check if there is only one like
            if ($likesAmount === 1)
                $string = 'You like this post.';
            // Check if there are two likes
            else if ($likesAmount === 2)
                $string = 'You and ' .
                          '<a href="profile.php?u=' . $randomUsers[0]['id'] . '">' . htmlspecialchars($randomUsers[0]['display_name']) . '</a> ' .
                          'like this post.';
            // Check if there are three likes
            else if ($likesAmount === 3)
                $string = 'You, ' .
                          '<a href="profile.php?u=' . $randomUsers[0]['id'] . '">' . htmlspecialchars($randomUsers[0]['display_name']) . '</a> ' .
                          'and ' .
                          '<a href="profile.php?u=' . $randomUsers[1]['id'] . '">' . htmlspecialchars($randomUsers[1]['display_name']) . '</a> ' .
                          'like this post.';
            // Check if there are more than three likes
            else
                $string = 'You, ' .
                          '<a href="profile.php?u=' . $randomUsers[0]['id'] . '">' . htmlspecialchars($randomUsers[0]['display_name']) . '</a> ' .
                          'and ' .
                          '<span class="btn-openmodal btn-showlikesmodal " data-postid="' . $postID . '" data-toggle="modal" data-target="#mainModal">' . ($likesAmount - 2) . ' other ponies</span> like this post.';
        } else {
            // Check if there is only one like
            if ($likesAmount === 1)
                $string = '<a href="profile.php?u=' . $randomUsers[0]['id'] . '">' . htmlspecialchars($randomUsers[0]['display_name']) . '</a> ' .
                          'like this post.';
            // Check if there are two likes
            else if ($likesAmount === 2)
                $string = '<a href="profile.php?u=' . $randomUsers[0]['id'] . '">' . htmlspecialchars($randomUsers[0]['display_name']) . '</a> ' .
                          'and ' .
                          '<a href="profile.php?u=' . $randomUsers[1]['id'] . '">' . htmlspecialchars($randomUsers[1]['display_name']) . '</a> ' .
                          'like this post.';
            // Check if there are three likes
            else if ($likesAmount === 3)
                $string = '<a href="profile.php?u=' . $randomUsers[0]['id'] . '">' . htmlspecialchars($randomUsers[0]['display_name']) . '</a>, ' .
                          '<a href="profile.php?u=' . $randomUsers[1]['id'] . '">' . htmlspecialchars($randomUsers[1]['display_name']) . '</a> ' .
                          'and ' .
                          '<a href="profile.php?u=' . $randomUsers[2]['id'] . '">' . htmlspecialchars($randomUsers[2]['display_name']) . '</a> ' .
                          'like this post.';
            // Check if there are more than three likes
            else
                $string = '<a href="profile.php?u=' . $randomUsers[0]['id'] . '">' . htmlspecialchars($randomUsers[0]['display_name']) . '</a>, ' .
                          '<a href="profile.php?u=' . $randomUsers[1]['id'] . '">' . htmlspecialchars($randomUsers[1]['display_name']) . '</a> ' .
                          'and ' .
                          '<span class="btn-openmodal btn-showlikesmodal " data-postid="' . $postID . '" data-toggle="modal" data-target="#mainModal">' . ($likesAmount - 2) . ' other ponies</span> like this post.';
        }

        return '<i class="fa fa-thumbs-o-up text-muted mr-1" aria-hidden="true"></i> <span>' . $string . '</span>';
    }

    /**
     * Add a comment for a post
     *
     * @since Release 0.1.0
     * @var integer $postID ID of a post
     * @var string $content Comment content
     * @return boolean Status of this method
     */
    public function comment($postID, $content)
    {
        // Return false if comment contains more than 500 characters
        if (strlen($content) > 500) {
            return false;
        }

        // Store required variables
        $currentDatetime = $this->utilities->getDatetime();

        // Get a selected post
        $post = $this->database->read(
            'comment_count',
            'posts',
            'WHERE id = ?',
            [$postID]
        );

        // Check if post is existing
        if (count($post) != 1) {
            return false;
        }

        // Add new comment to the database
        $commentID = $this->database->create(
            'post_id, user_id, datetime, content',
            'posts_comments',
            '',
            [$postID, $_SESSION['account']['id'], $currentDatetime, $content]
        );

        // Update post's comments counter
        $this->database->update(
            'comment_count',
            'posts',
            'WHERE id = ?',
            [intval($post[0]['comment_count']) + 1, $postID]
        );

        // Add one to user's statistics posts comments given counter
        $this->statistics->countAction('posts_comments_given', 1);

        return $commentID;
    }

    /**
     * Get comments for a selected post
     *
     * @since Release 0.1.0
     * @var integer $postID ID of a post
     * @var integer $lastCommentID ID of an oldest fetched post
     * @var integer|null $amount Amount of comments to fetch
     * @var string $mode Mode of comments fetching (first, more, send)
     * @return array Array of selected post comments
     */
    public function getComments($postID, $lastCommentID, $amount, $mode = 'first')
    {
        // Get array of comments for selected post
        switch ($mode) {
            case 'first':
                $comments = $this->database->read(
                    'pcm.id, pcm.user_id, usr.display_name, usr.username, usr.avatar, pcm.datetime, pcm.content',
                    'posts_comments pcm',
                    'INNER JOIN users usr ON usr.id = pcm.user_id WHERE pcm.post_id = ? ORDER BY pcm.id DESC LIMIT ?',
                    [$postID, $amount]
                );
                break;
            case 'more':
                $comments = $this->database->read(
                    'pcm.id, pcm.user_id, usr.display_name, usr.username, usr.avatar, pcm.datetime, pcm.content',
                    'posts_comments pcm',
                    'INNER JOIN users usr ON usr.id = pcm.user_id WHERE pcm.post_id = ? AND pcm.id < ? ORDER BY pcm.id DESC LIMIT ?',
                    [$postID, $lastCommentID, $amount]
                );
                break;
            case 'send':
                $comments = $this->database->read(
                    'pcm.id, pcm.user_id, usr.display_name, usr.username, usr.avatar, pcm.datetime, pcm.content',
                    'posts_comments pcm',
                    'INNER JOIN users usr ON usr.id = pcm.user_id WHERE pcm.post_id = ? AND pcm.id > ? ORDER BY pcm.id DESC',
                    [$postID, $lastCommentID]
                );
                break;
            default:
                return false;
        }

        // Reverse an array to display newest post on bottom
        $comments = array_reverse($comments);

        // Return array of selected post comments
        return $comments;
    }
}
