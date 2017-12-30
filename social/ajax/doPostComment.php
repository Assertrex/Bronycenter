<?php

// Allow access only for logged users
$loginRequired = true;

// Require system initialization code
require_once('../../system/partials/init.php');

// Use a Post class for handling users posts
use BronyCenter\Post;
$posts = Post::getInstance();

// Try to add a comment to a post
$posts->comment($_POST['id'], $_POST['content']);

// TODO Return error in JSON if not
