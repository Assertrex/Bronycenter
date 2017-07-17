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
			'p.id, p.user_id, p.datetime, p.content, p.type, u.display_name, u.last_online, u.country_code, u.avatar, d.birthdate, d.gender, l.user_id AS like_id',
			'posts p',
			'INNER JOIN users u ON p.user_id = u.id
             INNER JOIN users_details d ON d.user_id = u.id
             LEFT JOIN (SELECT id, post_id, user_id FROM posts_likes WHERE user_id = ? AND active = 1) AS l ON p.id = l.post_id
             WHERE status != 9 ORDER BY id DESC LIMIT ?',
			[$_SESSION['account']['id'], $amount]
		);

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
            'u.id, u.display_name',
            'posts_likes p',
            'INNER JOIN users u ON p.user_id = u.id WHERE p.post_id = ? AND p.active = 1',
            [$postID]
        );

        return $likes;
    }

    /**
     * Get users that has liked a post.
     *
     * @since 0.1.0
     * @var null|array $array Array with likes from getLikes method.
     * @var null|string $userLiked Null or ID of current user if has liked a post.
     * @return null|string Text showing who has liked a post.
     */
    public function getLikesString($array, $hasLiked)
    {
        // Store required variables.
        $likesAmount = count($array);
        $randomUser = null;

        // Check if post has any likes.
        if ($likesAmount === 0) {
            // Return null if noone has liked a post.
            return null;
        }

        // Get a display name of a random user that has liked a post.
        foreach ($array as $like) {
            // Check if it's not a current user.
            if ($like['id'] != $_SESSION['account']['id']) {
                // Set a display name of a user and break a loop.
                $randomUser = $like;
                break;
            }
        }

        // Check if current user has liked a post.
        if (!is_null($hasLiked)) {
            // Check if there is only one like (of current user).
            if ($likesAmount === 1)
                $string = 'You like this post.';
            // Check if there are two likes (of current user and somepony else).
            else if ($likesAmount === 2)
                $string = 'You and <a href="profile.php?u=' . $randomUser['id'] . '">' . $randomUser['display_name'] . '</a> like this post.';
            // Check if there are more than two likes (with current user's like).
            else
                $string = 'You and ' . ($likesAmount - 1) . ' other ponies like this post.';
        }
        // Do it if current user has not liked a post.
        else {
            // Check if there is only one like.
            if ($likesAmount === 1)
                $string = '<a href="profile.php?u=' . $randomUser['id'] . '">' . $randomUser['display_name'] . '</a> like this post.';
            // Check if there are more likes than one.
            else
                $string = $likesAmount . ' ponies like this post.';
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
			'WHERE type = 1',
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

        // Escape HTML characters in post's content.
        $postContent = htmlspecialchars($postContent, ENT_QUOTES);

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
            'id',
            'posts',
            'WHERE id = ? AND type = 1 AND status != 9',
            [$postID]
        );

        // Check if post has been found.
        if (count($post) != 1) {
            // TODO Error if post doesn't exist, has been removed or is not of a standard type.
            return false;
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
