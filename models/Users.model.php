<?php
class Users extends Model{
  function __construct(){
    parent::__construct();
  }

  /**
   * Devuelve los textos para el buscador principal
   * @param id: Devuelve texto específico dado un Id (podría hacerse por "component"..al gusto)
   * @param lang: Idioma de los textos
   * @return userId si se encuentra al usuario, false si no.
   */
  public static function checkLogin($email = false, $password = false){
    if($email === false || empty($email) || $password === false || empty($password)){return false;}
    $query = 'SELECT * FROM users WHERE email=:email AND active = 1';
    $stm = self::$db->prepare($query);
    $stm->execute(array('email'=>$email));
    $user = $stm->fetch();
    if( password_verify($password, $user['password']) ){
      return $user['id'];  
    }else{
      return false;
    }
    
  }

  /**
   * Inserta un nuevo usuario en la base de datos
   * @param $email -> es un campo unique en la bdd, por lo que puede fallar, de ahí la comprobación
   * @param $password -> se encripta con password_hash de php
   * @return lastInsertedId() de pdo cuando se ha insertado el usuario, false si ha habido algún error al insertar
   */
  public static function addUser($email = false, $password = false){
    if($email === false || empty($email) || $password === false || empty($password)){return false;}
    $query = 'SELECT email FROM users WHERE email=:email';
    $stm = self::$db->prepare($query);
    $stm->execute(array('email'=>$email));
    if($stm->rowCount() > 0){
      return false;
    }else{
      $query = 'INSERT INTO users (email, password, active) VALUES (:email, :password, 0)';
      $stm = self::$db->prepare($query);
      try { 
          $password_hashed = password_hash($password, PASSWORD_DEFAULT);
          self::$db->beginTransaction();
          $stm->execute( array('email' => $email, 'password' => $password_hashed) ); 
          self::$db->commit(); 
          return  self::$db->lastInsertId(); 
      } catch(PDOExecption $e) { 
          self::$db->rollback(); 
          return false;
      } 
    }
    return false;
  }
}
?>