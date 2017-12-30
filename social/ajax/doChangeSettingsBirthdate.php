<?php

// Allow access only for logged users
$loginRequired = true;

// Require system initialization code
require_once('../../system/partials/init.php');

// Try to change user's display name
$newValue = $user->changeUserBirthdate($_POST['day'], $_POST['month'], $_POST['year']);

// Return a new value
if ($newValue != false) {
    echo $newValue;
}

// TODO Return error in JSON if not
