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
    $search = 'component, '.$lang;
    $query = 'SELECT '.$search.' FROM translations WHERE controller=:controller';
    $stm = self::$db->prepare($query);
    $stm->execute(array('controller'=>$controller));
    $resp = $stm->fetchAll(PDO::FETCH_ASSOC);
    $result = array();
    foreach($resp as $key => $value){
      $result[$value[0]] = $value[1];
    }
    return $result;
  }
}
?>