<?php
$pageSettings = [
    'isAJAXCall' => true,
    'readonlyDenied' => false,
    'loginRequired' => true,
    'moderatorRequired' => false,
];

require_once('../../application/partials/init.php');

// Use a Post class for handling users posts
use BronyCenter\Post;
$posts = Post::getInstance();

// Get an amount of lastest posts
$postsAmount = $posts->getPostsAmount(
    'available_lastest',
    [
        'fetchFromID' => $_GET['id']
    ]
);

// Prepare array with result
$JSON = [
    'status' => 'success',
    'amount' => $postsAmount
];

// Format array into JSON
$JSON = json_encode($JSON);

// Display JSON result
echo $JSON;
