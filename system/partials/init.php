<?php

// Force PHP to display all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start a new session or use an existing one
session_start();

// Require config class to read configuration of an application
require(__DIR__ . '../../class/AssertrexPHP/config.php');

// Require utilities class to share many commonly used functions
require(__DIR__ . '../../class/AssertrexPHP/utilities.php');

// Require database class to create a connection
require(__DIR__ . '../../class/AssertrexPHP/database.php');

// Require account class for managing user's account
require(__DIR__ . '../../class/AssertrexPHP/account.php');

// Require session class to manage user session
require(__DIR__ . '../../class/AssertrexPHP/session.php');

// Require flash class to use flash session messages
require(__DIR__ . '../../class/AssertrexPHP/flash.php');

// Require validator class for validating user input
require(__DIR__ . '../../class/AssertrexPHP/validator.php');

// Require post class for handling users details
require(__DIR__ . '../../class/user.php');

// Require post class for handling users posts
require(__DIR__ . '../../class/post.php');

// Require statistics class for counting users actions
require(__DIR__ . '../../class/statistics.php');

// Create an instance of a config class to get details about website version
$config = AssertrexPHP\Config::getInstance();

// Create an instance of an utilities class to share common functions
$utilities = AssertrexPHP\Utilities::getInstance();

// Create an instance of a user class to share common functions
$user = BronyCenter\User::getInstance();

// Get stored flash messages from session
$flash = AssertrexPHP\Flash::getInstance();
$flashMessages = $flash->get();

// Check if user is logged in and verify a session
$session = AssertrexPHP\Session::getInstance();

// Create an instance of a statistics class to count users actions
$statistics = BronyCenter\Statistics::getInstance();

// Get details about website version
$websiteVersion = $config->getVersion();

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
