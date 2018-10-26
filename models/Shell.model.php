<?php
class Shell extends Model{
  function __construct(){
    parent::__construct();
  }
   /**
   * Devuelve la imagen de portada de la web
   * @param id: Devuelve una imagen específica dado un Id, si no devuleve aleatorias
   * @param numImages: Determina el número máximo de imágenes aleatorias a devolver
   */
  public static function getMainImage($id = false, $numImages = 1){
    if($id !== false){
        $query = 'SELECT id, url FROM PWA_main_images WHERE id=:id';
        $stm = self::$db->prepare($query);
        $stm->execute(array('id'=>$id));
    }else{
        $query = 'SELECT id, url FROM PWA_main_images ORDER BY RAND() LIMIT '.$numImages;
        $stm = self::$db->prepare($query);
        $stm->execute();
    }
  	return $stm->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Devuelve los textos para el datepicker (días, meses y nombres cortos para los días)
   * @param lang: Idioma de los textos (de momento son es/en/zh) (por defecto es "es")
   */
  public static function getDateNames($lang = LANGS[0]){
    $result = [];    

    //obtener los nombres del mes
    $query = 'SELECT '.$lang.' FROM PWA_months_names';
    $stm = self::$db->prepare($query);
    $stm->execute();
    $res = $stm->fetchAll(PDO::FETCH_ASSOC);
    $result['months_names'] = [];
    foreach($res as $arr){
      foreach($arr as $name){
        $result['months_names'][] = $name;
      }
    }
    

    //obtener los nombres de los dias
    $query = 'SELECT '.$lang.' FROM PWA_days_names';
    $stm = self::$db->prepare($query);
    $stm->execute();
    $res = $stm->fetchAll(PDO::FETCH_ASSOC);
    $result['days_names'] = [];
    foreach($res as $arr){
      foreach($arr as $name){
        $result['days_names'][] = $name;
      }
    }

    //obtener los abreviados de los dias
    $query = 'SELECT '.$lang.' FROM PWA_short_days_names';
    $stm = self::$db->prepare($query);
    $stm->execute();
    $res = $stm->fetchAll(PDO::FETCH_ASSOC);
    $result['short_days_names'] = [];
    foreach($res as $arr){
      foreach($arr as $name){
        $result['short_days_names'][] = $name;
      }
    }
    return $result;
  }

  /**
   * Devuelve los textos para el buscador principal
   * @param id: Devuelve texto específico dado un Id (podría hacerse por "component"..al gusto)
   * @param lang: Idioma de los textos
   */
  public static function getMainSearcher($id = false, $lang = LANGS[0]){
    $search = 'id, component, '.$lang;
    if($id !== false){
        $query = 'SELECT '.$search.' FROM PWA_main_searcher WHERE id=:id';
        $stm = self::$db->prepare($query);
        $stm->execute(array('id'=>$id));
    }else{
        $query = 'SELECT '.$search.' FROM PWA_main_searcher';
        $stm = self::$db->prepare($query);
        $stm->execute();
    }
    $result = self::getDateNames($lang);
    $result['main_searcher'] = $stm->fetchAll(PDO::FETCH_ASSOC);
  	return $result;
  }
}
?>