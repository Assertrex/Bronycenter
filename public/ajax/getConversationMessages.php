<?php

// Allow access only for logged users
$loginRequired = true;

// Require system initialization code
require_once('../../application/partials/init.php');

// Try to get recent messages from a conversation
$messages = $o_message->getConversationMessages($_GET['id'], $messageEncryptionKey);

// Prepare array with result
if (!empty($messages)) {
    $JSON = [
        'status' => 'success',
        'messages' => $messages
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
