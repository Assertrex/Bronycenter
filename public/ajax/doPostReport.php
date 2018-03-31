<?php

$isAJAXCall = true;
$loginRequired = true;
$readonlyDenied = true;

// Require system initialization code
require_once('../../application/partials/init.php');

// Use a Post class for handling users posts
use BronyCenter\Post;
$o_post = Post::getInstance();

// Report a post
$status = $o_post->doReport($_POST['id'], $_POST['category'], $_POST['reason']);

// Prepare array with result
if ($status === true) {
    $JSON = [
        'status' => 'success'
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
