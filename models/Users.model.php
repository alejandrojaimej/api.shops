<?php
class Users extends Model{
  function __construct(){
    parent::__construct();
  }

  /**
   * Devuelve los textos para el buscador principal
   * @param id: Devuelve texto específico dado un Id (podría hacerse por "component"..al gusto)
   * @param lang: Idioma de los textos
   */
  public static function checkLogin($email = false, $password = false){
    if($email === false || empty($email) || $password === false || empty($password)){return false;}
    $query = 'SELECT id FROM users WHERE email=:email AND password=:password AND active = 1';
    $stm = self::$db->prepare($query);
    $stm->execute(array('email'=>$email, 'password'=>$password));
    return $stm->fetchAll(PDO::FETCH_KEY_PAIR);
  }
}
?>