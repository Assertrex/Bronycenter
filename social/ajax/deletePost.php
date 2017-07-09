<?php
// Allow access only for logged users.
$loginRequired = true;

// Require system initialization code.
require_once('../../system/inc/init.php');

// Try to delete selected post.
$o_post->delete($_POST['id']);

// TODO Return error in JSON if not.
