<?php
$pageSettings = [
    'isAJAXCall' => true,
    'readonlyDenied' => true,
    'loginRequired' => true,
    'moderatorRequired' => false,
];

require_once('../../application/partials/init.php');

$o_account = BronyCenter\Account::getInstance();
$methodStatus = $o_account->changePassword($_POST['currentpassword'], $_POST['newpassword'], $_POST['newpasswordrepeat']);

if ($methodStatus) {
    $AJAXCallJSON = [
        'status' => 'success',
        'resultMessage' => $o_translation->getString('ajax', 'passwordChanged'),
    ];
} else {
    $AJAXCallJSON = [
        'status' => 'error',
        'resultMessage' => $o_translation->getString('ajax', 'unknownError'),
    ];
}

die($utilities->encodeJSON($AJAXCallJSON));
