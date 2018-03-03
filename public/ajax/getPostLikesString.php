<?php

// Allow access only for logged users
$loginRequired = true;

// Require system initialization code
require_once('../../application/partials/init.php');

// Use post class reading post likes
use BronyCenter\Post;
$posts = Post::getInstance();

// Get a string containing post likes string
$likes = $posts->getLikes($_GET['id']);

$string = $posts->getLikesString($_GET['id'], $likes, $_GET['hasliked']);

// Return a post likes string
if (!empty($string)) {
    echo $string;
}

// TODO Return error in JSON if not
