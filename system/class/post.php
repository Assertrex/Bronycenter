<?php

/**
 * Class used for actions with user's posts
 *
 * @copyright 2017 BronyCenter
 * @author Assertrex <norbert.gotowczyc@gmail.com>
 * @since 0.1.0
 */
class Post
{
    /**
     * Object of a system class.
     *
     * @since 0.1.0
     * @var null|object
     */
    private $system = null;

    /**
     * Object of a database class.
     *
     * @since 0.1.0
     * @var null|object
     */
    private $database = null;

    /**
     * Object of a validate class.
     *
     * @since 0.1.0
     * @var null|object
     */
    private $validate = null;

    /**
     * @since 0.1.0
     * @var object $o_system Object of a system class.
     * @var object $o_database Object of a database class.
     * @var object $o_validate Object of a validate class.
     */
    public function __construct($o_system, $o_database, $o_validate)
    {
        // Store required classes objects in a properties.
        $this->system = $o_system;
        $this->database = $o_database;
        $this->validate = $o_validate;
    }

    /**
     * Get selected user's posts.
     * TODO Make method to require search parameters.
     * TODO Fix offset somehow when new posts have been added (probably get from last id).
     *
     * @since 0.1.0
     * @var integer $amount Amount of posts.
     * @return array Array of selected posts.
     */
	public function get($amount = 10)
	{
        // Get an array of matching posts.
		$posts = $this->database->read(
			'p.id, p.user_id, p.datetime, p.content, p.like_count, p.comment_count, p.type, u.display_name, u.last_online, u.country_code, u.avatar, u.account_type, d.birthdate, d.gender, l.user_id AS ownlike_id',
			'posts p',
			'INNER JOIN users u ON p.user_id = u.id
             INNER JOIN users_details d ON d.user_id = u.id
             LEFT JOIN (SELECT id, post_id, user_id FROM posts_likes WHERE user_id = ? AND active = 1) AS l ON p.id = l.post_id
             WHERE status != 9 ORDER BY id DESC LIMIT ?',
			[$_SESSION['account']['id'], $amount]
		);

        // Add more elements to an array if any post has been found.
        for ($i = 0; $i < count($posts); $i++) {
            // Remember if post contains any likes.
            if ($posts[$i]['like_count'] == 0) {
                $posts[$i]['any_likes'] = false;
            } else {
                $posts[$i]['any_likes'] = true;
            }

            // Remember if user has liked a post.
            if ($posts[$i]['ownlike_id'] == null) {
                $posts[$i]['ownlike'] = false;
            } else {
                $posts[$i]['ownlike'] = true;
            }
        }

		return $posts;
	}

    /**
     * Get selected amount of recent posts.
     *
     * @since 0.1.0
     * @var null|integer $amount Amount of recent posts to get.
     * @return array Array of recent posts.
     */
    public function getRecent($amount = 25)
	{
		return $this->get($amount);
	}

    /**
     * Get users that has liked a post.
     *
     * @since 0.1.0
     * @var null|integer $postId ID of a post.
     * @return array Array of users that have liked a post.
     */
    public function getLikes($postID)
    {
        // Get an array of users that has liked a post.
        $likes = $this->database->read(
            'u.id, u.display_name, u.username, u.avatar',
            'posts_likes p',
            'INNER JOIN users u ON p.user_id = u.id WHERE p.post_id = ? AND p.active = 1',
            [$postID]
        );

        // Add more elements to an array if any like has been found.
        for ($i = 0; $i < count($likes); $i++) {
            // Set user's avatar or get the default one if not existing.
            $likes[$i]['avatar'] = $likes[$i]['avatar'] ?? 'default';
        }

        return $likes;
    }

