<?php
class Hotels extends Model{
  function __construct(){
    parent::__construct();
  }
  public static function getMainImage($hotelId){
    $query = 'SELECT Id, Imagen, ImagenP, ImagenG, Titulo FROM FOTOS WHERE RAlojamiento_id=:hotelId AND visible=1 ORDER BY favorita DESC, pos limit 1';
    $stm = self::$db->prepare($query);
  	$stm->execute(array('hotelId'=>$hotelId));
  	return $stm->fetchAll(PDO::FETCH_ASSOC);
  }
}
?>
