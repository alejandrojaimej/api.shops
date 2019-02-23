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
          $token = password_hash($email.$dateTime, PASSWORD_DEFAULT);
          self::$db->beginTransaction();
          $stm->execute( array('email' => $email, 'password' => $password_hashed, 'token' => $token, 'date' => $dateTime) ); 
          $userID = self::$db->lastInsertId();
          self::$db->commit(); 
          $query = 'SELECT email, token, active, registration_date FROM users WHERE id=:id';
          $stm = self::$db->prepare($query);
          $stm->execute( array('id' => $userID) ); 
          return $stm->fetch(PDO::FETCH_ASSOC);
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

      $query = 'SELECT id FROM users WHERE token=:token';
      $stm = self::$db->prepare($query);
      $stm->execute(array('token'=>$token));
      return $stm->fetch(PDO::FETCH_ASSOC);
    } catch(PDOExecption $e) { 
      self::$db->rollback(); 
      return false;
    }
  }

  /**
   * Comprueba si un email existe en  la base de datos y devuelve el token del usuario en caso de que exista
   */
  public static function checkEmail($email = false){
    if($email === false || empty($email)){return false;}
    $query = 'SELECT id, email, token FROM users WHERE email=:email AND active = 1';
    $stm = self::$db->prepare($query);
    $stm->execute(array('email'=>$email));
    return $stm->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * Devuelve las imágenes visibles en la galería de un usuario y por orden
   */
  public static function getGalleryImages($userId = false){
    if($userId === false || empty($userId)){return false;}
    $query = 'SELECT id, name, position FROM user_gallery WHERE userId=:userId AND visible = 1 ORDER BY position ASC';
    $stm = self::$db->prepare($query);
    $stm->execute(array('userId'=>$userId));
    return $stm->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Actualiza la posición de una imagen en la bd dado su id y nueva posción
   */
  public static function updateImagePosition($id = false, $position = false){
    if($id === false || $position === false){return false;}
    $query = 'UPDATE user_gallery SET position = :position WHERE id = :id';
    $stm = self::$db->prepare($query);
    $stm->execute(array( 'position'=>$position, 'id'=>$id ));
    $response = $stm->fetch();
    return true;
  }

  public static function uploadImage($userId = false, $image = false){
    if($userId === false || empty($userId) || $image === false || empty($image)){return false;}
    
    //comprobar que las rutas de subida existen y tienen permisos
    $path = dirname($_SERVER['DOCUMENT_ROOT'])."/../../admin.mk1/public/assets/images/user/$userId/";
    if (!is_dir($path)) {
      mkdir($path);
      chmod($path, 0777);
    }
    $path .= 'gallery/';
    if (!is_dir($path)) {
      mkdir($path);
      chmod($path, 0777);
    }

    $tempFile = $image['tmp_name'];
    $targetFile =  $path. $image['name'];
    $res = move_uploaded_file($tempFile,$targetFile);
    return $targetFile;
  }
}
?>