    /**
     * Get users that has liked a post.
     *
     * @since 0.1.0
     * @var integer $array ID of a post.
     * @var null|array $array Array with likes from getLikes method.
     * @var boolean $userLiked Boolean if current user has liked a post.
     * @return null|string Text showing who has liked a post.
     */
    public function getLikesString($postID, $array, $hasLiked)
    {
        // Store required variables.
        $likesAmount = count($array);
        $randomUser = null;
        $secondUser = null;

        // Check if post has any likes.
        if ($likesAmount === 0) {
            // Return null if noone has liked a post.
            return null;
        }

        // Get a display name of a random user that has liked a post.
        foreach ($array as $like) {
            // Check if it's not a current user.
            if ($like['id'] != $_SESSION['account']['id']) {
                // Set a display name of a two users and break a loop.
                if (is_null($randomUser)) {
                    $randomUser = $like;
                } else {
                    $secondUser = $like;
                    break;
                }
            }
        }

        // Check if current user has liked a post.
        if ($hasLiked == 'true') {
            // Check if there is only one like.
            if ($likesAmount === 1)
                $string = 'You like this post.';
            // Check if there are two likes.
            else if ($likesAmount === 2)
                $string = 'You and <a href="profile.php?u=' . $randomUser['id'] . '">' . $randomUser['display_name'] . '</a> like this post.';
            // Check if there are three likes.
            else if ($likesAmount === 3)
                $string = 'You, <a href="profile.php?u=' . $randomUser['id'] . '">' . $randomUser['display_name'] . '</a> and ' .
                          '<span class="btn-postshowlikes" data-postid="' . $postID . '" data-toggle="modal" data-target="#mainModal">' . ($likesAmount - 2) . ' other pony</span> like this post.';
            // Check if there are more than three likes.
            else if ($likesAmount === 3)
                $string = 'You, <a href="profile.php?u=' . $randomUser['id'] . '">' . $randomUser['display_name'] . '</a> and ' .
                          '<span class="btn-postshowlikes" data-postid="' . $postID . '" data-toggle="modal" data-target="#mainModal">' . ($likesAmount - 2) . ' other ponies</span> like this post.';
        }
        // Do it if current user has not liked a post.
        else {
            // Check if there is only one like.
            if ($likesAmount === 1)
                $string = '<a href="profile.php?u=' . $randomUser['id'] . '">' . $randomUser['display_name'] . '</a> like this post.';
            // Check if there are two likes.
            else if ($likesAmount === 2)
                $string = '<a href="profile.php?u=' . $randomUser['id'] . '">' . $randomUser['display_name'] . '</a> and <a href="profile.php?u=' . $secondUser['id'] . '">' . $secondUser['display_name'] . '</a> like this post.';
            // Check if there are three likes.
            else if ($likesAmount === 3)
                $string = '<a href="profile.php?u=' . $randomUser['id'] . '">' . $randomUser['display_name'] . '</a>, <a href="profile.php?u=' . $secondUser['id'] . '">' . $secondUser['display_name'] . '</a> and ' .
                          '<span class="btn-postshowlikes" data-postid="' . $postID . '" data-toggle="modal" data-target="#mainModal">' . ($likesAmount - 2) . ' other pony</span> like this post.';
            // Check if there are more than three likes.
            else
                $string = '<a href="profile.php?u=' . $randomUser['id'] . '">' . $randomUser['display_name'] . '</a>, <a href="profile.php?u=' . $secondUser['id'] . '">' . $secondUser['display_name'] . '</a> and ' .
                          '<span class="btn-postshowlikes" data-postid="' . $postID . '" data-toggle="modal" data-target="#mainModal">' . ($likesAmount - 2) . ' other ponies</span> like this post.';
        }

        return $string;
    }

    /**
     * Like/unlike selected post.
     *
     * @since 0.1.0
     * @var string $postID ID of a post to like/unlike.
     * @return bool Result of this method.
     */
    public function like($postID)
    {
        // Get details about likes for post.
        $post = $this->database->read(
            'pst.id, pst.like_count, lik.id AS like_id, lik.user_id, lik.active',
            'posts pst',
            'LEFT JOIN (SELECT id, post_id, user_id, active FROM posts_likes WHERE post_id = ? AND user_id = ?) AS lik ON pst.id = lik.post_id WHERE pst.id = ? AND status != 9',
            [$postID, $_SESSION['account']['id'], $postID]
        );

        // Return error if post have not been found.
        if (count($post) != 1) {
            return false;
        }

        // Add like if user has not liked it before (Like).
        if (is_null($post[0]['user_id'])) {
            // Add like row to the database.
            $hasLiked = $this->database->create(
                'post_id, user_id',
                'posts_likes',
                '',
                [$postID, $_SESSION['account']['id']]
            );

            // Return error if user couldn't like post.
            if (empty($hasLiked)) {
                return false;
            }

            // Add one to post like counter.
            $this->database->update(
                'like_count',
                'posts',
                'WHERE id = ?',
                [$post[0]['like_count'] + 1, $postID]
            );
        }
        // Update like if user has unliked it (Like again).
        else if ($post[0]['active'] == false) {
            // Change the active status to true for the like in the database.
            $this->database->update(
                'active',
                'posts_likes',
                'WHERE id = ?',
                [1, $post[0]['like_id']]
            );

            // Add one to post like counter.
            $this->database->update(
                'like_count',
                'posts',
                'WHERE id = ?',
                [$post[0]['like_count'] + 1, $postID]
            );
        }
        // Remove like if user has already liked post (Unlike).
        else {
            // Change the active status to false for the like in the database.
            $this->database->update(
                'active',
                'posts_likes',
                'WHERE id = ?',
                [0, $post[0]['like_id']]
            );

            // Subtract one from post like counter.
            $this->database->update(
                'like_count',
                'posts',
                'WHERE id = ?',
                [$post[0]['like_count'] - 1, $postID]
            );
        }

        return true;
    }

