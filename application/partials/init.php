<?php

// Start a new session or use an existing one
session_start();

// Set a default timezone as a UTC
date_default_timezone_set('UTC');

// Create an instance of a config class
require(__DIR__ . '/../core/config.php');
$config = BronyCenter\Config::getInstance();

// Get website's settings
$websiteSettings = $config->getSection('system');
$websiteVersion = $config->getVersion();
$websiteEncryptionKey = $config->getSection('messages')['key'];

// Get a translation strings depending on a website's language
$websiteLanguage = $websiteSettings['language'] ?? 'en';
require_once(__DIR__ . '/translation/' . $websiteLanguage . '.php');

// Enable error reporting if debugging is enabled
if ($websiteSettings['enableDebug']) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

// Require all classes
require(__DIR__ . '/../core/database.php');
require(__DIR__ . '/../core/utilities.php');
require(__DIR__ . '/../core/account.php');
require(__DIR__ . '/../core/session.php');
require(__DIR__ . '/../core/flash.php');
require(__DIR__ . '/../core/validator.php');
require(__DIR__ . '/../core/user.php');
require(__DIR__ . '/../core/post.php');
require(__DIR__ . '/../core/statistics.php');
require(__DIR__ . '/../core/message.php');

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
$o_message->setEncryptionKey($websiteEncryptionKey);

// Account default variables
$loggedIn = false;
$loggedModerator = false;
$readonlyState = false;
$readonlyStateString = null;

// Check if user is currently logged in
if ($session->verify()) {
    $loggedIn = true;

    // Check if user is a moderator
    if ($_SESSION['account']['isModerator']) {
        $loggedModerator = true;
    }

    // Check if account is in a read-only state
    if (!empty($_SESSION['account']['reason_readonly'])) {
        $readonlyState = true;

        switch ($_SESSION['account']['reason_readonly']) {
            case 'unverified':
                $readonlyStateString = $translationArray['errors']['accountUnverified'];
                break;
            case 'muted':
                $readonlyStateString = $translationArray['errors']['accountMuted'];
                break;
            default:
                $readonlyStateString = $translationArray['errors']['accountReadonly'];
        }
    }
} else {
    $session->destroy();
}

// Redirect not logged guest if page requires log in
if (isset($loginRequired) && $loginRequired == true && $loggedIn != true) {
    $flash->error($translationArray['errors']['loginRequired']);
    header('Location: ../');
    die();
}

// Redirect user back if page can be accessed only by moderators
if (!empty($moderatorRequired) && $moderatorRequired == true && $loggedModerator != true) {
    $flash->error($translationArray['errors']['moderatorRequired']);
    header('Location: index.php');
    die();
}

// Stop executing if page or AJAX requires an account outside of readonly state
if (isset($readonlyDenied) && $readonlyDenied == true && $readonlyState != false) {
    if (isset($isAJAXCall) && $isAJAXCall == true) {
        $AJAXCallJSON = [
            'status' => 'error',
            'error' => $readonlyStateString
        ];

        die(json_encode($AJAXCallJSON, JSON_UNESCAPED_UNICODE));
    }

    $flash->error($readonlyStateString);
    header('Location: index.php');
    die();
}
