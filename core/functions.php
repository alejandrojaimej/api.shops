<?php
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
