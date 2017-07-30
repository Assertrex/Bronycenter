<?php
// Allow access only for logged users.
$loginRequired = true;

// Require system initialization code.
require_once('../../system/inc/init.php');

// Leave reason field null if not provided.
$deleteReason = $_POST['reason'] ?? null;

// Try to delete selected post.
$o_post->delete($_POST['id'], $deleteReason);

// TODO Return error in JSON if not.
