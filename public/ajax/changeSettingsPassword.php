<?php

// Allow access only for logged users
$loginRequired = true;

// Require system initialization code
require_once('../../application/partials/init.php');

// Check if new password is same as it's repeated form
if ($_POST['newpassword'] != $_POST['repeatpassword']) {
    $flash->error('You haven\'t repeated new password correctly.');
    return false;
}

// Try to change user's password
$account = BronyCenter\Account::getInstance();
$account->changePassword($_POST['oldpassword'], $_POST['newpassword']);

// TODO Return error in JSON if not
