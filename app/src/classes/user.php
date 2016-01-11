<?php

class User {

	public function Create ($request, $response, $args){

		//init database
		$db = DB::getInstance();

		//gat user data
		$user = json_decode($request->getBody()->getContents());

		if( empty( $user->first_name ) || empty( $user->last_name ) || empty( $user->email ) || empty( $user->password ) ){
			echo "ERROR";
		}else{

			//check email exit ?
			$exists = $db->user('email = ?', $user->email)->fetch();

			if($exists){
				echo json_encode(array('success' => false, 'message'=> 'Email already in use') );
			}else{

				$data = array();

				$data['first_name'] = Helpers::sanitize($user->first_name);
				$data['last_name'] = Helpers::sanitize($user->last_name);
				$data['email'] = Helpers::sanitize($user->email);
				$data['password'] = Helpers::EncrytPassword( Helpers::sanitize($user->password) );

				//insert in db
				$db->user->insert($data);
				echo json_encode(array('success' => true) );
			}

		}


		return $response->withHeader('Content-type', 'application/json');

	}

	public function Find ($request, $response, $args){

		$db = DB::getInstance();

		$data = array( 'success' => false);

		$user = $db->user("id = ?", $args['id'])->fetch();

		if($user){
			$data['success'] = true;

			$data['user'] = array(
				'id' => $user['id'],
				'first_name' => $user['first_name'],
				'last_name' => $user['last_name'],
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
	}
}



class Helpers {

	//encrypt a password
	public static function EncrytPassword($pw){
		
		$options = [
		    'cost' => 12,
		    'salt' => PASSORD_SECRET_HASH,
		];

		return password_hash($pw, PASSWORD_BCRYPT, $options);
	}

	//check a password matches the hash
	public static function CheckPassword($pw, $hash){
		return password_verify($pw, $hash);
	}

	//clean data, remove spaces, special chars
	public static function sanitize($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}

	// validate URL
	public static function validate_url($url){
		return filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED);
	}
	 
	// validate email address
	public static function validate_email($email){
		return filter_var($email, FILTER_VALIDATE_EMAIL);
 	}
}

?>