<?php

$isAJAXCall = true;
$loginRequired = true;
$readonlyDenied = true;

// Require system initialization code
require_once('../../application/partials/init.php');

// Try to change user's display name
$newValue = $user->changeUserDisplayname($_POST['value']);

// Return a new value
if ($newValue != false) {
    echo $newValue;
}

// TODO Return error in JSON if not
