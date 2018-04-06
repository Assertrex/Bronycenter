<?php
$pageSettings = [
    'isAJAXCall' => true,
    'readonlyDenied' => true,
    'loginRequired' => true,
    'moderatorRequired' => false,
];

require_once('../../application/partials/init.php');

use BronyCenter\Post;
$o_post = Post::getInstance();

$methodStatus = $o_post->doReport($_POST['id'], $_POST['category'], $_POST['reason']);

if ($methodStatus) {
    $AJAXCallJSON = [
        'status' => 'success',
        'resultMessage' => $o_translation->getString('ajax', 'postReported'),
        'data' => [
            'postID' => intval($_POST['id'])
        ],
    ];
} else {
    $AJAXCallJSON = [
        'status' => 'error',
        'resultMessage' => $o_translation->getString('ajax', 'unknownError'),
    ];
}

die($utilities->encodeJSON($AJAXCallJSON));
