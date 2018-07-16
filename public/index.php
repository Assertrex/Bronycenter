<?php

// Include Composer's autoloader
require_once('../vendor/autoload.php');

// Fix .htaccess rewrite path
$_SERVER['SCRIPT_NAME'] = str_replace('/public', '', $_SERVER['SCRIPT_NAME']);

// Load website settings (from .env file)
$dotEnv = new Dotenv\Dotenv('..');
$dotEnv->load();

// Set a timezone for PHP to use (from .env file)
date_default_timezone_set($_ENV['PHP_DEFAULT_TIMEZONE']);

// Turn on/off all errors reporting for PHP (from .env file)
if ($_ENV['PHP_ENABLE_ERRORS'] === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
}

// Define Slim settings (from .env file)
$slimSettings = [
    'displayErrorDetails' => ($_ENV['SLIM_ENABLE_ERRORS'] === 'true') ? true : false,
    'determineRouteBeforeAppMiddleware' => false,
    'doctrine' => [
        'dev_mode' => ($_ENV['DOCTRINE_DEV_MODE'] === 'true') ? true : false,
        'cache_dir' => '../cache/doctrine',
        'metadata_dirs' => [
            '../application/models'
        ],
        'connection' => [
            'driver' => (!empty($_ENV['DOCTRINE_DB_DRIVER'])) ? $_ENV['DOCTRINE_DB_DRIVER'] : 'pdo_mysql',
            'host' => (!empty($_ENV['DOCTRINE_DB_HOSTNAME'])) ? $_ENV['DOCTRINE_DB_HOSTNAME'] : 'localhost',
            'port' => (!empty(intval($_ENV['DOCTRINE_DB_PORT']))) ? $_ENV['DOCTRINE_DB_PORT'] : 3306,
            'dbname' => $_ENV['DOCTRINE_DB_DATABASE'],
            'user' => $_ENV['DOCTRINE_DB_USERNAME'],
            'password' => $_ENV['DOCTRINE_DB_PASSWORD'],
            'charset' => (!empty($_ENV['DOCTRINE_DB_CHARSET'])) ? $_ENV['DOCTRINE_DB_CHARSET'] : 'utf8mb4'
        ]
    ],
    'twig' => [
        'debug' => ($_ENV['TWIG_ENABLE_DEBUG'] === 'true') ? true : false,
        'cache' => ($_ENV['TWIG_CACHE_VIEWS'] === 'true') ? '../cache/twig' : false,
        'strict_variables' => ($_ENV['TWIG_STRICT_VARIABLES'] === 'true') ? true : false
    ],
    'session' => [
        'name' => (!empty($_ENV['SESSION_NAMESPACE'])) ? $_ENV['SESSION_NAMESPACE'] : 'slim_session',
        'lifetime' => (!empty($_ENV['SESSION_LIFETIME'])) ? $_ENV['SESSION_LIFETIME'] : '20 minutes',
    ]
];

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
