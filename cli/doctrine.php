<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

// Include PHP file containing CLI configuration code
$container = require_once('config/bootstrap.php');

// Run a CLI Doctrine application
ConsoleRunner::run(
    ConsoleRunner::createHelperSet($container[EntityManager::class])
);
