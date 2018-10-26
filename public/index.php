<?php
//https://www.slimframework.com/docs/v3/objects/application.html
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../core/globals.php';
session_start();
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings );
$DEFAULT_LANG = LANGS[0];
require __DIR__ . '/../src/dependencies.php';
// Register middleware
require __DIR__ . '/../src/middleware.php';
// Register routes
require __DIR__ . '/../src/routes.php';

function loadModel($model = false){
    if($model === false){return false;}
    require_once CORE_FOLDER.DIRECTORY_SEPARATOR.'Model.php';
    require_once MODELS_FOLDER.DIRECTORY_SEPARATOR.$model.'.model.php';
    //el controlador está en un subdirectorio
    if(strpos($model, '/')){
        $aux = array_reverse(explode('/', $model));
        $model = $aux[0];
    }
    return new $model();
}

function haveEmptyParameters($required_params, $request, $response){
    $error = false; 
    $error_params = '';
    $request_params = $request->getParsedBody(); 
    foreach($required_params as $param){
        if(!isset($request_params[$param]) || strlen($request_params[$param])<=0){
            $error = true; 
            $error_params .= $param . ', ';
        }
    }
    if($error){
        $error_detail = array();
        $error_detail['error'] = true; 
        $error_detail['message'] = 'Required parameters ' . substr($error_params, 0, -2) . ' are missing or empty';
        $response->write(json_encode($error_detail));
    }
    return $error; 
}

function auth($request, $response){
    $headers = $request->getHeaders();
    /*if( !isset($headers['HTTP_X_AUTHENTICATION'][0]) || !in_array($headers['HTTP_X_AUTHENTICATION'][0], ACCEPTED_API_KEYS)){
        $response_data = array();
        $response_data['error'] = true; 
        $response_data['description'] = 'Invalid API KEY'; 
        $response->write(json_encode($response_data));
        return $response
        ->withHeader('Content-type', 'text/plain')
        ->withStatus(403);
    }*/
    return 'valid';
}
 

// Run app
$app->run();
