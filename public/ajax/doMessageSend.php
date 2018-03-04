<?php

// Allow access only for logged users
$loginRequired = true;

// Require system initialization code
require_once('../../application/partials/init.php');

// Try to get recent messages from a conversation
$result = $o_message->doSend($_POST['id'], $_POST['message'], $websiteEncryptionKey);

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
