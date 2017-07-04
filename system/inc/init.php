<?php

// Create new or use existing PHP session
session_start();

// Report all errors // TODO Only on devserver
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Require system class for file handling, dates formatting etc.
require_once(__DIR__ . '/../class/system.php');
$o_system = new System();

// Require database class for interaction with it
require_once(__DIR__ . '/../class/database.php');
$o_database = new Database($o_system);

// Require validate class for validating user inputs
require_once(__DIR__ . '/../class/validate.php');
$o_validate = new Validate($o_system);

// Require user class for actions with users accounts
require_once(__DIR__ . '/../class/user.php');
$o_user = new User($o_system, $o_database, $o_validate);

// Require user class for actions with user's posts
require_once(__DIR__ . '/../class/post.php');
$o_post = new Post($o_system, $o_database, $o_validate);

// Check if user is logged in
if ($o_user->verifySession()) {
    $isLogged = true;

    // Check if user has verified an e-mail address
    if (!is_null($_SESSION['user']['email'])) {
        $emailVerified = true;
    } else {
        $emailVerified = false;
    }
} else {
    $isLogged = false;
}

// Redirect guest if login is required
if (!empty($loginRequired) && !$isLogged) {
    $o_system->setMessage('error', 'Page is visible only for logged users!');
    header('Location: ../index.php');
}
