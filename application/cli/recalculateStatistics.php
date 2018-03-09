<?php

// Require an initialization code for a CLI script
require('init.php');

// Display a console message about starting executing a script
echo 'Starting recalculating users statistics. This might take a while...' . PHP_EOL;
echo PHP_EOL;

/**
 * Step 1: Fetching users and preparing an statistics array
**/
echo '[Step 1]: Fetching users ID\'s and preparing a statistics array.' . PHP_EOL;

// Get all existing users into a temporary array
$arr_users_temp = $class_database->read(
    'id',
    'users',
    '',
    []
);

// Prepare a new array
$arr_statistics = [];

// Insert each user into a new array and prepare empty statistics fields values
foreach ($arr_users_temp as $arr_users_temp_item) {
    $arr_statistics[$arr_users_temp_item['id']] = [
        'user_id' => $arr_users_temp_item['id'],
        'user_points' => 0,
        'posts_created' => 0,
        'posts_removed' => 0,
        'posts_removed_mod' => 0,
        'posts_likes_given' => 0,
        'posts_likes_received' => 0,
        'posts_comments_removed' => 0,
        'posts_comments_removed_mod' => 0,
        'posts_comments_given' => 0,
        'posts_comments_received' => 0
    ];
}

// Clear a temporary array
unset($arr_users_temp);

/**
 * Step 2: Counting details about created and removed posts
**/
echo '[Step 2]: Counting details about created and removed posts.' . PHP_EOL;

// Get all existing posts
$arr_posts = $class_database->read(
    'user_id, type, status, delete_moderator, delete_id',
    'posts',
    'WHERE type = 1',
    []
);

// Analyze each post and count posts creations and removals
foreach ($arr_posts as $arr_posts_item) {
    $authorID = $arr_posts_item['user_id'];
    $isRemoved = false;
    $hasRemovedModerator = false;

    // Check if post has been removed
    if ($arr_posts_item['status'] == 9) {
        $isRemoved = true;
    }

    // Check if post has been removed as a moderator
    if ($arr_posts_item['delete_moderator'] == 1 && ($arr_posts_item['user_id'] != $arr_posts_item['delete_id'])) {
        $hasRemovedModerator = true;
    }

    // Insert statistics into an array
    if (array_key_exists($authorID, $arr_statistics)) {
        // Count a post as created
        $arr_statistics[$authorID]['posts_created']++;
        $arr_statistics[$authorID]['user_points'] += 10;

        // Count a post as removed
        if ($isRemoved && !$hasRemovedModerator) {
            $arr_statistics[$authorID]['posts_removed']++;
            $arr_statistics[$authorID]['user_points'] -= 10;
        }

        // Count a post as removed by a moderator
        if ($isRemoved && $hasRemovedModerator) {
            $arr_statistics[$authorID]['posts_removed_mod']++;
            $arr_statistics[$authorID]['user_points'] -= 10;
        }
    }
}

// Clear an array
unset($arr_posts);

/**
 * Step 3: Counting details about existing posts likes
**/
echo '[Step 3]: Counting details about existing posts likes.' . PHP_EOL;

// Get all existing posts likes
$arr_posts_likes = $class_database->read(
    'l.id, l.post_id, p.user_id AS post_author_id, l.user_id, l.active',
    'posts_likes l',
    'INNER JOIN `posts` p ON p.id = l.post_id WHERE l.active = 1 AND p.status != 9',
    []
);

// Analyze each post like
foreach ($arr_posts_likes as $arr_posts_likes_item) {
    $authorID = $arr_posts_likes_item['post_author_id'];
    $likeByID = $arr_posts_likes_item['user_id'];
    $ownPost = ($authorID == $likeByID);

    // Check if post has not been liked by it's author
    if (!$ownPost) {
        if (array_key_exists($likeByID, $arr_statistics)) {
            // Count a like as given on someone's post
            $arr_statistics[$likeByID]['posts_likes_given']++;
            $arr_statistics[$likeByID]['user_points'] += 1;
        }

        if (array_key_exists($authorID, $arr_statistics)) {
            // Count a like as received from different user on own post
            $arr_statistics[$authorID]['posts_likes_received']++;
            $arr_statistics[$authorID]['user_points'] += 5;
        }
    }
}

// Clear an array
unset($arr_posts_likes);

/**
 * Step 4: Counting details about existing posts comments
**/
echo '[Step 4]: Counting details about existing posts comments.' . PHP_EOL;

// Get all existing posts comments
$arr_posts_comments = $class_database->read(
    'c.id, c.post_id, p.user_id AS post_author_id, c.user_id, c.active',
    'posts_comments c',
    'INNER JOIN `posts` p ON p.id = c.post_id WHERE c.active = 1 AND p.status != 9',
    []
);

// Analyze each post comment
foreach ($arr_posts_comments as $arr_posts_comments_item) {
    $authorID = $arr_posts_comments_item['post_author_id'];
    $commentByID = $arr_posts_comments_item['user_id'];
    $ownPost = ($authorID == $commentByID);

    // Check if post has not been commented by it's author
    if (!$ownPost) {
        if (array_key_exists($commentByID, $arr_statistics)) {
            // Count a comment as given on someone's post
            $arr_statistics[$commentByID]['posts_comments_given']++;
            $arr_statistics[$commentByID]['user_points'] += 1;
        }

        if (array_key_exists($authorID, $arr_statistics)) {
            // Count a comment as received from different user on own post
            $arr_statistics[$authorID]['posts_comments_received']++;
            $arr_statistics[$authorID]['user_points'] += 3;
        }
    }
}

// Clear an array
unset($arr_posts_comments);

/**
 * Step 5: Update statistics values in a database
**/
echo '[Step 5]: Update statistics values in a database.' . PHP_EOL;

// Update statistics values for each user in database
foreach ($arr_statistics as $arr_statistics_user) {
    $class_database->update(
        'user_points, posts_created, posts_removed, posts_removed_mod, posts_likes_given, ' .
        'posts_likes_received, posts_comments_removed, posts_comments_removed_mod, ' .
        'posts_comments_given, posts_comments_received',
        'users_statistics',
        'WHERE user_id = ?',
        [
            $arr_statistics_user['user_points'],
            $arr_statistics_user['posts_created'],
            $arr_statistics_user['posts_removed'],
            $arr_statistics_user['posts_removed_mod'],
            $arr_statistics_user['posts_likes_given'],
            $arr_statistics_user['posts_likes_received'],
            $arr_statistics_user['posts_comments_removed'],
            $arr_statistics_user['posts_comments_removed_mod'],
            $arr_statistics_user['posts_comments_given'],
            $arr_statistics_user['posts_comments_received'],
            $arr_statistics_user['user_id']
        ]
    );
}

// // Print each user statistics
// ksort($arr_statistics);
//
// foreach ($arr_statistics as $arr_statistics_user) {
//     echo json_encode($arr_statistics_user) . PHP_EOL;
// }

// Show script execution statistics
$execute_time_ms = round((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000);

echo PHP_EOL;
echo '[RESULT]: Finished script execution in ' . $execute_time_ms . 'ms.' . PHP_EOL;
echo '[RESULT]: Modified statistics for ' . count($arr_statistics) . ' users.' . PHP_EOL;
echo PHP_EOL;
