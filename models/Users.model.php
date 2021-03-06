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

  /**
   * Sube y guarda una nueva imagen para la galería
   */
  public static function uploadImage($profile_id = false, $image = false){
    if($profile_id === false || empty($profile_id) || $image === false || empty($image)){return false;}
    
    //comprobar que las rutas de subida existen y tienen permisos
    $path = "../../admin.mk1/public/assets/images/user/$profile_id/";
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
    $res = file_put_contents($targetFile, file_get_contents($tempFile));
    if($res !== false){
      chmod($targetFile, 0755);

      $query = 'SELECT MAX(position) AS pos FROM user_gallery WHERE profile_id = :profile_id';
      $stm = self::$db->prepare($query);
      $stm->execute( array('profile_id' => $profile_id) );
      $response = $stm->fetch();
      if(!$response){
        $position = 0;
      }else{
        $position = $response['pos'] + 1;
      }

      $query = 'INSERT INTO user_gallery (profile_id, name, position) VALUES (:profile_id, :name, :position )';
      $stm = self::$db->prepare($query);
      self::$db->beginTransaction();
      $stm->execute( array('profile_id' => $profile_id, 'name' => $image['name'], 'position'=>$position) );
      $imageId = self::$db->lastInsertId();
      self::$db->commit(); 
      return array($imageId, $image['name']);
    }else{
      return false;
    }
  }

   /**
   * Borrado lógico de una imagen de la galería
   */
  public static function deleteImage($id = false, $profile_id = false){
    if($id === false || $profile_id === false){return false;}
    $query = 'UPDATE user_gallery SET visible = 0 WHERE id = :id AND profile_id = :profile_id';
    $stm = self::$db->prepare($query);
    $stm->execute(array( 'id'=>$id, 'profile_id'=>$profile_id ));
    return true;
  }


  /**
   *  ***********************************
   *  ************* SETTERS *************
   * ************************************
   */


  /**
   * Elije una imagen favorita
   */
  public static function setFavoriteImage($id = false, $profile_id = false){
    if($id === false || $profile_id === false){return false;}
    //quitar la otra imagen favorita si la hubiera
    $query = 'UPDATE user_gallery SET favorite = 0 WHERE profile_id = :profile_id';
    $stm = self::$db->prepare($query);
    $stm->execute(array( 'profile_id'=>$profile_id )); 
    //setear la nueva favorita
    $query = 'UPDATE user_gallery SET favorite = 1 WHERE id = :id AND profile_id = :profile_id';
    $stm = self::$db->prepare($query);
    $stm->execute(array( 'id'=>$id, 'profile_id'=>$profile_id ));
    return true;
  }

  /**
   * Añade/modifica la descripción de un usuario
   */
  public static function setDescription($profile_id = false, $description = false){
    if($description === false || $profile_id === false){return false;}
    $query = 'INSERT INTO user_description (profile_id, description) VALUES (:profile_id, :description) ON duplicate KEY UPDATE description=:description2';
    $stm = self::$db->prepare($query);
    $stm->execute(array( 'profile_id'=>$profile_id, 'description'=>$description, 'description2'=>$description ));
    return true;
  }

  /**
   * Añade/modifica el email de contacto de un usuario
   */
  public static function setContactEmail($profile_id = false, $email = false){
    if($email === false || $profile_id === false){return false;}
    $query = 'INSERT INTO user_email (profile_id, email) VALUES (:profile_id, :email) ON duplicate KEY UPDATE email=:email2';
    $stm = self::$db->prepare($query);
    $stm->execute(array( 'profile_id'=>$profile_id, 'email'=>$email, 'email2'=>$email ));
    return true;
  }

  /**
   * Añade/modifica métodos de pago aceptados por un usuario
   */
  public static function setPaymentMethods($profile_id = false, $methods = []){
    if(empty($methods) || $profile_id === false){return false;}
    $methods = implode(',', $methods);
    $query = 'INSERT INTO user_payment_methods (profile_id, payment_methods) VALUES (:profile_id, :payment_methods) ON duplicate KEY UPDATE payment_methods=:payment_methods2';
    $stm = self::$db->prepare($query);
    $stm->execute(array( 'profile_id'=>$profile_id, 'payment_methods'=>$methods, 'payment_methods2'=>$methods ));
    return true;
  }

  /**
   * Añade/modifica los filtros de un usuario
   */
  public static function setFilters($profile_id = false, $filters = array()){
    if($profile_id === false || empty($filters)){return false;}

    $query = 'INSERT INTO user_filters (profile_id, filter_id, sub_filters) VALUES (:profile_id, :filter_id, :sub_filters) ON duplicate KEY UPDATE sub_filters=:sub_filters2';
    self::$db->beginTransaction();
    $stm = self::$db->prepare($query);
    foreach($filters as $key => $value){
      //ajustar los valores al formato de la db
      if($key == 5){ //para la fecha de nacimiento
        if($value['day']<10){
          $value['day'] = '0'.$value['day'];
        }
        if($value['month']<10){
          $value['month'] = '0'.$value['month'];
        }
        $value = implode('-', $value);
      }else if($key == 7){//para las características
        $value = implode(',', $value);
      }

      //añadir los insert a la transaction
      $stm->execute(array( 'profile_id'=>$profile_id, 'filter_id'=>$key, 'sub_filters'=>$value, 'sub_filters2'=>$value ));      
    }
    self::$db->commit();   
    return true;
  }

   /**
   * Añade/modifica un perfil de usuario
   */
  public static function setProfile($userId = false, $profile_id = false, $name = false, $surname = false, $phone = false){
    if($userId === false || $profile_id === false || $name === false || $surname === false || $phone === false){return false;}

    if($profile_id == 0){
      $query = 'INSERT INTO user_profiles (profile_id, userId) VALUES (NULL, :userId)';
      $stm = self::$db->prepare($query);
      self::$db->beginTransaction();
      $stm->execute( array('userId' => $userId) );
      $profile_id = self::$db->lastInsertId();
      self::$db->commit(); 
    }
    $query = 'INSERT INTO profile_details (profile_id, name, surname, mobile_phone) VALUES (:profile_id, :name, :surname, :phone) ON DUPLICATE KEY UPDATE name=:name2, surname=:surname2, mobile_phone=:phone2';
    $stm = self::$db->prepare($query);
    $stm->execute(array( 'profile_id'=>$profile_id, 'name'=>$name, 'surname'=>$surname, 'phone'=>$phone, 'name2'=>$name, 'surname2'=>$surname, 'phone2'=>$phone ));
    return true;
  }

  /**
   *  ***********************************
   *  ************* GETTERS *************
   * ************************************
   */

  /**
   * Devuelve las imágenes visibles en la galería de un usuario y por orden
   */
  public static function getGalleryImages($profile_id = false){
    if($profile_id === false || empty($profile_id)){return false;}
    $query = 'SELECT id, name, position, favorite FROM user_gallery WHERE profile_id=:profile_id AND visible = 1 ORDER BY position ASC';
    $stm = self::$db->prepare($query);
    $stm->execute(array('profile_id'=>$profile_id));
    return $stm->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Devuelve la descripcion de un usuario
   */
  public static function getDescription($profile_id = false){
    if($profile_id === false || empty($profile_id)){return false;}
    $query = 'SELECT description FROM user_description WHERE profile_id=:profile_id';
    $stm = self::$db->prepare($query);
    $stm->execute(array('profile_id'=>$profile_id));
    return $response = $stm->fetch();
  }

   /**
   * Devuelve el email de contacto de un usuario
   */
  public static function getContactEmail($profile_id = false){
    if($profile_id === false || empty($profile_id)){return false;}
    $query = 'SELECT email FROM user_email WHERE profile_id=:profile_id';
    $stm = self::$db->prepare($query);
    $stm->execute(array('profile_id'=>$profile_id));
    return $response = $stm->fetch();
  }

  /**
   * Obtiene todos los métodos de pago posibles
   * (Si no se especifica un idioma o no lo tenenos, se envía en el idioma por defecto de la web)
   */
  public static function getAllPaymentMethods($lang = LANGS[0]){
    $lang = (!in_array($lang, LANGS) ? LANGS[0] : $lang);
    $query = 'SELECT id, '.$lang.' FROM payment_methods';
    $stm = self::$db->prepare($query);
    $stm->execute();
    return $stm->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Obtiene todos los métodos de pago posibles
   * (Si no se especifica un idioma o no lo tenenos, se envía en el idioma por defecto de la web)
   */
  public static function getUserPaymentMethods($profile_id = false){
    if($profile_id === false){return false;}
    $query = 'SELECT payment_methods FROM user_payment_methods WHERE profile_id = :profile_id';
    $stm = self::$db->prepare($query);
    $stm->execute(array('profile_id'=>$profile_id));
    return $stm->fetch();
  }

  /**
   * Devuelve todos los filtros de un usuario
   */
  public static function getUserFilters($profile_id = false){
    if($profile_id === false || empty($profile_id)){return false;}
    $query = 'SELECT filter_id, sub_filters FROM user_filters WHERE profile_id=:profile_id ORDER BY filter_id ASC';
    $stm = self::$db->prepare($query);
    $stm->execute(array('profile_id'=>$profile_id));
    return $stm->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Devuelve todos los perfiles de un usuario
   */
  public static function getUserProfiles($userId = false){
    if($userId === false || empty($userId)){return false;}
    $query = 'SELECT DISTINCT up.profile_id, pd.name, pd.surname, pd.mobile_phone FROM user_profiles up, profile_details pd WHERE up.userId=:userId AND pd.profile_id = up.profile_id AND pd.profile_id IN (SELECT DISTINCT profile_id FROM user_profiles WHERE userId=:userId2) ORDER BY up.profile_id ASC';
    $stm = self::$db->prepare($query);
    $stm->execute(array('userId'=>$userId, 'userId2'=>$userId));
    return $stm->fetchAll(PDO::FETCH_ASSOC);
  }
  
}
?>