<?php
class Cart extends Model{
  function __construct(){
    parent::__construct();
  }

  /**
   * Devuelve todos los productos, sus titulos, subtitulos y descripciones
   */
  public static function getCart($lang = LANGS[0], $userId = false){
    $lang = (!in_array($lang, LANGS) ? LANGS[0] : $lang);
    if($userId === false){ return false; }
    $query = 'SELECT products FROM user_cart WHERE userId = :userId';
    $stm = self::$db->prepare($query);
    $stm->execute( array('userId'=>$userId) );
    $products = $stm->fetch();
    if($products){$products = json_decode($products, true);}
    $result = array();

    //formar un array con los datos relevantes de los productos en el carrito para pintarlos en la view
    foreach($producst as $key => $value){
        //obtener los detalles de cada producto en funcion del idioma actual
        $query = 'SELECT p.image, p.price, pt.'.$lang.' AS title, ps.'.$lang.' as subtitle, pd.'.$lang.' as description FROM products p, products_title pt, products_subtitle ps, products_description pd WHERE p.id = :product_id AND pt.product_id = p.id AND ps.product_id = p.id AND pd.product_id = p.id';
        $stm->execute( array('product_id'=>$key) );
        $product_details = $stm->fetch();

        //formar el array
        $result[$key] = array();
        $result[$key]['product_id'] = $value['product_id'];
        $result[$key]['quantity'] = $value['quantity'];
        $result[$key]['date_added'] = $value['date_added'];
        $result[$key]['image'] = $$product_details['image'];
        $result[$key]['price'] = $$product_details['price'];
        $result[$key]['title'] = $$product_details['title'];
        $result[$key]['subtitle'] = $$product_details['subtitle'];
        $result[$key]['description'] = $$product_details['description'];
    }

    return $result;
  }

  
  /**
   * Devuelve todos los productos, sus titulos, subtitulos y descripciones
   */
  public static function setCart($userId = false, $products = false){
    if($userId === false || $products === false){ return false; }
    $query = 'INSERT INTO user_cart (id, userId, products) VALUES (NULL, :userId, :products) ON duplicate KEY UPDATE products = :products2';
    $stm = self::$db->prepare($query);
    $stm->execute(array('userId'=> $userId, 'products'=>$products, 'products2'=>$products));
    return true;
  }


}