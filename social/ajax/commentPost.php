<?php
// Allow access only for logged users.
$loginRequired = true;

// Require system initialization code.
require_once('../../system/inc/init.php');

// Try to add a comment to a post.
$o_post->comment($_POST['id'], $_POST['content']);

// TODO Return error in JSON if not.
