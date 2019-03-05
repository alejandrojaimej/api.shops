<?php
class Products extends Model{
  function __construct(){
    parent::__construct();
  }

  /**
   * Devuelve todos los productos, sus titulos, subtitulos y descripciones
   */
  public static function getAllProducts($lang = LANGS[0]){
    $lang = (!in_array($lang, LANGS) ? LANGS[0] : $lang);
    $query = 'SELECT p.*, pt.'.$lang.' as title, ps.'.$lang.' as subtitle, pd.'.$lang.' as description FROM products p, products_title pt, products_subtitle ps, products_description pd WHERE p.id = pt.product_id AND p.id = ps.product_id AND p.id = pd.product_id ORDER BY p.id';
    $stm = self::$db->prepare($query);
    $stm->execute();
    return $stm->fetchAll(PDO::FETCH_ASSOC);
  }


}