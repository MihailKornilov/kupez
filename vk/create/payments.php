<?php
header("Content-Type: application/json; encoding=utf-8"); 

$secret_key = 'h9IjOkxIMwoW8agQkW3M'; // Защищенный ключ приложения

/*
foreach ($_POST as $n => $p) { $aaa .= $n." = ".$p."\n"; }
$fp = fopen("1.txt","w");
fwrite($fp, $aaa);
fclose($fp);
*/

// Проверка подписи
$sig = $_POST['sig'];
unset($_POST['sig']);
ksort($_POST);
$str = '';
foreach ($_POST as $k => $v) {
  $str .= $k.'='.$v;
}

if ($sig != md5($str.$secret_key)) {
  $response['error'] = array(
    'error_code' => 10,
    'error_msg' => 'Несовпадение вычисленной и переданной подписи запроса.',
    'critical' => true
  );
} else {
  $arr = explode('_',$_POST['item']);
  $votes = $arr[1];
  // Подпись правильная
  switch ($_POST['notification_type']) {
  case 'get_item':
      $response['response'] = array(
//        'item_id' => 25,
        'title' => 'Платная услуга при размещении объявления',
        'photo_url' => 'http://kupez.nyandoma.ru/vk/create/2L0rsK_j2_w.jpg',
        'price' => $votes
      );
    break;

  case 'order_status_change':
    // Изменение статуса заказа в тестовом режиме
    if ($_POST['status'] == 'chargeable') {
      $order_id = intval($_POST['order_id']);
      $app_order_id = 1; // Тут фактического заказа может не быть - тестовый режим.
      $response['response'] = array(
        'order_id' => $order_id,
        'app_order_id' => $app_order_id,
        );
    } else {
      $response['error'] = array(
        'error_code' => 100,
        'error_msg' => 'Передано непонятно что вместо chargeable.',
        'critical' => true
      );
    }
    break;








  case 'get_item_test':
    $response['response'] = array(
//      'item_id' => 25,
      'title' => 'Платная услуга при размещении объявления - test',
      'photo_url' => 'http://kupez.nyandoma.ru/vk/create/2L0rsK_j2_w.jpg',
      'price' => $votes
    );
    break;



  case 'order_status_change_test':
    // Изменение статуса заказа в тестовом режиме
    if ($_POST['status'] == 'chargeable') {
      $order_id = intval($_POST['order_id']);
      $app_order_id = 1; // Тут фактического заказа может не быть - тестовый режим.
      $response['response'] = array(
        'order_id' => $order_id,
        'app_order_id' => $app_order_id,
        );
    } else {
      $response['error'] = array(
        'error_code' => 100,
        'error_msg' => 'Передано непонятно что вместо chargeable.',
        'critical' => true
      );
    }
    break;

  }
}


echo json_encode($response);
?>
