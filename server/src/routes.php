<?php
// Routes

define('MAX_TASK_PER_USER', 2);

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

require 'classes/database.php';
require 'classes/task.php';
require 'classes/user.php';
require 'classes/secure.php';
require 'classes/helpers.php';
require 'classes/auth.php';


$app->group(SERVER_URL.'/api', function () use ( $RequireAuth ) {

	//USER//

	$this->post('/user', 'User:Create');

    $this->post('/user/authenticate', 'User:Auth');

    $this->get('/user/{id}', 'User:Find')->add( $RequireAuth );

    //TASKS//
    $this->post('task/create', 'Task:Create');
    
    $this->get('/task', 'Task:GetAll')->add( $RequireAuth );

    $this->get('/task/{id}', 'Task:Find')->add( $RequireAuth );

    $this->post('/task/accept', 'Task:Accept')->add( $RequireAuth );
    
    $this->post('/task/complete', 'Task:Complete')->add( $RequireAuth );

    $this->post('/task/validate', 'Task:Validate')->add( $RequireAuth );

});

?>
