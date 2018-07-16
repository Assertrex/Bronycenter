<?php

// Fix .htaccess rewrite path
$_SERVER['SCRIPT_NAME'] = str_replace('/public', '', $_SERVER['SCRIPT_NAME']);

// Include PHP file containing re-usable configuration code
require_once('../application/config/bootstrap.php');

// Instantiate Slim application
$application = new \Slim\App([
    'settings' => $slimSettings
]);

// Get a default Dependency Container from Slim
$container = $application->getContainer();

// Include PHP file containing uncommon additional functions
require_once('../application/functions.php');

// Include PHP files containing middlewares, registered services and defined routes
require_once('../application/config/services.php');
require_once('../application/config/middlewares.php');
require_once('../application/config/routes.php');

// Run an application
$application->run();
