<?php

$isAJAXCall = true;
$loginRequired = true;
$readonlyDenied = true;

// Require system initialization code
require_once('../../application/partials/init.php');

// Use a Post class for handling users posts
use BronyCenter\Post;
$posts = Post::getInstance();

// Try to add a comment to a post
$posts->comment($_POST['id'], $_POST['content']);

// TODO Return error in JSON if not
