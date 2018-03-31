<?php

$isAJAXCall = true;
$loginRequired = true;
$readonlyDenied = true;

// Require system initialization code
require_once('../../application/partials/init.php');

// Use post class for creating a new post
use BronyCenter\Post;
$posts = Post::getInstance();

// Check if post type is valid
if ($_POST['type'] != 1) {
    die();
}

// Try to create a new post
$postID = $posts->add(
    $_POST['content'] ?? '',
    intval($_POST['type'])
);

// Return a new post ID
echo $postID;

// TODO Return error in JSON if not
