<?php

class Task {

	public function index($req, $res, $params){

		$res->render($response, 'header.phtml');

		$db = DB::getInstance();
		
		$data = $db->tasks();

	}
}

?>