    /**
     * Add a comment to a post.
     *
     * @since 0.1.0
     * @var integer $postID ID of a post.
     * @var string $content Comment content.
     * @return boolean Status of this method.
     */
    public function comment($postID, $content) {
        // Return false if comment contains more than 250 characters.
        if (strlen($content) > 250) {
            return false;
        }

        // Store required variables.
        $currentDatetime = $this->system->getDatetime();

        // Get a selected post.
        $post = $this->database->read(
            'comment_count',
            'posts',
            'WHERE id = ?',
            [$postID]
        );

        // Check if post is existing.
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

        // Update post's comments counter.
        $this->database->update(
            'comment_count',
            'posts',
            'WHERE id = ?',
            [intval($post[0]['comment_count']) + 1, $postID]
        );

        return $commentID;
    }

    /**
     * Get comments for a post.
     *
     * @since 0.1.0
     * @var integer $postID ID of a post.
     * @var integer $lastCommentID ID of an oldest fetched post.
     * @var null|integer $amount Amount of comments to fetch.
     * @var string $mode Mode of comments fetching (first, more, send).
     * @return array Array of selected post comments.
     */
    public function getPostComments($postID, $lastCommentID, $amount, $mode = 'first') {
        // Get array of comments for selected post.
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

        // Reverse an array to display newest post on bottom.
        $comments = array_reverse($comments);

        // Return array of selected post comments.
        return $comments;
    }

    /**
     * Count created posts.
     * Counts standard posts created by user's except deleted ones.
     *
     * @since 0.1.0
     * @return string Amount of available posts.
     */
    public function getPostsCount() {
        $existingPosts = $this->database->read(
			'COUNT(*) AS posts',
			'posts',
			'WHERE type = 1 AND status = 0',
			[]
		);

        return $existingPosts[0]['posts'];
    }

    /**
     * Create a new post.
     *
     * @since 0.1.0
     * @var string $userID ID of a post author.
     * @var string $postContent Post's content message.
     * @var string $postType Post's type ID.
     * @return boolean Result of this method.
     */
    public function create($userID, $postContent, $postType)
	{
        // Store common system values.
        $currentIP = $this->system->getVisitorIP();
		$currentDatetime = $this->system->getDatetime();

        // Make sure that standard post is at least 3 characters long.
		if ($postType === 1 && strlen($postContent) < 3) {
            $this->system->setMessage(
                'error',
                'Post needs to contain at least 3 characters.'
            );

			return false;
		}

        // Make sure that standard post doesn't contain more than 1000 characters.
		if ($postType === 1 && strlen($postContent) < 3) {
            $this->system->setMessage(
                'error',
                'Post can\'t contain more than 1000 characters.'
            );

			return false;
		}

        // Insert post into database.
		$this->database->create(
			'user_id, ip, datetime, content, type',
			'posts',
			'',
			[$userID, $currentIP, $currentDatetime, $postContent, $postType]
		);

		return true;
	}

    /**
     * Delete own post.
     * // TODO Allow moderators to delete posts.
     *
     * @since 0.1.0
     * @var string $postID ID of a post.
     * @var string|null $reason Reason of deleting.
     * @return boolean Result of this method.
     */
    public function delete($postID, $reason = null)
	{
        // Store common system values.
        $currentIP = $this->system->getVisitorIP();
		$currentDatetime = $this->system->getDatetime();

        // Get details about existing and not removed standard post.
        $post = $this->database->read(
            'id, user_id',
            'posts',
            'WHERE id = ? AND type = 1 AND status != 9',
            [intval($postID)]
        );

        // Check if post has been found.
        if (count($post) != 1) {
            // TODO Error if post doesn't exist, has been removed or is not of a standard type.
            return false;
        }

        // Check if user is allowed to delete a post.
        if ($post[0]['user_id'] != $_SESSION['account']['id']) {
            // Check if user is a moderator.
            $moderator = $this->database->read(
                'account_type',
                'users',
                'WHERE id = ?',
                [$_SESSION['account']['id']]
            );

            // Check if user is allowed to delete post.
            if ($moderator[0]['account_type'] != 8 && $moderator[0]['account_type'] != 9) {
                return false;
            }
        }

        // Change post status to removed.
        $isRemoved = $this->database->update(
            'status, delete_id, delete_ip, delete_datetime, delete_reason',
            'posts',
            'WHERE id = ?',
            [9, $_SESSION['account']['id'], $currentIP, $currentDatetime, $reason, $postID]
        );

        // Return unsuccessful result if SQL update haven't changed any rows.
        if (empty($isRemoved)) {
            return false;
        }

		return true;
	}
}
