<?php
$pageSettings = [
    'isAJAXCall' => true,
    'readonlyDenied' => true,
    'loginRequired' => true,
    'moderatorRequired' => false,
];

require_once('../../application/partials/init.php');
require_once('../../application/core/image.php');

$o_account = BronyCenter\Account::getInstance();
$methodStatus = $o_account->changeAvatar($_FILES['avatar']);

if ($methodStatus) {
    $AJAXCallJSON = [
        'status' => 'success',
        'resultMessage' => $o_translation->getString('ajax', 'avatarChanged'),
        'data' => [
            'avatar' => $_SESSION['user']['avatar'],
        ],
    ];
} else {
    $AJAXCallJSON = [
        'status' => 'error',
        'resultMessage' => $o_translation->getString('ajax', 'unknownError'),
    ];
}

die($utilities->encodeJSON($AJAXCallJSON));
