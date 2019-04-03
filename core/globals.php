<?php 
require_once('.privateGlobals.php');

defined('DEBUG') or define('DEBUG', true);
if(DEBUG){
    defined('BASE_ROUTE') or define('BASE_ROUTE', __DIR__ . '/../');
}else{
    defined('BASE_ROUTE') or define('BASE_ROUTE', '/var/www/html/api.mk1');
}


defined('SITE_VERSION') or define('SITE_VERSION', '1.0');

defined('LANGS') or define('LANGS', ['es', 'en', 'zh']);

defined('BASEDIR') or define('BASEDIR', __DIR__);
defined('DOMAIN') or define('DOMAIN', (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost'));
defined('BASEURL') or define('BASEURL', 'http'.(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : '').'://'.DOMAIN);
defined('CORE_FOLDER') or define('CORE_FOLDER', BASE_ROUTE.DIRECTORY_SEPARATOR.'core');
defined('MODELS_FOLDER') or define('MODELS_FOLDER', BASE_ROUTE.DIRECTORY_SEPARATOR.'models');

    
defined('USER_CREATED') or define('USER_CREATED', 101);
defined('USER_EXISTS') or define('USER_EXISTS', 102);
defined('USER_FAILURE') or define('USER_FAILURE', 103); 
defined('USER_AUTHENTICATED') or define('USER_AUTHENTICATED', 201);
defined('USER_NOT_FOUND') or define('USER_NOT_FOUND', 202); 
defined('USER_PASSWORD_DO_NOT_MATCH') or define('USER_PASSWORD_DO_NOT_MATCH', 203);
defined('PASSWORD_CHANGED') or define('PASSWORD_CHANGED', 301);
defined('PASSWORD_DO_NOT_MATCH') or define('PASSWORD_DO_NOT_MATCH', 302);
defined('PASSWORD_NOT_CHANGED') or define('PASSWORD_NOT_CHANGED', 303);


