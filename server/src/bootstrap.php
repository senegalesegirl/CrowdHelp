<?php 


define('PASSORD_SECRET_HASH', mcrypt_create_iv(22, MCRYPT_DEV_URANDOM));

require APP . '/vendor/autoload.php';

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

// Instantiate the app
$settings = require APP . '/src/settings.php';


?>