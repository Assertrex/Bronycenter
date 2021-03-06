<?php

date_default_timezone_set('UTC');
session_start();

require(__DIR__ . '/../core/config.php');
require(__DIR__ . '/../core/translation.php');
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

$o_config = BronyCenter\Config::getInstance();
$o_translation = BronyCenter\Translation::getInstance();

$websiteSettings = $o_config->getSettings('system');
$websiteEncryptionKey = $o_config->getSettings('messages')['key'];

if ($websiteSettings['enableErrors']) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

$utilities = BronyCenter\Utilities::getInstance();
$user = BronyCenter\User::getInstance();
$flash = BronyCenter\Flash::getInstance();
$flashMessages = $flash->get();
$session = BronyCenter\Session::getInstance();
$statistics = BronyCenter\Statistics::getInstance();
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
                $readonlyStateString = $o_translation->getString('errors', 'accountUnverified');
                break;
            case 'muted':
                $readonlyStateString = $o_translation->getString('errors', 'accountMuted');
                break;
            default:
                $readonlyStateString = $o_translation->getString('errors', 'accountReadonly');
        }
    }
} else {
    $session->destroy();
}

// Redirect not logged guest if page requires log in
if (isset($pageSettings['loginRequired']) && $pageSettings['loginRequired'] == true && $loggedIn != true) {
    if (!empty($pageSettings['isAJAXCall'])) {
        $AJAXCallJSON = [
            'status' => 'error',
            'errorMessage' => $o_translation->getString('ajax', 'loginRequired')
        ];

        die($utilities->encodeJSON($AJAXCallJSON));
    }

    $flash->error($o_translation->getString('errors', 'loginRequired'));
    $_SESSION['data']['pathLoginAccessed'] = $_SERVER['REQUEST_URI'];

    header('Location: ../login.php');
    die();
}

// Redirect user back if page can be accessed only by moderators
if (!empty($pageSettings['moderatorRequired']) && $pageSettings['moderatorRequired'] == true && $loggedModerator != true) {
    $flash->error($o_translation->getString('errors', 'moderatorRequired'));

    header('Location: index.php');
    die();
}

// Stop executing if page or AJAX requires an account outside of readonly state
if (isset($pageSettings['readonlyDenied']) && $pageSettings['readonlyDenied'] == true && $readonlyState != false) {
    if (isset($pageSettings['isAJAXCall']) && $pageSettings['isAJAXCall'] == true) {
        $AJAXCallJSON = [
            'status' => 'error',
            'errorMessage' => $readonlyStateString,
        ];

        die($utilities->encodeJSON($AJAXCallJSON));
    }

    $flash->error($readonlyStateString);
    header('Location: index.php');
    die();
}
