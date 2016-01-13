<?php

define('APP', __DIR__ );


define('SERVER_URL', $_SERVER['HTTP_HOST'].'/CrowdHelp/CrowdHelp' );

echo SERVER_URL;

//load config
require APP . '/src/bootstrap.php';

class Api extends \Slim\App{

	private $extras;
	
	public static $instance;

	function __construct($settings){
		parent::__construct($settings);

		$more = array();

		self::$instance = $this;
	}

	public static function getInstance(){
		return self::$instance;
	}

	public function setExtra($key, $value){
		$extras[$key] = $value;
	}

	public function getExtra($key){
		return (!empty($extras[$key])) ? $extras[$key] : false;
	}

}

//init app
$app = new Api($settings);

// Set up dependencies
require APP . '/src/dependencies.php';

// Register middleware
require APP . '/src/middleware.php';

// Register routes
require APP . '/src/routes.php';

// Run app
$app->run();
