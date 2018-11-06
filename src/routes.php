<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

/**
 * Devuelve todos los textos para una view determinada
 * @param lang Tiene que ser uno de los definidos en LANGS (globlas.php) en caso de ser otro devuelve el primer idioma definido.
 */
$app->get('/text/{lang}/{controller}', function(Request $request, Response $response, array $args){
    $resp = auth($request, $response);if($resp != 'valid'){return $resp;}
    $shell = loadModel('Shell');

    $result = $shell::getText($args['lang'], $args['controller']);

    $response_data = array();
    $response_data['error'] = false; 
    $response_data['response'] = $result; 
    $response->write(json_encode($response_data));

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
});

/**
 * Comprueba si un par Usuario/Contraseña existe en la base de datos
 */
$app->post('/login', function(Request $request, Response $response){
    $resp = auth($request, $response);if($resp != 'valid'){return $resp;}
    $users = loadModel('Users');

    $result = $users::checkLogin($request->getParam("email"), $request->getParam('password') );

    $response_data = array();
    $response_data['error'] = false; 
    $response_data['response'] = $result;
    $response->write(json_encode($response_data));

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
});

/**
 * Inserta un nuevo usuario en la base de datos
 */
$app->put('/registerUser/{email}/{password}', function(Request $request, Response $response, array $args){
    $resp = auth($request, $response);if($resp != 'valid'){return $resp;}
    $users = loadModel('Users');

    $result = $users::addUser($args['email'], $args['password']);

    $response_data = array();
    $response_data['error'] = false; 
    $response_data['response'] = $result; 
    $response->write(json_encode($response_data));

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
});

/**
 * Inserta un nuevo usuario en la base de datos
 */
$app->get('/activateUser[/{userToken:.*}]', function(Request $request, Response $response, array $args){
    $resp = auth($request, $response);if($resp != 'valid'){return $resp;}
    $users = loadModel('Users');

    echo '1: '.urldecode($args['userToken']);
    echo '<br>2: '.urldecode($request->getAttribute('userToken'));

    /*$result = $users::activateUser(urldecode($args['userToken']));

    $response_data = array();
    $response_data['error'] = false; 
    $response_data['response'] = $result; 
    $response->write(json_encode($response_data));

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  */
});