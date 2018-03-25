<?php

// Allow access only for logged users
$loginRequired = true;

// Require system initialization code
require_once('../../application/partials/init.php');

// Try to get recent messages from a conversation
$messages = $o_message->getConversationMessages($_GET['conversation_id'], $_GET['messages_limit'], $websiteEncryptionKey, $_GET['messages_last_id'] ?? null);

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
