<?php

// Used for creating and reading user's posts

namespace BronyCenter;

use DateTime;

class Post
{
    private static $instance = null;
    private $o_config = null;
    private $o_translation = null;
    private $user = null;
    private $database = null;
    private $flash = null;
    private $utilities = null;
    private $validator = null;
    private $statistics = null;

    public function __construct()
    {
        $this->o_config = Config::getInstance();
        $this->o_translation = Translation::getInstance();
        $this->user = User::getInstance();
        $this->database = Database::getInstance();
        $this->flash = Flash::getInstance();
        $this->utilities = Utilities::getInstance();
        $this->validator = Validator::getInstance();
        $this->statistics = Statistics::getInstance();
    }

    public static function getInstance($reset = false)
    {
        if (!self::$instance || $reset === true) {
            self::$instance = new Post();
        }

        return self::$instance;
    }

    public function isPostExisting(int $postID) : bool
    {
        $isPostExisting = $this->database->read(
            'p.id',
            'posts p',
            'WHERE p.id = ? AND p.status != 9',
            [$postID]
        );

        $isPostExisting = count($isPostExisting);

        if (!$isPostExisting) {
            return false;
        }

        return true;
    }

    /**
     * Validate and create a new post
     *
     * @since Release 0.1.0
     * @var string $postContent Post's content message
     * @var string $postType Post's type ID
     * @var integer|null $serverPostUserID ID of a user for which a server message is made for
     * @return integer ID of a created post
     */
    public function add($postContent, $postType, $serverPostUserID = null)
    {
        // Store common system values
        $currentDatetime = $this->utilities->getDatetime();

        // Validate content of a post
        if (!$this->validator->checkPostContent($postContent, $postType)) {
            return false;
        }

        // Insert post into database
        $postID = $this->database->create(
            'user_id, datetime, content, type',
            'posts',
            '',
            [$serverPostUserID ?? $_SESSION['account']['id'], $currentDatetime, $postContent, $postType]
        );

        // Return a post ID if post has been successfully added
        if (intval($postID) != 0) {
            // Add points to user statistics counters if it's not a server post
            if (intval($postType) == 1) {
                $this->statistics->userPostCreate();
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
            'user_id, content, edit_count, type, like_count, comment_count',
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
        $postModified = $this->database->update(
            'content, edit_count',
            'posts',
            'WHERE id = ?',
            [$postContent, $postDetails['edit_count'] + 1, $postID]
        );

        // Check if post has been successfully modified
        if (intval($postModified) != 0) {
            // Store previous version of a post
            $this->database->create(
                'post_id, user_id, ip, datetime, content, like_count, comment_count',
                'posts_edits',
                '',
                [$postID, $postDetails['user_id'], $currentIP, $currentDatetime, $postDetails['content'], $postDetails['like_count'], $postDetails['comment_count']]
            );

            return $postContent;
        } else {
            $this->flash->error('Post has not been edited because of an unknown error.');
            return false;
        }
    }

    public function doApprove(int $postID) : bool
    {
        if (!$this->isPostExisting($postID)) {
            return false;
        }

        if (!$this->user->isCurrentModerator()) {
            return false;
        }

        $postApproved = $this->database->update(
            'report_checked, status',
            'posts',
            'WHERE id = ?',
            [1, 0, $postID]
        );

        if (!intval($postApproved)) {
            return false;
        }

        return true;
    }

    /**
     * Remove an own post or someone's as an moderator
     *
     * @since Release 0.1.0
     * @var string $postID ID of a post to remove
     * @return boolean Result of a function
     */
    public function delete($postID)
    {
        // Check if post ID is valid
        if (empty(intval($postID))) {
            return false;
        }

        // Get details about post author
        $postDetails = $this->database->read(
            'p.id, p.user_id, u.account_type',
            'posts p',
            'INNER JOIN users u ON p.user_id = u.id WHERE p.id = ?',
            [intval($postID)]
        );

        // Check if post exists
        if (!count($postDetails)) {
            $this->flash->error('Post that you\'re trying to delete doesn\'t exist.');
            return false;
        }

        // Define required details
        $isCurrentAuthor = $_SESSION['account']['id'] == $postDetails[0]['user_id'];
        $isCurrentModerator = $this->user->isCurrentModerator();
        $currentDatetime = $this->utilities->getDatetime();
        $removeAsModerator = ($isCurrentModerator && !$isCurrentAuthor);

        // Check if user is allowed to delete a post
        if (!$isCurrentAuthor && !$isCurrentModerator) {
            $this->flash->error('You\'re not allowed to remove this post.');
            return false;
        }

        // Turn post into a removed state
        $postRemovedID = $this->database->update(
            'status, delete_moderator, delete_id, delete_datetime',
            'posts',
            'WHERE id = ?',
            [9, intval($removeAsModerator), $_SESSION['account']['id'], $currentDatetime, $postID]
        );

        // Check if post has been successfully removed
        if (empty(intval($postRemovedID))) {
            return false;
        }

        // Remove points from user statistics counters
        if ($removeAsModerator) {
            $this->statistics->moderatorPostDelete($postDetails[0]['user_id']);
        } else {
            $this->statistics->userPostDelete();
        }

        return intval($postRemovedID);

    }

    /**
     * Report a post that should be removed
     *
     * @since Release 0.1.0
     * @var integer $id ID of a reported post
     * @return boolean|array Returns true or an array of errors
     */
    public function doReport($id)
    {
        // Define an array for holding error messages
        $method_errors = [];

        // Allow post reporting only to the users without account restrictions
        if ($_SESSION['account']['standing'] != 0) {
            $method_errors[] = 'You\'re not allowed to report a post. Check your account standing in settings.';
            $this->flash->error($method_errors[0]);

            return $method_errors;
        }

        // Change post ID from string to the integer
        $id = intval($id);

        // Check if post ID is an valid integer (higher than 0)
        if (empty($id)) {
            $method_errors[] = 'Post could not be reported as it\'s ID number is not valid.';
            $this->flash->error($method_errors[0]);

            return $method_errors;
        }

        // Search for a selected post
        $postDetails = $this->database->read(
            'id, status, report_count',
            'posts',
            'WHERE id = ?',
            [$id],
            false
        );

        // Check if post have been found
        if (empty($postDetails)) {
            $method_errors[] = 'Post could not be reported because it\'s ID number does not exist.';
            $this->flash->error($method_errors[0]);

            return $method_errors;
        }

        // Check if post is still available
        if ($postDetails['status'] != 0) {
            $method_errors[] = 'Post could not be reported because it has been already suspended or removed.';
            $this->flash->error($method_errors[0]);

            return $method_errors;
        }

        // Search if user has already reported this post
        $post_reported = $this->database->read(
            'id',
            'posts_reported',
            'WHERE post_id = ? AND user_id = ?',
            [$id, $_SESSION['account']['id']],
            false
        );

        // Check if user has already reported this post
        if (!empty($post_reported)) {
            $method_errors[] = 'You can report a post only once.';
            $this->flash->error($method_errors[0]);

            return $method_errors;
        }

        // Report a post if all above conditions have been met
        $report_created = $this->database->create(
            'post_id, user_id, datetime',
            'posts_reported',
            '',
            [$id, $_SESSION['account']['id'], $this->utilities->getDatetime()]
        );

        // Check if post has been successfully reported
        if (empty($report_created)) {
            $method_errors[] = 'Post could not be reported due to an unknown error.';
            $this->flash->error($method_errors[0]);

            return $method_errors;
        }

        // Add one to a report counter
        $this->database->update(
            'report_count',
            'posts',
            'WHERE id = ?',
            [++$postDetails['report_count'], $postDetails['id']]
        );

        // Return an successful notification
        $this->flash->success('Thank you for helping us with keeping ' . $this->o_config->getWebsiteTitle() . ' safe. Moderators will receive a notification to review a post that you have reported.');
        return true;
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
        $fetchColumns = 'p.id, p.user_id, p.datetime, p.content, p.like_count, p.comment_count, p.edit_count, p.type, l.user_id AS ownlike_id';

        // Use selected fetch mode
        switch ($array['fetchMode']) {
            // Fetch available posts from newest to oldest
            case 'getNewest':
                $posts = $this->database->read(
                    $fetchColumns,
                    'posts p',
                    'INNER JOIN users u ON p.user_id = u.id
                     LEFT JOIN (SELECT post_id, user_id FROM posts_likes WHERE user_id = ? AND active = 1) AS l ON p.id = l.post_id
                     WHERE u.account_type != 0 AND status != 9 ORDER BY id DESC LIMIT ? OFFSET ?',
                    [$_SESSION['account']['id'], $array['fetchAmount'] ?? 25, $array['fetchOffset'] ?? 0]
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
                     LEFT JOIN (SELECT id, post_id, user_id FROM posts_likes WHERE user_id = ? AND active = 1) AS l ON p.id = l.post_id
                     WHERE p.id > ? AND u.account_type != 0 AND status != 9 ORDER BY id DESC',
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
            $posts[$i]['is_current_user_author'] = $posts[$i]['user_id'] == $_SESSION['account']['id'];

            // Check if post contains any likes
            $posts[$i]['post_has_likes'] = $posts[$i]['like_count'] > 0;

            // Check if current user has liked a post
            $posts[$i]['current_user_liked'] = $posts[$i]['ownlike_id'] != 0;

            // Make a named interval of when a post has been published
            $posts[$i]['datetime_interval'] = $this->utilities->getDateIntervalString($this->utilities->countDateInterval($posts[$i]['datetime']));

            // Remember amount of post comments
            $posts[$i]['amount_comments'] = intval($posts[$i]['comment_count']);

            // Remember if post has been edited
            $posts[$i]['was_edited'] = false;

            if ($posts[$i]['edit_count'] != 0) {
                $posts[$i]['was_edited'] = true;

                if ($posts[$i]['edit_count'] > 1) {
                    $posts[$i]['edit_count_string'] = ' ' . $posts[$i]['edit_count'] . ' ' . $this->o_translation->getString('common', 'timesAsActionRepeated');
                } else {
                    $posts[$i]['edit_count_string'] = '';
                }
            }

            // Get array of post comments (limit to 2 newest) if any exists
            if ($posts[$i]['amount_comments'] > 0) {
                $posts[$i]['array_comments'] = $this->getComments($posts[$i]['id'], null, 2);
            }

            // Get details about post likes if any exists
            if ($posts[$i]['post_has_likes']) {
                // Get list of a post likes
                $posts[$i]['array_likes'] = $this->getLikes($posts[$i]['id']);

                // Get string about users that has liked a post
                $posts[$i]['string_likes'] = $this->getLikesString($posts[$i]['id'], $posts[$i]['array_likes'], $posts[$i]['current_user_liked']);
            }
        }

        // Return an array of posts
        if (!empty($posts)) {
            return $posts;
        } else {
            return [];
        }
    }

    public function countCreatedPosts(): int
    {
        $posts = $this->database->read(
            'count(id) AS amount',
            'posts',
            '',
            [],
            false
        )['amount'];

        return intval($posts) ?: 0;
    }

    public function countAvailablePosts(): int
    {
        $posts = $this->database->read(
            'count(p.id) AS amount',
            'posts p',
            'INNER JOIN users u ON p.user_id = u.id ' .
                'WHERE u.account_type != 0 AND p.status = 0',
            [],
            false
        )['amount'];

        return intval($posts) ?: 0;
    }

    public function countAllReportedPosts(): int
    {
        $posts = $this->database->read(
            'count(id) AS amount',
            'posts',
            'WHERE report_count != 0',
            [],
            false
        )['amount'];

        return intval($posts) ?: 0;
    }

    public function countReportedPosts(): int
    {
        $posts = $this->database->read(
            'count(id) AS amount',
            'posts',
            'WHERE report_count != 0 AND report_checked = 0 AND status != 9',
            [],
            false
        )['amount'];

        return intval($posts) ?: 0;
    }

    public function getReportedPosts(int $amount) : array
    {
        $fetchColumns = 'p.id, p.user_id, p.datetime, p.content, p.like_count, p.comment_count, p.edit_count, p.report_count, p.type, p.status, u.display_name';

        $posts = $this->database->read(
            $fetchColumns,
            'posts p',
            'INNER JOIN users u ON p.user_id = u.id ' .
                'INNER JOIN posts_reported r ON r.post_id = p.id ' .
                'WHERE p.report_count != 0 AND p.report_checked = 0 AND p.status != 9 ' .
                'ORDER BY r.datetime LIMIT ?',
            [$amount]
        );

        return $posts ?: [];
    }

    public function getUsersThatReportedPost(int $postID) : array
    {
        $fetchColumns = 'u.id, u.display_name';

        $posts = $this->database->read(
            $fetchColumns,
            'posts_reported r',
            'INNER JOIN users u ON r.user_id = u.id ' .
                'WHERE r.post_id = ?',
            [$postID]
        );

        return $posts ?: [];
    }

    // Count created posts (Legacy method)
    public function getPostsAmount(string $fetchMode = 'available', array $fetchSettings = []) : int
    {
        switch ($fetchMode) {
            case 'available':
                $posts = $this->database->read(
                    'COUNT(p.id) AS posts',
                    'posts p',
                    'INNER JOIN users u ON p.user_id = u.id ' .
                        'WHERE u.account_type != 0 AND p.status = 0',
                    []
                )[0]['posts'];
                break;
            case 'available_lastest':
                $posts = $this->database->read(
                    'COUNT(p.id) AS posts',
                    'posts p',
                    'INNER JOIN users u ON p.user_id = u.id ' .
                        'WHERE u.account_type != 0 AND p.status = 0 AND p.id > ?',
                    [$fetchSettings['fetchFromID']]
                )[0]['posts'];
                break;
        }

        return intval($posts) ?: 0;
    }

    /**
    * Get an array with previous versions of a post
    *
    * @since Release 0.1.0
    * @var integer $postID ID of a post
    * @return array Array containing details about each version of a post
    */
    public function getEditHistory($postID)
    {
        // Define an array for holding error messages
        $method_errors = [];

        // Change post ID from string to the integer
        $postID = intval($postID);

        // Check if post ID is an valid integer (higher than 0)
        if (empty($postID)) {
            $method_errors[] = 'Post history can\'t be viewed because post ID number is not valid.';
            $this->flash->error($method_errors[0]);

            return false;
        }

        // Get an edit history for a selected post
        $edit_history = $this->database->read(
            'id, post_id, user_id, datetime, content, like_count, comment_count',
            'posts_edits',
            'WHERE post_id = ?',
            [$postID]
        );

        return $edit_history;
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
            'u.id',
            'posts_likes l',
            'INNER JOIN users u ON l.user_id = u.id WHERE l.post_id = ? AND l.active = 1',
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
    **/
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
            'pst.id, pst.user_id as post_author_id, pst.like_count, lik.id AS like_id, lik.user_id, lik.active',
            'posts pst',
            'LEFT JOIN (SELECT id, post_id, user_id, active FROM posts_likes WHERE post_id = ? AND user_id = ?) AS lik ON pst.id = lik.post_id WHERE pst.id = ?',
            [$postID, $_SESSION['account']['id'], $postID]
        );

        // Return error if post have not been found
        if (count($post) === 0) {
            return false;
        }

        // Remember if current user tries to like own post
        $isCurrentAuthor = $_SESSION['account']['id'] == $post[0]['post_author_id'];

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

            // Add points for user statistics counters
            $this->statistics->userPostLike();

            if (!$isCurrentAuthor) {
                $this->statistics->userPostLikeReceive($post[0]['post_author_id']);
            }
        } // if

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

            // Add points for user statistics counters
            $this->statistics->userPostLike();

            if (!$isCurrentAuthor) {
                $this->statistics->userPostLikeReceive($post[0]['post_author_id']);
            }
        } // else if

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

            // Remove points from user statistics counters
            $this->statistics->userPostUnlike();

            if (!$isCurrentAuthor) {
                $this->statistics->userPostLikeLose($post[0]['post_author_id']);
            }
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
        $loopAmount = 4;

        for ($i = 1; $i < $loopAmount; $i++) {
            // Stop the loop if no users has left
            if (empty($array[$i - 1])) {
                break;
            }

            // Skip current user and get one more person
            if ($array[$i - 1]['id'] != $_SESSION['account']['id']) {
                $randomUsers[] = $this->user->generateUserDetails($array[$i - 1]['id']);
            }
        }

        // Check if current user has liked a post
        if ($hasLiked == 'true') {
            if ($likesAmount === 1) {
                $likedString = $this->o_translation->getString('postslist', 'currentLikeThisPostSingle');
            } else {
                $likedString = $this->o_translation->getString('postslist', 'currentLikeThisPostMultiple');
            }

            // Check if there is only one like
            if ($likesAmount === 1)
                $string = ucfirst($this->o_translation->getString('common', 'you'));
            // Check if there are two likes
            else if ($likesAmount === 2)
                $string = ucfirst($this->o_translation->getString('common', 'you')) . ' ' . $this->o_translation->getString('common', 'and') . ' ' .
                          '<a href="profile.php?u=' . $randomUsers[0]['id'] . '" data-toggle="tooltip" data-html="true" title="' . $randomUsers[0]['tooltip'] . '">' . $this->utilities->doEscapeString($randomUsers[0]['display_name']) . '</a> ';
            // Check if there are three likes
            else if ($likesAmount === 3)
                $string = ucfirst($this->o_translation->getString('common', 'you')) . ', ' .
                          '<a href="profile.php?u=' . $randomUsers[0]['id'] . '" data-toggle="tooltip" data-html="true" title="' . $randomUsers[0]['tooltip'] . '">' . $this->utilities->doEscapeString($randomUsers[0]['display_name']) . '</a> ' .
                          $this->o_translation->getString('common', 'and') . ' ' .
                          '<a href="profile.php?u=' . $randomUsers[1]['id'] . '" data-toggle="tooltip" data-html="true" title="' . $randomUsers[1]['tooltip'] . '">' . $this->utilities->doEscapeString($randomUsers[1]['display_name']) . '</a> ';
            // Check if there are more than three likes
            else
                $string = ucfirst($this->o_translation->getString('common', 'you')) . ', ' .
                          '<a href="profile.php?u=' . $randomUsers[0]['id'] . '" data-toggle="tooltip" data-html="true" title="' . $randomUsers[0]['tooltip'] . '">' . $this->utilities->doEscapeString($randomUsers[0]['display_name']) . '</a> ' .
                          $this->o_translation->getString('common', 'and') . ' ' .
                          '<span class="btn-openmodal btn-showlikesmodal " data-postid="' . $postID . '" data-toggle="modal" data-target="#mainModal">' . ($likesAmount - 2) . ' ' . $this->o_translation->getString('postslist', 'otherPeople') . '</span>';
        } else {
            if ($likesAmount === 1) {
                $likedString = $this->o_translation->getString('postslist', 'otherLikeThisPostSingle');
            } else {
                $likedString = $this->o_translation->getString('postslist', 'otherLikeThisPostMultiple');
            }

            // Check if there is only one like
            if ($likesAmount === 1)
                $string = '<a href="profile.php?u=' . $randomUsers[0]['id'] . '" data-toggle="tooltip" data-html="true" title="' . $randomUsers[0]['tooltip'] . '">' . $this->utilities->doEscapeString($randomUsers[0]['display_name']) . '</a> ';
            // Check if there are two likes
            else if ($likesAmount === 2)
                $string = '<a href="profile.php?u=' . $randomUsers[0]['id'] . '" data-toggle="tooltip" data-html="true" title="' . $randomUsers[0]['tooltip'] . '">' . $this->utilities->doEscapeString($randomUsers[0]['display_name']) . '</a> ' .
                          $this->o_translation->getString('common', 'and') . ' ' .
                          '<a href="profile.php?u=' . $randomUsers[1]['id'] . '" data-toggle="tooltip" data-html="true" title="' . $randomUsers[1]['tooltip'] . '">' . $this->utilities->doEscapeString($randomUsers[1]['display_name']) . '</a> ';
            // Check if there are three likes
            else if ($likesAmount === 3)
                $string = '<a href="profile.php?u=' . $randomUsers[0]['id'] . '" data-toggle="tooltip" data-html="true" title="' . $randomUsers[0]['tooltip'] . '">' . $this->utilities->doEscapeString($randomUsers[0]['display_name']) . '</a>, ' .
                          '<a href="profile.php?u=' . $randomUsers[1]['id'] . '" data-toggle="tooltip" data-html="true" title="' . $randomUsers[1]['tooltip'] . '">' . $this->utilities->doEscapeString($randomUsers[1]['display_name']) . '</a> ' .
                          $this->o_translation->getString('common', 'and') . ' ' .
                          '<a href="profile.php?u=' . $randomUsers[2]['id'] . '" data-toggle="tooltip" data-html="true" title="' . $randomUsers[2]['tooltip'] . '">' . $this->utilities->doEscapeString($randomUsers[2]['display_name']) . '</a> ';
            // Check if there are more than three likes
            else
                $string = '<a href="profile.php?u=' . $randomUsers[0]['id'] . '" data-toggle="tooltip" data-html="true" title="' . $randomUsers[0]['tooltip'] . '">' . $this->utilities->doEscapeString($randomUsers[0]['display_name']) . '</a>, ' .
                          '<a href="profile.php?u=' . $randomUsers[1]['id'] . '" data-toggle="tooltip" data-html="true" title="' . $randomUsers[1]['tooltip'] . '">' . $this->utilities->doEscapeString($randomUsers[1]['display_name']) . '</a> ' .
                          $this->o_translation->getString('common', 'and') . ' ' .
                          '<span class="btn-openmodal btn-showlikesmodal " data-postid="' . $postID . '" data-toggle="modal" data-target="#mainModal">' . ($likesAmount - 2) . ' ' . $this->o_translation->getString('postslist', 'otherPeople') . '</span>';
        }

        return '<i class="fa fa-thumbs-o-up text-muted mr-1" aria-hidden="true"></i> <span>' . $string . ' ' . $likedString . '.</span>';
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
            'user_id AS post_author_id, comment_count',
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

        // Add points to user statistics counters
        $this->statistics->userPostComment();
        $this->statistics->userPostCommentReceive($post[0]['post_author_id']);

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
        // Set query for selected mode
        switch ($mode) {
            case 'first':
                $sql_additional = 'WHERE post_id = ? ORDER BY id DESC LIMIT ?';
                $sql_array = [$postID, $amount];
                break;
            case 'more':
                $sql_additional = 'WHERE post_id = ? AND id < ? ORDER BY id DESC LIMIT ?';
                $sql_array = [$postID, $lastCommentID, $amount];
                break;
            case 'send':
                $sql_additional = 'WHERE post_id = ? AND id > ? ORDER BY id DESC';
                $sql_array = [$postID, $lastCommentID];
                break;
            default:
                return false;
        }

        // Get array of comments for selected post
        $comments = $this->database->read(
            'id, user_id, datetime, content',
            'posts_comments',
            $sql_additional,
            $sql_array
        );

        // Reverse an array to display newest post on bottom
        $comments = array_reverse($comments);

        for ($i = 0; $i < count($comments); $i++) {
            $comments[$i]['content'] = $this->utilities->replaceURLsWithLinks($this->utilities->doEscapeString($comments[$i]['content']));
        }

        // Return array of selected post comments
        return $comments;
    }
}
