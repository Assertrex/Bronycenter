<?php
// Include system initialization code
require('../application/partials/init.php');

// Create an instance of an account class for managing accounts
$o_account = BronyCenter\Account::getInstance();

// Check if a valid e-mail related function has been called
switch ($_GET['func'] ?? null) {
    // Try to verify an e-mail address
    case 'verify':
        if ($o_account->doVerifyEmail($_GET['id'], $_GET['token'])) {
            $flash->success(
                'Your account has been successfully activated. You can now log in, just remember to be nice and to Love & Tolerate. :3<br />
                If you have any questions, suggestions or problems, don\'t be afraid to contact with an administrator. He\'ll be happy to meet you and to lend a helping hoof.'
            );
            $utilities->redirect('login.php');
        }
    default:
        $flash->error('You\'ve been redirected to the main page, because you\'ve tried to access a page for managing e-mail address, but your link seems to be invalid.');
        $utilities->redirect('index.php');
}
