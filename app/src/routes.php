<?php
// Routes

define('PASSORD_SECRET_HASH', mcrypt_create_iv(22, MCRYPT_DEV_URANDOM));
define('MAX_TASK_PER_USER', 2);

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;


$app->group('/api', function () {

	//USER//
    $this->post('/user/authenticate', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

		$db = DB::getInstance();

		$data = array( 'success' => false);

		//GET POST DATA
		$request_data = json_decode($request->getBody()->getContents());

		$list = $db->user("email = ?", $request_data->username)->fetch();

		if( $list ){
			if(password_verify($request_data->password, $list['password'])){
				unset($list['password']);
				$data['success'] = true;
				$data['user'] = $list;
			}
		}

		echo json_encode($data);
	    return $response->withHeader('Content-type', 'application/json');

    });

    $this->get('/user/{id}', function ($request, $response, $args) {

		$db = DB::getInstance();

		$data = array( 'success' => false);

		$user = $db->user("id = ?", $args['id'])->fetch();

		if($user){
			$data['success'] = true;

			$data['user_data'] = array(
				'id' => $user['id'],
				'name' => $user['name'],
				'email' => $user['email'],
				'score' => $user['score']
				);

			// get current tasks
			$tasks = $db->user_task();
			
			$data['task_list'] = array();

			foreach ($tasks as $t) {
			    $data['task_list'][] = $t->task;
			}

		}else{
			$data['message'] = "User not valid";
		}

		echo json_encode($data);
	    return $response->withHeader('Content-type', 'application/json');

    });

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
