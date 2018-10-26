<?php
class Locations extends Model{
  function __construct(){
    parent::__construct();
  }

  public static function searchByString($lang = LANGS[0], $string = ''){
    if(in_array($lang, LANGS)){
      $lang = strtolower($lang);
    }else{
      $lang = strtolower(LANGS[0]);
    }
    $query = "SELECT DISTINCT * FROM 
      (
        SELECT DISTINCT continent_name, country_name, subdivision_1_name, subdivision_2_name, city_name, '1' AS orden FROM `locations_$lang` WHERE city_name LIKE '%$string%'
          UNION
        SELECT DISTINCT continent_name, country_name, subdivision_1_name, subdivision_2_name, city_name, '2' AS orden FROM `locations_$lang` WHERE subdivision_2_name LIKE '%$string%' 
          UNION
        SELECT DISTINCT continent_name, country_name, subdivision_1_name, subdivision_2_name, city_name, '3' AS orden FROM `locations_$lang` WHERE subdivision_1_name LIKE '%$string%' 
          UNION
        SELECT DISTINCT continent_name, country_name, subdivision_1_name, subdivision_2_name, city_name, '4' AS orden FROM `locations_$lang` WHERE country_name LIKE '%$string%' 
      ) A  ORDER BY orden";
    $stm = self::$db->prepare($query);
    $stm->execute();
    return $stm->fetchAll(PDO::FETCH_ASSOC);
  }
}
?>