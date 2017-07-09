<?php
// Require system initialization code.
require_once('system/inc/init.php');

// Redirect user if any of $_GET fields is invalid.
if (empty($_GET['key']) || empty($_GET['id']) || strlen($_GET['key']) !== 16 || !is_numeric($_GET['id'])) {
    $o_system->setMessage('error', 'Your e-mail verification link is invalid.');
	header('Location: index.php');
	die();
}

// Get e-mail address of requested user.
$rowKey = $o_database->read(
	'id, email',
	'key_email',
	'WHERE user_id = ? AND hash = ? AND used_datetime IS NULL',
	[$_GET['id'], $_GET['key']]
);

// Check if key is existing.
if (count($rowKey) !== 1) {
    // Show failed system message on invalid key.
    $o_system->setMessage(
        'error',
        'Your e-mail verification link is invalid or has expired.'
    );

    // Redirect user into the homepage.
	header('Location: index.php');
	die();
}

// Get username of a user that's already using this e-mail address.
$rowUser = $o_database->read(
	'display_name, username',
	'users',
	'WHERE email = ?',
	[$rowKey[0]['email']]
);

// Check if any user is already using this e-mail address.
if (count($rowUser) === 1) {
    // Show failed system message if different user is using this e-mail address.
    $o_system->setMessage(
        'error',
        'This e-mail address is already used by <b>' . $row[0]['display_name'] . '</b>.'
    );

    // Redirect user into the homepage.
	header('Location: index.php');
	die();
}

// Store common variables.
$currentIP = $o_system->getVisitorIP();
$currentDatetime = $o_system->getDatetime();

// Check if user is validating e-mail for the first time, or just changing it.
$account = $o_database->read(
	'email, account_type',
	'users',
	'WHERE id = ?',
	[$_GET['id']]
);

// If account type is 0 (unverified), change it to 1 (standard member).
$account_type = $account[0]['account_type'];

if ($account_type == 0) {
    $account_type = 1;
}

// Create a welcome post on social page if user has verified e-mail for the first time.
if (is_null($account[0]['email'])) {
    $o_post->create($_GET['id'], null, 10);
}

// Mark key as used.
$o_database->update(
	'used_ip, used_datetime',
	'key_email',
	'WHERE id = ?',
	[$currentIP, $currentDatetime, $rowKey[0]['id']]
);

// Show a successful system message for the new user.
if (is_null($account[0]['email'])) {
    // Set a new e-mail address for a user and update the registration date.
    $o_database->update(
    	'email, registration_datetime, account_type',
    	'users',
    	'WHERE id = ?',
    	[$rowKey[0]['email'], $currentDatetime, $account_type, $_GET['id']]
    );

    // Show successful system message about e-mail has been verified.
    $o_system->setMessage(
        'success',
        'Your e-mail address has been verified successfully. You can now start using your account.'
    );
}
// Show a successful system message for a user that is changing his e-mail.
else {
    // Set a new e-mail address for a user.
    $o_database->update(
    	'email, account_type',
    	'users',
    	'WHERE id = ?',
    	[$rowKey[0]['email'], $account_type, $_GET['id']]
    );

    // Show successful system message about e-mail has been verified.
    $o_system->setMessage(
        'success',
        'Your e-mail address has been changed successfully.'
    );
}

// Check if user is already logged in.
if ($o_session->verify()) {
    // Change user's e-mail address in the session.
    $_SESSION['user']['email'] = $rowKey[0]['email'];

    // Redirect logged user to the social page.
    header('Location: social/');
    die();
}

// Redirect not-logged user into login page.
header('Location: login.php');
die();
