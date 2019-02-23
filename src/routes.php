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
 * Devuelve todos los textos para la administración
 * @param lang Tiene que ser uno de los definidos en LANGS (globlas.php) en caso de ser otro devuelve el primer idioma definido.
 */
$app->get('/adminText/{lang}/{controller}/{userId}', function(Request $request, Response $response, array $args){
    $resp = auth($request, $response);if($resp != 'valid'){return $resp;}
    $shell = loadModel('Shell');

    $result = $shell::getAdminText($args['lang'], $args['controller'], $args['userId']);

    $response_data = array();
    $response_data['error'] = false; 
    $response_data['response'] = $result; 
    $response->write(json_encode($response_data));

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
});

/**
 * Devuelve las imágenes de la galería de un usuario
 * @param userId	Contiene un id de usuario válido en la db
 */
$app->get('/userGallery/{userId}', function(Request $request, Response $response, array $args){
    $resp = auth($request, $response);if($resp != 'valid'){return $resp;}
    $model = loadModel('Users');

    $result = $model::getGalleryImages($args['userId']);

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
 * Actualiza la posición de una imágen de la galería de usuario
 */
$app->post('/updateImagePosition', function(Request $request, Response $response){
    $resp = auth($request, $response);if($resp != 'valid'){return $resp;}
    $users = loadModel('Users');

    $result = $users::updateImagePosition($request->getParam("id"), $request->getParam('position') );

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
$app->post('/activateUser', function(Request $request, Response $response){
    $resp = auth($request, $response);if($resp != 'valid'){return $resp;}
    $users = loadModel('Users');

    $result = $users::activateUser($request->getParam('userToken'));

    $response_data = array();
    $response_data['error'] = false; 
    $response_data['response'] = $result; 
    $response->write(json_encode($response_data));

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
});

/**
 * Comprueba si un email existe en la base de datos y devuelve su token
 */
$app->post('/forgotPass', function(Request $request, Response $response){
    $resp = auth($request, $response);if($resp != 'valid'){return $resp;}
    $users = loadModel('Users');

    $result = $users::checkEmail($request->getParam("email") );

    $response_data = array();
    $response_data['error'] = false; 
    $response_data['response'] = $result;
    $response->write(json_encode($response_data));

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
});