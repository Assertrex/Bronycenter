<?php
$pageSettings = [
    'isAJAXCall' => true,
    'readonlyDenied' => true,
    'loginRequired' => true,
    'moderatorRequired' => true,
];

require_once('../../application/partials/init.php');

use BronyCenter\Post;
$o_post = Post::getInstance();

$postID = intval($_POST['id']);
$methodStatus = $o_post->doApprove($postID);

if ($methodStatus) {
    $AJAXCallJSON = [
        'status' => 'success',
        'resultMessage' => $o_translation->getString('ajax', 'postApproved'),
        'data' => [
            'postID' => $postID
        ],
    ];
} else {
    $AJAXCallJSON = [
        'status' => 'error',
        'resultMessage' => $o_translation->getString('ajax', 'unknownError'),
    ];
}

die($utilities->encodeJSON($AJAXCallJSON));
