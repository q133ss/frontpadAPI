<?php
/*
 *Plugin name: Frontpad API
 */

 //API frontpad
add_action('woocommerce_thankyou', 'front_api');
global $woocommerce, $post;
function front_api($order_id){

    $order = new WC_Order( $order_id );
    $dataa = $order->get_data();

    $all = $order->get_subtotal(); //sum without discount 5000
    $disk = $order->get_total();//sum with Skidka 4500
    //Получаем сумму скидки
    $a = $all-$disk;
    //Умножаем сумму скидки на 100
    $b = $a*100;
    $x = $b/$all;

    settype($all, "integer");

    $name = $order->get_billing_first_name();
    $street = $order->get_billing_address_1();
    $home = $order->get_billing_address_2();
    $phone = $order->get_billing_phone();
    $note = $order->get_customer_note();

    $item_sku = array();

    foreach ($order->get_items() as $item) { //get sku
    $product = wc_get_product($item->get_product_id());
    $item_sku[] = $product->get_sku();
  }


//детали заказа в кодировке utf-8
$param['secret'] = "4dYAehhBbFB3THrGhenZ7kG82fbGZt2NAH9FTh6YQyKz535b6f34tz2fE3QrhD5H94dSa9DQtaak36rYaeKyDzD7keizGb6F68k67QHrQHEEDYnE322rinkztzT3BQytNGnHiZ32yss2aFr4tbeErs4KRKBdkYA3sQzDBirYEHHEb2ty4B3fTRSznfDD44rhENK524QrAiYB9bZ4EN8irRHFZa7Q4kZy3zkNk44KaKaaQ24eA3sQE4hr4d";				//ключ api
$param['street']  = $street;		//улица
$param['home']	=  $home;				//дом
//$param['apart']	= $home;	 			//квартира
$param['phone'] = $phone;		//телефон
$param['descr']	= urlencode($note); 	//комментарий
$param['name']	= $name;		//имя клиента
$param['sale'] = $x;
$tags = array(1,5);				//отметки заказа - необязательно
$hook_status = array(3,4);			//запрос вебхука - необязательно

//подготовка запроса
foreach ($param as $key => $value) {
$data .= "&".$key."=".$value;
}

if($tags) {
foreach ($tags as $key => $value){
		$data .= "&tags[".$key."]=".$value."";
}
}

if($hook_status) {
foreach ($hook_status as $key => $value){
		$data .= "&hook_status[".$key."]=".$value."";
}
}

//содержимое заказа
foreach ($item_sku as $key => $value){
$data .= "&product[".$key."]=".$value."";
$data .= "&product_kol[".$key."]=".$product_kol[$key]."";
if(isset($product_mod[$key])) {
$data .= "&product_mod[".$key."]=".$product_mod[$key]."";
}
}

//отправка
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://app.frontpad.ru/api/index.php?new_order");
curl_setopt($ch, CURLOPT_FAILONERROR, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$result = curl_exec($ch);
curl_close($ch);

//результат
//echo $result;
}
