<?php
/*
#####################################
#  ShopOS: Shopping Cart Software.
#  Copyright (c) 2008-2010
#  http://www.shopos.ru
#  http://www.shoposs.com
#  Ver. 1.0.2
#####################################
*/
/*  Copyright (c) 2007 VaM Shop, http://vamshop.com */

function get_var($name, $default = 'none') 
{
  return (isset($_GET[$name])) ? $_GET[$name] : ((isset($_POST[$name])) ? $_POST[$name] : $default);
}

require (_CLASS.'order.php');

// logging
//$fp = fopen('webmoney.log', 'a+');
//$str=date('Y-m-d H:i:s').' - ';
//foreach ($_REQUEST as $vn=>$vv) {
//  $str.=$vn.'='.$vv.';';
//}

//fwrite($fp, $str."\n");
//fclose($fp);
// variables prepearing

$inv_id = get_var('orderId');
$order = new order($inv_id);
$order_sum = $order->info['total'];

var_dump($_POST);
exit;

// checking and handling
if (number_format($order_sum,0) == number_format($_POST['recipientAmount'],0)) {
if ($_POST['paymentStatus'] == '5') {
  $sql_data_array = array('orders_status' => MODULE_PAYMENT_OPLATA_ORDER_STATUS_ID);
  os_db_perform(DB_PREFIX.'orders', $sql_data_array, 'update', "orders_id='".$inv_id."'");

  $sql_data_arrax = array('orders_id' => $inv_id,
                          'orders_status_id' => MODULE_PAYMENT_OPLATA_ORDER_STATUS_ID,
                          'date_added' => 'now()',
                          'customer_notified' => '0',
                          'comments' => 'Oplata accepted this order payment');
  os_db_perform(DB_PREFIX.'orders_status_history', $sql_data_arrax);

  echo 'OK'.$inv_id;
}
}

?>