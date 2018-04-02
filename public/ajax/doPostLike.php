<?php
$pageSettings = [
    'isAJAXCall' => true,
    'readonlyDenied' => true,
    'loginRequired' => true,
    'moderatorRequired' => false,
];

require_once('../../application/partials/init.php');

// Use a Post class for handling users posts
use BronyCenter\Post;
$posts = Post::getInstance();

// Like or unlike a post
$posts->addLike($_GET['id']);

// TODO Return error in JSON if not
