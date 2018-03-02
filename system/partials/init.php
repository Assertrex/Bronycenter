<?php

// Force PHP to display all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start a new session or use an existing one
session_start();

// Require config class to read configuration of an application
require(__DIR__ . '../../class/config.php');

// Require utilities class to share many commonly used functions
require(__DIR__ . '../../class/utilities.php');

// Require database class to create a connection
require(__DIR__ . '../../class/database.php');

// Require account class for managing user's account
require(__DIR__ . '../../class/account.php');

// Require session class to manage user session
require(__DIR__ . '../../class/session.php');

// Require flash class to use flash session messages
require(__DIR__ . '../../class/flash.php');

// Require validator class for validating user input
require(__DIR__ . '../../class/validator.php');

// Require post class for handling users details
require(__DIR__ . '../../class/user.php');

// Require post class for handling users posts
require(__DIR__ . '../../class/post.php');

// Require statistics class for counting users actions
require(__DIR__ . '../../class/statistics.php');

// Require message class for actions with messages
require(__DIR__ . '../../class/message.php');

// Create an instance of a config class to get details about website version
$config = BronyCenter\Config::getInstance();

// Create an instance of an utilities class to share common functions
$utilities = BronyCenter\Utilities::getInstance();

// Create an instance of a user class to share common functions
$user = BronyCenter\User::getInstance();

// Get stored flash messages from session
$flash = BronyCenter\Flash::getInstance();
$flashMessages = $flash->get();

// Check if user is logged in and verify a session
$session = BronyCenter\Session::getInstance();

// Create an instance of a statistics class to count users actions
$statistics = BronyCenter\Statistics::getInstance();

// Create an instance of a message class for actions with messages
$o_message = BronyCenter\Message::getInstance();

// Get details about website version
$websiteVersion = $config->getVersion();

// Store a messages encryption key
$messageEncryptionKey = $config->getSection('messages')['key'];

// Check if user is currently logged in
if ($session->verify()) {
    $loggedIn = true;

    // Check if user is a moderator
    if ($_SESSION['account']['isModerator']) {
        $loggedModerator = true;
    } else {
        $loggedModerator = false;
    }
} else {
    $loggedIn = false;
    $loggedModerator = false;
}

// TODO Handle not verified users and muted & banned users

// Redirect not logged guest if page requires log in
if (isset($loginRequired) && $loggedIn === false) {
    $flash->error('You need to be logged in to view this page.');
    header('Location: ../');
    die();
}
