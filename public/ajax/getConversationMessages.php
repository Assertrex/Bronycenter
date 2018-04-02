<?php
$pageSettings = [
    'isAJAXCall' => true,
    'readonlyDenied' => false,
    'loginRequired' => true,
    'moderatorRequired' => false,
];

require_once('../../application/partials/init.php');

// Try to get recent messages from a conversation
$messages = $o_message->getMessages($_GET['conversation_id'], $_GET['messages_limit'], $_GET['messages_last_id'] ?? null);

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
