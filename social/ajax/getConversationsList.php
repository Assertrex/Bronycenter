<?php

// Allow access only for logged users
$loginRequired = true;

// Require system initialization code
require_once('../../system/partials/init.php');

// Get a list of an available conversations
$conversations = $o_message->getConversations($messageEncryptionKey);

// Prepare array with result
if (!empty($conversations)) {
    $JSON = [
        'status' => 'success',
        'conversations' => $conversations
    ];
} else {
    $JSON = [
        'status' => 'error'
    ];
}

// Format array into JSON
$JSON = json_encode($JSON);

// Display JSON result
echo $JSON;
