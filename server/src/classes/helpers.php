<?php

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
