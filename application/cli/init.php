<?php

// Display a console message about initializing system
echo PHP_EOL;
echo 'Loading required classes...' . PHP_EOL;

// Include all required classes
require(__DIR__ . '/../core/config.php');
require(__DIR__ . '/../core/database.php');
require(__DIR__ . '/../core/flash.php');
require(__DIR__ . '/../core/utilities.php');
require(__DIR__ . '/../core/validator.php');
require(__DIR__ . '/../core/user.php');
require(__DIR__ . '/../core/post.php');
require(__DIR__ . '/../core/statistics.php');

// Create instances of required classes
$class_config = BronyCenter\Config::getInstance();
$class_database = BronyCenter\Database::getInstance();
$class_flash = BronyCenter\Flash::getInstance();
$class_utilities = BronyCenter\Utilities::getInstance();
$class_validator = BronyCenter\Validator::getInstance();
$class_user = BronyCenter\User::getInstance();
$class_post = BronyCenter\Post::getInstance();
$class_statistics = BronyCenter\Statistics::getInstance();

// Display a console message about starting executing a code
echo 'Starting a CLI script...' . PHP_EOL;
