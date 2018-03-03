<?php

// Allow access only for logged users
$loginRequired = true;

// Require system initialization code
require_once('../../application/partials/init.php');

// Create an instance of a database class to use CRUD operations on database
$database = BronyCenter\Database::getInstance();

// Store LIKE question in a variable
$likeQuestion = '%' . $_GET['name'] . '%';

// Get an array of matched users
$matchedUsers = $database->read(
    'id, display_name, username, avatar, last_online',
    'users',
    'WHERE (display_name LIKE ? OR username LIKE ?) AND account_type != 0 AND account_standing NOT IN (8, 9) ORDER BY last_online DESC LIMIT 10',
    [$likeQuestion, $likeQuestion]
);

for ($i = 0; $i < count($matchedUsers); $i++) {
    // Show default avatar if user don't have it
    if (is_null($matchedUsers[$i]['avatar'])) {
        $matchedUsers[$i]['avatar'] = 'default';
    }

    // Format activity datetimes
    $matchedUsers[$i]['last_online'] = $utilities->getDateIntervalString($utilities->countDateInterval($matchedUsers[$i]['last_online']));
}

// Prepare array with result
if (!empty($matchedUsers)) {
    $JSON = [
        'status' => 'success',
        'users' => $matchedUsers
    ];
} else {
    $JSON = [
        'status' => 'error',
        'users' => []
    ];
}

// Format array into JSON
$JSON = json_encode($JSON);

// Display JSON result
echo $JSON;

?>
