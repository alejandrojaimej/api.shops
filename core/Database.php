<?php
/**
*		Conexión singleton a la base de datos mediante PDO
*		Gestiona la conexión e impide que se genere más de una por cada petición del cliente
*/


class DB {
	protected static $instance;
	protected function __construct() {}

	public static function getInstance() {
		if(empty(self::$instance)) {
			try {
				self::$instance = new PDO(DATABASE['db_type'].":host=".DATABASE['db_host'].';port='.DATABASE['db_port'].';dbname='.DATABASE['db_name'], DATABASE['db_user'], DATABASE['db_pass']);
				self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
				self::$instance->query('SET NAMES utf8');
				self::$instance->query('SET CHARACTER SET utf8');
			} catch(PDOException $error) {
				echo $error->getMessage();
			}
		}
		return self::$instance;
	}
	public static function setCharsetEncoding() {
		if (self::$instance == null) {
			self::connect();
		}
		self::$instance->exec(
			"SET NAMES 'utf8';
			SET character_set_connection=utf8;
			SET character_set_client=utf8;
			SET character_set_results=utf8");
	}
}
?>
