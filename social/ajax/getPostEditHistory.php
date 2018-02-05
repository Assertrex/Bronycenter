<?php

// Allow access only for logged users
$loginRequired = true;

// Require system initialization code
require_once('../../system/partials/init.php');

// Use a Post class for handling users posts
use BronyCenter\Post;
$o_post = Post::getInstance();

// Get an array of post edits
$edit_history = $o_post->getEditHistory($_POST['id']);

// Prepare array with result
if ($edit_history !== false) {
    // Escape HTML characters in post content
    for ($i = 0; $i < count($edit_history); $i++) {
        $edit_history[$i]['content'] = $utilities->doEscapeString($edit_history[$i]['content']);
        $edit_history[$i]['datetime_interval'] = $utilities->getDateIntervalString($utilities->countDateInterval($edit_history[$i]['datetime']));
    }

    $JSON = [
        'status' => 'success',
        'edit_history' => $edit_history
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
