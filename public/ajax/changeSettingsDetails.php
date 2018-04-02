<?php
$pageSettings = [
    'isAJAXCall' => true,
    'readonlyDenied' => true,
    'loginRequired' => true,
    'moderatorRequired' => false,
];

require_once('../../application/partials/init.php');

// Try to change a selected user details value
$newValue = $user->changeSettingsDetails($_POST['field'], $_POST['value']);

// Return a new value
if ($newValue != false) {
    echo $newValue;
}

// TODO Return error in JSON if not
