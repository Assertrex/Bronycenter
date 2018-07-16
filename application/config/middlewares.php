<?php

// Handle a trailing slash in URL
$application->add((new \Middlewares\TrailingSlash(false))->redirect(true));

// Add a middleware for getting an IP address
$application->add(new \RKA\Middleware\IpAddress(true, ['10.0.0.1', '10.0.0.2']));

// Add a middleware for handling user sessions
$application->add(new \Slim\Middleware\Session([
    'name' => $container['settings']['session']['name'],
    'lifetime' => $container['settings']['session']['lifetime'],
    'autorefresh' => true
]));
