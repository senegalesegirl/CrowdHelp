<?php


class Task {

	private $db;

	function __construct(){
		$this->db = DB::getInstance();
	}

	public function GetAll($request, $response, $args) {
		
		$data = array( 
			'success' => true,
			'tasks' => $this->db->task('state = ?', "1")
		);

	    echo json_encode($data);

	    return $response->withHeader('Content-type', 'application/json');

    }

    public function Find($request, $response, $args) {
		
		//userid
		$user_id = Api::getInstance()->getExtra('user_id');

		//get the task id
		$id = $args['id'];

		$data = array( 'success' => false);

		//fetch task data
	 	$t = $this->db->task("id = ?", $id)->fetch();

	 	if( $t ){
	 		$data = array( 'success' => true, 'task' => $t);

	 		//verifier si l'utilisateur courant n'as pas déja choisi cette tache
			$exist = $t->user_task()->fetch();

			if($exist){
				$data['task']['accepted'] = ($exist['user_id'] == $user_id);
			}else{
				$data['task']['accepted'] = false;
			}
	 	}

	    echo json_encode($data);

	    return $response->withHeader('Content-type', 'application/json');

    }

    public function Create( $request, $response, $args) {

		
		
		$task_info = $request->getParsedBody();
	    
	    $task_info['date'] = date();
	    
	    $result = $this->db->task->insert($task_info);

	    echo json_encode($result);

	    return $response->withHeader('Content-type', 'application/json');

    }

    public function Accept ($request, $response, $args) {

		

		//GET POST DATA
		$request_data = json_decode($request->getBody()->getContents());
		
		$data = array( 'success' => false);

		$task = $this->db->task('id = ?', $request_data->taskId)->fetch();

		//la tache peut etre acceptée
		if($task['state'] == 1){
			
			//l'utilisateur n'as pas trop de taches en cours
			$current = $this->db->user_task('user_id = ?',$request_data->userId)->count("*");

			if ($current < MAX_TASK_PER_USER){
				$task['state'] = 2;
				$task->update();
				
				$this->db->user_task->insert(array(
				    "user_id" => $request_data->userId,
				    "task_id" => $request_data->taskId
				));

				$data['success'] = true;

			}else{
				$data['message'] = "Vous ne pouvez pas accepter plus de taches.";
			}

		}else{
			$data['message'] = "Cette tache n'est pas disponible";
		}

	    echo json_encode($data);

	    return $response->withHeader('Content-type', 'application/json');

    }

    public function Complete ($request, $response, $args) {

		

		$email = $request->getHeader('PHP_AUTH_USER');
	 	$pw = $request->getHeader('PHP_AUTH_PW');

	 	$userId = $this->db->user("email = ?", $email)->select('id')->fetch('id');

		//GET POST DATA
		//$request_data = $request->getParsedBody();
		$request_data = json_decode($request->getBody()->getContents());
		
		$data = array( 'success' => false);

		$task = $this->db->task('id = ?', $request_data->taskId)->fetch();

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

    }

    public function Validate( $request, $response, $args) {

		

		//GET POST DATA
		$request_data = $request->getParsedBody();

		$data = array( 'success' => false);

		if($request_data){

			$task = $this->db->task('id = ?', $request_data['taskId'])->fetch();

			if($task){
				if($task['mail'] == $request_data['mail'] && $task['state'] != 4){

					//on change l'etat de la tache
					$task['state'] = 4;
					$task->update();
					

					//give user points
					$row = $task->user_task();

					if( $row[0] ){
						$user = $this->db->user('id = ?', $row[0]['user_id'])->fetch();
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

    }
}