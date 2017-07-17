<?php
// Allow access only for logged users.
$loginRequired = true;

// Require system initialization code.
require_once('../../system/inc/init.php');

// Get list of post likes.
$likes = $o_post->getLikes($_GET['id']);

// Get string about users that has liked a post.
$likesString = $o_post->getLikesString($likes, $_GET['ownlike']);

// Return a string containing string about user likes.
echo $likesString;

// TODO Return error in JSON if not.
