<?php
$pageSettings = [
    'isAJAXCall' => true,
    'readonlyDenied' => true,
    'loginRequired' => true,
    'moderatorRequired' => false,
];

require_once('../../application/partials/init.php');

// Try to get recent messages from a conversation
$result = $o_message->doMessageSend($_POST['id'], $_POST['message'], $websiteEncryptionKey);

// Prepare array with result
if (!empty(intval($result))) {
    $JSON = [
        'status' => 'success',
        'messageID' => intval($result)
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
