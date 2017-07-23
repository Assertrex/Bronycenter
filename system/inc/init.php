<?php

// Require system class for file handling, dates formatting etc.
require_once(__DIR__ . '/../class/system.php');
$o_system = new System();

// Report all PHP errors for development website and disable them for production.
if ($o_system->development) {
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 'Off');
    error_reporting(0);
}

// Require database class for interaction with it.
require_once(__DIR__ . '/../class/database.php');
$o_database = new Database($o_system);

// Require session class for creating and handling user session.
require_once(__DIR__ . '/../class/session.php');
$o_session = new Session($o_system, $o_database);

// Require validate class for validating user inputs.
require_once(__DIR__ . '/../class/validate.php');
$o_validate = new Validate($o_system);

// Require user class for actions with users accounts.
require_once(__DIR__ . '/../class/user.php');
$o_user = new User($o_system, $o_database, $o_session, $o_validate);

// Require user class for actions with user's posts.
require_once(__DIR__ . '/../class/post.php');
$o_post = new Post($o_system, $o_database, $o_validate);

// Check if user is logged in.
if ($o_session->verify()) {
    $isLogged = true;

    // Check if user has verified an e-mail address.
    if (!is_null($_SESSION['user']['email'])) {
        $emailVerified = true;
    } else {
        $emailVerified = false;
    }
} else {
    $isLogged = false;
}

// Check if user is logged in on login restricted pages.
if (!empty($loginRequired) && !$isLogged) {
    // Show failed system message if user is not logged in.
    $o_system->setMessage(
        'error',
        'Page is visible only for logged users!'
    );

    // Redirect user into homepage if user is not logged in.
    header('Location: ../index.php');
    die();
}
