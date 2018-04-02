<?php

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('../../application/core/config.php');
require('../../application/core/translation.php');
require('../../application/core/utilities.php');
require('../../application/core/database.php');
require('../../application/core/flash.php');
require('../../application/core/session.php');

$session = BronyCenter\Session::getInstance();

if ($session->verify()) {
    echo 'true';
} else {
    echo 'false';
}
