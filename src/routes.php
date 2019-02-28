<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes


/**
 *   METODOS GET
 */

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
$app->get('/userGallery/{profile_id}', function(Request $request, Response $response, array $args){
    $resp = auth($request, $response);if($resp != 'valid'){return $resp;}
    $model = loadModel('Users');

    $result = $model::getGalleryImages($args['profile_id']);

    $response_data = array();
    $response_data['error'] = false; 
    $response_data['response'] = $result; 
    $response->write(json_encode($response_data));

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
});

/**
 * Obtiene la descripcion de un usuario
 */
$app->get('/getDescription/{profile_id}', function(Request $request, Response $response, array $args){
    $resp = auth($request, $response);if($resp != 'valid'){return $resp;}
    $model = loadModel('Users');

    $result = $model::getDescription($args['profile_id']);

    $response_data = array();
    $response_data['error'] = false; 
    $response_data['response'] = $result; 
    $response->write(json_encode($response_data));

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
});


/**
 * Obtiene el email de contacto de un usuario
 */
$app->get('/getContactEmail/{profile_id}', function(Request $request, Response $response, array $args){
    $resp = auth($request, $response);if($resp != 'valid'){return $resp;}
    $model = loadModel('Users');

    $result = $model::getContactEmail($args['profile_id']);

    $response_data = array();
    $response_data['error'] = false; 
    $response_data['response'] = $result; 
    $response->write(json_encode($response_data));

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
});

/**
 * Obtiene el email de contacto de un usuario
 */
$app->get('/getAllPaymentMethods/{lang}', function(Request $request, Response $response, array $args){
    $resp = auth($request, $response);if($resp != 'valid'){return $resp;}
    $model = loadModel('Users');

    $result = $model::getAllPaymentMethods($args['lang']);

    $response_data = array();
    $response_data['error'] = false; 
    $response_data['response'] = $result; 
    $response->write(json_encode($response_data));

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
});

/**
 * Obtiene las formas de pago seleccionadas por un usuario
 */
$app->get('/getUserPaymentMethods/{profile_id}', function(Request $request, Response $response, array $args){
    $resp = auth($request, $response);if($resp != 'valid'){return $resp;}
    $model = loadModel('Users');

    $result = $model::getUserPaymentMethods($args['profile_id']);

    $response_data = array();
    $response_data['error'] = false; 
    $response_data['response'] = $result; 
    $response->write(json_encode($response_data));

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
});

/**
 * Obtiene todos los filtros y sus subfiltros de la base de datos
 */
$app->get('/getFiltersAndSubfilters/{lang}', function(Request $request, Response $response, array $args){
    $resp = auth($request, $response);if($resp != 'valid'){return $resp;}
    $model = loadModel('Shell');

    $result = $model::getFiltersAndSubfilters($args['lang']);

    $response_data = array();
    $response_data['error'] = false; 
    $response_data['response'] = $result; 
    $response->write(json_encode($response_data));

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
});


/**
 * Obtiene los perfiles de un usuario
 */
$app->get('/getUserProfiles/{userId}', function(Request $request, Response $response, array $args){
    $resp = auth($request, $response);if($resp != 'valid'){return $resp;}
    $model = loadModel('Users');

    $result = $model::getUserProfiles($args['userId']);

    $response_data = array();
    $response_data['error'] = false; 
    $response_data['response'] = $result; 
    $response->write(json_encode($response_data));

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
});


/**
 *   METODOS POST
 */

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
 * Activa un usuario registrado en la base de datos
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

/**
 * Sube una nueva imagen para la galería de los usuarios
 */
$app->post('/uploadImage', function(Request $request, Response $response){
    $resp = auth($request, $response);if($resp != 'valid'){return $resp;}
    $users = loadModel('Users');

    $result = $users::uploadImage($request->getParam("profile_id"), $request->getParam("image") );
    $response->write(json_encode($result));

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
});

/**
 * Modifica la imagen favorita del usuario
 */
$app->post('/setFavoriteImage', function(Request $request, Response $response){
    $resp = auth($request, $response);if($resp != 'valid'){return $resp;}
    $users = loadModel('Users');

    $result = $users::setFavoriteImage($request->getParam("id"), $request->getParam('profile_id') );

    $response_data = array();
    $response_data['error'] = false; 
    $response_data['response'] = $result;
    $response->write(json_encode($response_data));

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
});

/**
 * Borrado lógico de una imagen de la galería
 */
$app->post('/deleteImage', function(Request $request, Response $response){
    $resp = auth($request, $response);if($resp != 'valid'){return $resp;}
    $users = loadModel('Users');

    $result = $users::deleteImage($request->getParam("id"), $request->getParam('profile_id') );

    $response_data = array();
    $response_data['error'] = false; 
    $response_data['response'] = $result;
    $response->write(json_encode($response_data));

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
});

/**
 * Inserta/modifica la descripción de un usuario
 */
$app->post('/setDescription', function(Request $request, Response $response){
    $resp = auth($request, $response);if($resp != 'valid'){return $resp;}
    $users = loadModel('Users');

    $result = $users::setDescription($request->getParam("profile_id"), $request->getParam('description') );

    $response_data = array();
    $response_data['error'] = false; 
    $response_data['response'] = $result;
    $response->write(json_encode($response_data));

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
});


/**
 * Inserta/modifica el email de contacto de un usuario
 */
$app->post('/setContactEmail', function(Request $request, Response $response){
    $resp = auth($request, $response);if($resp != 'valid'){return $resp;}
    $users = loadModel('Users');

    $result = $users::setContactEmail($request->getParam("profile_id"), $request->getParam('email') );

    $response_data = array();
    $response_data['error'] = false; 
    $response_data['response'] = $result;
    $response->write(json_encode($response_data));

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
});

/**
 * Inserta/modifica los métods de pago aceptados por un usuario
 */
$app->post('/setPaymentMethods', function(Request $request, Response $response){
    $resp = auth($request, $response);if($resp != 'valid'){return $resp;}
    $users = loadModel('Users');

    $result = $users::setPaymentMethods($request->getParam("profile_id"), $request->getParam('methods') );

    $response_data = array();
    $response_data['error'] = false; 
    $response_data['response'] = $result;
    $response->write(json_encode($response_data));

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
});


/**
 *   METODOS PUT
 */

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

