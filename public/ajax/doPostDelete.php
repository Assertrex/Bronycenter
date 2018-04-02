<?php
$pageSettings = [
    'isAJAXCall' => true,
    'readonlyDenied' => true,
    'loginRequired' => true,
    'moderatorRequired' => false,
];

require_once('../../application/partials/init.php');

// Use post class for creating a new post
use BronyCenter\Post;
$posts = Post::getInstance();

// Try to remove a post
$postID = $posts->delete($_POST['id'], $_POST['reason'] ?? null);

// Prepare array with result
if (!empty($postID)) {
    $JSON = [
        'status' => 'success',
        'post' => $postID
    ];
} else {
    $JSON = [
        'status' => 'error'
    ];
}

// Format array into JSON
$JSON = json_encode($JSON);

// Display JSON result
echo $JSON;
