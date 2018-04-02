<?php
$pageSettings = [
    'isAJAXCall' => true,
    'readonlyDenied' => true,
    'loginRequired' => true,
    'moderatorRequired' => false,
];

require_once('../../application/partials/init.php');

// Try to change user's password
$account = BronyCenter\Account::getInstance();
$account->changePassword($_POST['currentpassword'], $_POST['newpassword'], $_POST['newpasswordrepeat']);

// TODO Return error in JSON if not
