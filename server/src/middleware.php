<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

$RequireAuth = function ($request, $response, $next) use ($app) {

 	$authHeader = $request->getHeaderLine('Authorization');

 	list($jwt) = sscanf( $authHeader, 'Bearer %s');

 	$token_data = Auth::Validate($jwt);

 	if( $token_data !== false ){
 		$app->setExtra('user_id',$token_data->user);
 		$response = $next($request, $response);
 	}else{
 		echo "HTTP/1.1 401 Unauthorized";
 		header('HTTP/1.1 401 Unauthorized', true, 401);
 		exit;
 	}

    return $response;
};
