<?php
class Users extends Model{
  function __construct(){
    parent::__construct();
  }

  /**
   * Comprueba el login de un usuario
   * @param email: email introducido en el formulario
   * @param password: Contraseña introducida por el usuario en el formulario
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
      $query = 'INSERT INTO users (email, password, token, active, registration_date) VALUES (:email, :password, :token, 0, :date)';
      $stm = self::$db->prepare($query);
      try { 
          $password_hashed = password_hash($password, PASSWORD_DEFAULT);
          $dateTime = date('Y-m-d H:i:s');
          $token = password_hash($email.$dateTime);
          self::$db->beginTransaction();
          $stm->execute( array('email' => $email, 'password' => $password_hashed, 'token' => $token, 'date' => $dateTime) ); 
          self::$db->commit(); 
          $userID = self::$db->lastInsertId(); 
          $query = 'SELECT email, token, active, registration_date FROM users WHERE id = :id';
          $stm = self::$db->prepare($query);
          $stm->execute( array('id' => $userID) ); 
          return $stm->fetch();
      } catch(PDOExecption $e) { 
          self::$db->rollback(); 
          return false;
      } 
    }
    return false;
  }

  /**
   * Activa un usuario en el sistema
   * @param token: password_hash( email + registration_date )
   * @return true si se activa al usuario, false si no.
   */
  public static function activateUser($token = false){
    if($token === false || empty($token)){return false;}
    $query = 'UPDATE users SET active = 1 WHERE token=:token';
    $stm = self::$db->prepare($query);
    try { 
      self::$db->beginTransaction();
      $stm->execute(array('token'=>$token));
      self::$db->commit(); 
      return true;
    } catch(PDOExecption $e) { 
      self::$db->rollback(); 
      return false;
    }
  }
}
?>