<?php
// Allow access only for logged users.
$loginRequired = true;

// Require system initialization code.
require_once('../../system/inc/init.php');

// Try to add like to a post.
$o_post->like($_POST['id']);

// TODO Return error in JSON if not.
