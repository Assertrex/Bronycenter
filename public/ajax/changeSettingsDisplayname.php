<?php
$pageSettings = [
    'isAJAXCall' => true,
    'readonlyDenied' => true,
    'loginRequired' => true,
    'moderatorRequired' => false,
];

require_once('../../application/partials/init.php');

// Try to change user's display name
$newValue = $user->changeUserDisplayname($_POST['value']);

// Return a new value
if ($newValue != false) {
    echo $newValue;
}

// TODO Return error in JSON if not
