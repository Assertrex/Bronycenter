<?php

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;

// Register a view service using Twig templating engine
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('../application/views', [
        'cache' => $container['settings']['twig']['cache'],
        'debug' => $container['settings']['twig']['debug'],
        'strict_variables' => $container['settings']['twig']['strict_variables'],
    ]);

    // Instantiate and add Twig extension to a view
    $view->addExtension(
        new \Slim\Views\TwigExtension($container['router'], $container['request']->getUri()->getBasePath())
    );

    // Add debug extension to Twig if debug mode has been enabled
    if ($container['settings']['twig']['debug']) {
        $view->addExtension(
            new Twig_Extension_Debug()
        );
    }

    return $view;
};

// Register an entity manager service using Doctrine ORM
$container[EntityManager::class] = function ($container): EntityManager {
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

// Register a flash messages service
$container['flash'] = function () {
    return new \Slim\Flash\Messages();
};

// Register a user session helper
$container['session'] = function () {
    return new \SlimSession\Helper();
};

// Register a PSR-15 middleware support
$container['callableResolver'] = function ($container) {
    return new \Bnf\Slim3Psr15\CallableResolver($container);
};
