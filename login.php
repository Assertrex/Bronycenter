<?php
// Include system initialization code
require('system/partials/init.php');

if (!empty($_POST['login-submit'])) {
    // Get stored flash messages from session
    $account = BronyCenter\Account::getInstance();
    $loginStatus = $account->login([
        'username' => $_POST['login-username'],
        'password' => $_POST['login-password']
    ]);

    if ($loginStatus) {
        // Display flash message notification about website
        $flash->info(
            'Please note, that BronyCenter is currently in early development stage.<br />' .
            'Many features will be added/changed in the future. Website design will change as well.'
        );

        // Redirect user into the social part of a website
        header('Location: social/');
        die();
    }

    // Redirect user into the login page again
    header('Location: index.php'); // TODO TO LOGIN PAGE!!!
    die();
}
