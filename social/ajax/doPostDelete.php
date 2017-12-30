<?php

// Allow access only for logged users
$loginRequired = true;

// Require system initialization code
require_once('../../system/partials/init.php');

// Use post class for creating a new post
use BronyCenter\Post;
$posts = Post::getInstance();

// Try to remove a post
$postID = $posts->delete($_GET['id'], $_GET['reason'] ?? null);

// Check if post has been deleted
if (!empty($postID)) {
    echo $postID;
} else {
    echo 'false';
}

// TODO Return error in JSON if not
