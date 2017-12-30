<?php

// Start a new session or use an existing one
session_start();

// Require config class to read configuration of an application
require(__DIR__ . '../../class/AssertrexPHP/config.php');

// Require utilities class to share many commonly used functions
require(__DIR__ . '../../class/AssertrexPHP/utilities.php');

// Require database class to create a connection
require(__DIR__ . '../../class/AssertrexPHP/database.php');

// Require flash class to use flash session messages
require(__DIR__ . '../../class/AssertrexPHP/flash.php');

// Require session class to manage user session
require(__DIR__ . '../../class/AssertrexPHP/session.php');

// Check if user is logged in and verify a session
$session = AssertrexPHP\Session::getInstance();

// Check if user is currently logged in
if ($session->verify()) {
    echo 'true';
} else {
    echo 'false';
}
