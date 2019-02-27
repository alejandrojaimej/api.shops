<?php
class Shell extends Model{
  function __construct(){
    parent::__construct();
  }

  /**
   * Devuelve los textos para el buscador principal
   * @param id: Devuelve texto específico dado un Id (podría hacerse por "component"..al gusto)
   * @param lang: Idioma de los textos
   */
  public static function getText($lang = LANGS[0], $controller = 'login'){
    $search = 'component, '.(!in_array($lang, LANGS) ? LANGS[0] : $lang);
    $query = 'SELECT '.$search.' FROM translations WHERE controller=:controller OR controller="base"';
    $stm = self::$db->prepare($query);
    $stm->execute(array('controller'=>$controller));
    return $stm->fetchAll(PDO::FETCH_KEY_PAIR);
  }

  /**
   * Devuelve los textos para el buscador principal
   * @param id: Devuelve texto específico dado un Id (podría hacerse por "component"..al gusto)
   * @param lang: Idioma de los textos
   */
  public static function getAdminText($lang = LANGS[0], $controller = 'login', $userId = false){
    if($userId === false){ return false; }
    $search = 'component, '.(!in_array($lang, LANGS) ? LANGS[0] : $lang);
    $query = 'SELECT '.$search.' FROM translations WHERE controller=:controller OR controller="base"';
    $stm = self::$db->prepare($query);
    $stm->execute(array('controller'=>$controller));
    $textos = $stm->fetchAll(PDO::FETCH_KEY_PAIR);

    /*$query = 'SELECT id, name FROM comerces WHERE userId = :user';
    $stm = self::$db->prepare($query);
    $stm->execute(array('user'=>$userId));
    $comercios = $stm->fetchAll(PDO::FETCH_KEY_PAIR);*/

    $query = 'SELECT name, surname FROM user_details WHERE userId = :user';
    $stm = self::$db->prepare($query);
    $stm->execute(array('user'=>$userId));
    $usuario = $stm->fetch();

    return array('texts'=>$textos, /*'comerces'=>$comercios,*/ 'user'=>$usuario);
  }

  /**
   * Devuelve los nombres de los filtros
   */
  public static function getAllFiltersNames($lang = LANGS[0]){
    $search = (!in_array($lang, LANGS) ? LANGS[0] : $lang);
    $query = 'SELECT '.$search.' FROM filters';
    $stm = self::$db->prepare($query);
    $stm->execute();
    return $stm->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Devuelve el nombre de un filtro específico
   */
  public static function getFilterNames($lang = LANGS[0], $filter_id = false){
    if($filter_id === false){return false;}
    $search = (!in_array($lang, LANGS) ? LANGS[0] : $lang);
    $query = 'SELECT '.$search.' FROM filters WHERE id=:filter_id';
    $stm = self::$db->prepare($query);
    $stm->execute(array('filter_id'=>$filter_id));
    return $stm->fetch();
  }

  /**
   * Devuelve los nombres de los sub_filtros de un filtro específico
   */
  public static function getSubFilterByFilterId($lang = LANGS[0], $filter_id = false){
    if($filter_id === false){return false;}
    $search = (!in_array($lang, LANGS) ? LANGS[0] : $lang);
    $query = 'SELECT '.$search.' FROM sub_filters WHERE filter_id = :filter_id ORDER BY filter_id, id ASC';
    $stm = self::$db->prepare($query);
    $stm->execute();
    return $stm->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Devuelve los id y nombres de los filtros y sus sub_filtros
   */
  public static function getFiltersAndSubfilters($lang = LANGS[0]){
    $search = (!in_array($lang, LANGS) ? LANGS[0] : $lang);
    $query = 'SELECT distinct filters.id as filter_id, filters.'.$search.' AS filter_name, sub_filters.id as sub_filter_id, sub_filters.'.$search.' AS sub_filter_name FROM filters, sub_filters WHERE filters.id = sub_filters.filter_id';
    $stm = self::$db->prepare($query);
    $stm->execute();
    return $stm->fetchAll(PDO::FETCH_ASSOC);
  }

}
?>