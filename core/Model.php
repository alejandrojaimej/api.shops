<?php
/**
 *  Clase principal de todos los modelos
 *  Contiene las funciones comunes a todos los modelos, en este caso la conexión a la base de datos
 *
 */
class Model
{
  public static $db = false;
  function __construct()
  {
    require_once CORE_FOLDER.'/Database.php';
    try {
    	self::$db = DB::getInstance();
    	DB::setCharsetEncoding();
      return self::$db;
    } catch (Exception $e) {
    	print $e->getMessage();
    }
  }
}

?>
