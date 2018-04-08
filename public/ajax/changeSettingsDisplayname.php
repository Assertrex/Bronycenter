<?php
$pageSettings = [
    'isAJAXCall' => true,
    'readonlyDenied' => true,
    'loginRequired' => true,
    'moderatorRequired' => false,
];

require_once('../../application/partials/init.php');

$o_account = BronyCenter\Account::getInstance();
$methodStatus = $o_account->changeDisplayname($_POST['value']);

if ($methodStatus) {
    $userDetails = $user->getUserDetails($_SESSION['account']['id'], []);

    $AJAXCallJSON = [
        'status' => 'success',
        'resultMessage' => $o_translation->getString('ajax', 'displaynameChanged'),
        'data' => [
            'displayname' => $userDetails['display_name'],
        ],
    ];
} else {
    $AJAXCallJSON = [
        'status' => 'error',
        'resultMessage' => $o_translation->getString('ajax', 'unknownError'),
    ];
}

die($utilities->encodeJSON($AJAXCallJSON));
