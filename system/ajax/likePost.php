<?php

// Don't show system messages
$deferSystemMessages = true;

// Require common PHP classes and verify a session here
require_once('../inc/init.php');

// Try to add like to a post
$o_post->like($_POST['id']);

// TODO Return error in JSON if not
