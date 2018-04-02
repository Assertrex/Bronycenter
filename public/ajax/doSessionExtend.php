<?php

require('../../application/core/config.php');
require('../../application/core/translation.php');
require('../../application/core/database.php');
require('../../application/core/session.php');
require('../../application/core/utilities.php');
require('../../application/core/flash.php');

session_start();

$o_translation = BronyCenter\Translation::getInstance();
$o_session = BronyCenter\Session::getInstance();
$o_flash = BronyCenter\Flash::getInstance();

$AJAXCallJSON = [
    'status' => 'error',
    'errorMessage' => $o_translation->getString('ajax', 'unknownError'),
];

if ($o_session->verify()) {
    $AJAXCallJSON = [
        'status' => 'success',
        'data' => [
            'isLoggedIn' => true,
            'userId' => $_SESSION['account']['id'],
        ],
    ];
} else {
    $AJAXCallJSON = [
        'status' => 'success',
        'data' => [
            'isLoggedIn' => false,
        ],
    ];

    $o_session->destroy();
}

die(json_encode($AJAXCallJSON, JSON_UNESCAPED_UNICODE));
