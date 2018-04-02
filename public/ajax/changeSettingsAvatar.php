<?php
$pageSettings = [
    'isAJAXCall' => true,
    'readonlyDenied' => true,
    'loginRequired' => true,
    'moderatorRequired' => false,
];

require_once('../../application/partials/init.php');

// Require a class for handling avatar creations
require_once('../../application/core/image.php');

// Create an avatar and generate a hash for it
$hash = $user->changeAvatar($_FILES['avatar']);

// Return a new value
if ($hash != false) {
    echo $hash;
}

// TODO Return error in JSON if not
