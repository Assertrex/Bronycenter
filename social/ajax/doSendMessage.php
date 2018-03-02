<?php

// Allow access only for logged users
$loginRequired = true;

// Require system initialization code
require_once('../../system/partials/init.php');

// Try to send a message
$messageID = $o_message->doSend($_POST['id'], $_POST['content'], $messageEncryptionKey);

// Prepare array with result
if (!empty($messageID)) {
    $JSON = [
        'status' => 'success',
        'messageID' => $messageID
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
