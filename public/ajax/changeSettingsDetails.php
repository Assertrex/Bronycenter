<?php

// Allow access only for logged users
$loginRequired = true;

// Require system initialization code
require_once('../../application/partials/init.php');

// Try to change a selected user details value
$newValue = $user->changeSettingsDetails($_POST['field'], $_POST['value']);

// Return a new value
if ($newValue != false) {
    echo $newValue;
}

// TODO Return error in JSON if not
