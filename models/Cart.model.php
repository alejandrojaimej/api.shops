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
    $products = $stm->fetch(PDO::FETCH_ASSOC);
    if($products){$products = json_decode($products['products'], true);}  
    $result = array();

    //formar un array con los datos relevantes de los productos en el carrito para pintarlos en la view
    foreach($products as $product){
        //obtener los detalles de cada producto en funcion del idioma actual
        $query = 'SELECT p.image, p.price, pt.'.$lang.' AS title, ps.'.$lang.' as subtitle, pd.'.$lang.' as description FROM products p, products_title pt, products_subtitle ps, products_description pd WHERE p.id = :product_id AND pt.product_id = p.id AND ps.product_id = p.id AND pd.product_id = p.id';
        $stm = self::$db->prepare($query);
        $stm->execute( array('product_id'=>$product['product_id']) );
        $product_details = $stm->fetch(PDO::FETCH_ASSOC);

        //formar el array
        $result[$product['product_id']] = array();
        $result[$product['product_id']]['product_id'] = $product['product_id'];
        $result[$product['product_id']]['quantity'] = $product['quantity'];
        $result[$product['product_id']]['date_added'] = $product['date_added'];
        $result[$product['product_id']]['image'] = $$product_details['image'];
        $result[$product['product_id']]['price'] = $$product_details['price'];
        $result[$product['product_id']]['title'] = $$product_details['title'];
        $result[$product['product_id']]['subtitle'] = $$product_details['subtitle'];
        $result[$product['product_id']]['description'] = $$product_details['description'];
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
    self::$db->beginTransaction();
    $stm->execute(array('userId'=> $userId, 'products'=>$products, 'products2'=>$products));
    $cart_id = self::$db->lastInsertId();
    self::$db->commit(); 
    return true;
  }


}