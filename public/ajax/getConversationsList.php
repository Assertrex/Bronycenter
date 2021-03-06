<?php
$pageSettings = [
    'isAJAXCall' => true,
    'readonlyDenied' => false,
    'loginRequired' => true,
    'moderatorRequired' => false,
];

require_once('../../application/partials/init.php');

// Get a list of an available conversations
$conversations = $o_message->getConversationsArray();

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
