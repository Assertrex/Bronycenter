<?php

// Don't show system messages
$deferSystemMessages = true;

// Require common PHP classes and verify a session here
require_once('../inc/init.php');

// Try to delete selected post
$o_post->delete($_POST['id']);

// TODO Return error in JSON if not
