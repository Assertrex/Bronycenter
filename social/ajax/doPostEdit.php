<?php

// Allow access only for logged users
$loginRequired = true;

// Require system initialization code
require_once('../../system/partials/init.php');

// Use a Post class for handling users posts
use BronyCenter\Post;
$posts = Post::getInstance();

// Edit a post
$newContent = $posts->edit($_POST['id'], $_POST['content']);

// Prepare array with result
if (!empty($newContent)) {
    $JSON = [
        'status' => 'success',
        'post' => $_POST['id'],
        'content' => $newContent
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
