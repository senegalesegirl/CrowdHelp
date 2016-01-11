<?php

define('APP', __DIR__ . '/app');

require APP . '/vendor/autoload.php';

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

require APP . '/src/DB.php';
require APP . '/src/controller/task.ctrl.php';

// Instantiate the app
$settings = require APP . '/src/settings.php';

$app = new \Slim\App($settings);

// Set up dependencies
require APP . '/src/dependencies.php';

// Register middleware
require APP . '/src/middleware.php';

// Register routes
require APP . '/src/routes.php';

// Run app
$app->run();
