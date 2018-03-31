<?php

$isAJAXCall = true;
$loginRequired = true;
$readonlyDenied = true;

// Require system initialization code
require_once('../../application/partials/init.php');

// Try to change user's password
$account = BronyCenter\Account::getInstance();
$account->changePassword($_POST['currentpassword'], $_POST['newpassword'], $_POST['newpasswordrepeat']);

// TODO Return error in JSON if not
