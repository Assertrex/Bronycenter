<?php

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;
use Slim\Container;

// Include Composer's autoloader
require_once '../vendor/autoload.php';

// Turn on PHP errors reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Load website settings (from .env file)
$dotEnv = new Dotenv\Dotenv('..');
$dotEnv->load();

// Set a timezone for PHP to use (from .env file)
date_default_timezone_set($_ENV['PHP_DEFAULT_TIMEZONE']);

// Create a Slim container with defined settings (from .env file)
$container = new Container([
    'settings' => [
        'displayErrorDetails' => true,
        'determineRouteBeforeAppMiddleware' => false,
        'doctrine' => [
            'dev_mode' => true,
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
        ]
    ]
]);

$container[EntityManager::class] = function (Container $container): EntityManager {
    $config = Setup::createAnnotationMetadataConfiguration(
        $container['settings']['doctrine']['metadata_dirs'],
        $container['settings']['doctrine']['dev_mode']
    );

    $config->setMetadataDriverImpl(
        new AnnotationDriver(
            new AnnotationReader,
            $container['settings']['doctrine']['metadata_dirs']
        )
    );

    $config->setMetadataCacheImpl(
        new FilesystemCache(
            $container['settings']['doctrine']['cache_dir']
        )
    );

    return EntityManager::create(
        $container['settings']['doctrine']['connection'],
        $config
    );
};

return $container;
