<?php
// Routes

define('MAX_TASK_PER_USER', 2);

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;


$app->group('/api', function () {

	//USER//
    
	//create user
	$this->post('/user', 'User:Create');

    $this->post('/user/authenticate', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

		$db = DB::getInstance();

		$result = array( 'success' => false, 'message' => 'Couple email/mot de passe invalide');

		//GET POST DATA
		$request_data = json_decode($request->getBody()->getContents());

		$user = $db->user("email = ?", $request_data->username)->fetch();

		if( $user ){
			if(password_verify($request_data->password, $user['password'])){

				//dont send password
				unset($user['password']);
					
				//create & save token
				$user['token'] = Secure::GenerateToken();
				$user->update();
				
				$result = array ('success' => true, 'user' => $user);
			}
		}

		echo json_encode($result);
	    return $response->withHeader('Content-type', 'application/json');

    });

    $this->get('/user/{id}', 'User:Find');

    //TASKS//

    $this->get('/task', function ($request, $response, $args) {

		$db = DB::getInstance();
		
		$data = array( 
			'success' => true,
			'tasks' => $db->task('state = ?', 1)
			);

	    echo json_encode($data);

	    return $response->withHeader('Content-type', 'application/json');

    });

    $this->get('/task/{id}', function ($request, $response, $args) {

		$db = DB::getInstance();
		
		$id = $args['id'];

		$data = array( 'success' => false);

	 	$t = $db->task("id = ?", $id)->fetch();

	 	if( $t ){
	 		$data = array( 'success' => true, 'task' => $t);

	 		//verifier si l'utilisateur courant n'as pas choisi cette tache
		 	$user = $request->getHeader('PHP_AUTH_USER');
		 	$pw = $request->getHeader('PHP_AUTH_PW');

		 	$user_id = $db->user("email = ?", $user)->select('id')->fetch('id');
			
			$exist = $t->user_task()->fetch();

			if($exist){
				$data['task']['accepted'] = $exist['user_id'] == $user_id;
			}else{
				$data['task']['accepted'] = false;
			}
	 	}

	    echo json_encode($data);

	    return $response->withHeader('Content-type', 'application/json');

    });

    $this->post('task/create', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

		$db = DB::getInstance();
		
		$task_info = $request->getParsedBody();
	    
	    $task_info['date'] = date();
	    
	    $result = $db->task->insert($task_info);

	    echo json_encode($result);

	    return $response->withHeader('Content-type', 'application/json');

    });
    
    $this->post('/task/accept', function ($request, $response, $args) {

		$db = DB::getInstance();

		//GET POST DATA
		$request_data = json_decode($request->getBody()->getContents());
		
		$data = array( 'success' => false);

		$task = $db->task('id = ?', $request_data->taskId)->fetch();

		//la tache peut etre acceptée
		if($task['state'] == 1){
			
			//l'utilisateur n'as pas trop de taches en cours
			$current = $db->user_task('user_id = ?',$request_data->userId)->count("*");

			if ($current < MAX_TASK_PER_USER){
				$task['state'] = 2;
				$task->update();
				
				$db->user_task->insert(array(
				    "user_id" => $request_data->userId,
				    "task_id" => $request_data->taskId
				));

				$data['success'] = true;

			}else{
				$data['message'] = "You cannot accept more tasks";
			}

		}else{
			$data['message'] = "This task is not available";
		}

	    echo json_encode($data);

	    return $response->withHeader('Content-type', 'application/json');

    });

    
    $this->post('/task/complete', function ($request, $response, $args) {

		$db = DB::getInstance();

		$email = $request->getHeader('PHP_AUTH_USER');
	 	$pw = $request->getHeader('PHP_AUTH_PW');

	 	$userId = $db->user("email = ?", $email)->select('id')->fetch('id');

		//GET POST DATA
		//$request_data = $request->getParsedBody();
		$request_data = json_decode($request->getBody()->getContents());
		
		$data = array( 'success' => false);

		$task = $db->task('id = ?', $request_data->taskId)->fetch();

		//la tache peut etre complettée
		if($task['state'] == 2){
			
			//on change l'etat de la tache
			$task['state'] = 3;
			$task->update();
			
			$data['success'] = true;
			$data['message'] = "Task Updated";
		}else{
			$data['message'] = "This task is not available";
		}

	    echo json_encode($data);

	    return $response->withHeader('Content-type', 'application/json');

    });
	

    $this->post('/task/validate', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

		$db = DB::getInstance();

		//GET POST DATA
		$request_data = $request->getParsedBody();

		$data = array( 'success' => false);

		if($request_data){

			$task = $db->task('id = ?', $request_data['taskId'])->fetch();

			if($task){
				if($task['mail'] == $request_data['mail'] && $task['state'] != 4){

					//on change l'etat de la tache
					$task['state'] = 4;
					$task->update();
					

					//give user points
					$row = $task->user_task();

					if( $row[0] ){
						$user = $db->user('id = ?', $row[0]['user_id'])->fetch();
						$user['score'] += 10;
						$user->update();

						$row->delete();
					}

					$data['success'] = true;
					$data['message'] = "Task validated";
				}else{

					$data['success'] = false;
					$data['message'] = "You cannot validate this task";
				}

			}else{
				$data['message'] = "Invalid task";
			}
		}

	    echo json_encode($data);

	    return $response->withHeader('Content-type', 'application/json');

    });

});

$app->any('/[*]', function (ServerRequestInterface $request, ResponseInterface $response, $args){

    return $this->renderer->render($response, 'index.phtml', $args);

});
?>
