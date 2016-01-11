<?php

define('APP', __DIR__ . '/app');

//load config
require APP . '/src/bootstrap.php';

//init app
$app = new \Slim\App($settings);

// Set up dependencies
require APP . '/src/dependencies.php';

// Register middleware
require APP . '/src/middleware.php';

// Register routes
require APP . '/src/routes.php';

// Run app
$app->run();
