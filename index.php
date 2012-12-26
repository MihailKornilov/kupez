<?php
require_once('include/conf.php');

//xcache_unset('user'.$_GET['viewer_id']);
//xcache_unset('obSpisokFirst');

$vkUser = xcache_get('user'.$_GET['viewer_id']);
if (!$vkUser) {
  $vkUser = (array)$VK->QueryObjectOne("select * from vk_user where viewer_id=".$_GET['viewer_id']);
  if ($vkUser) {
    $update = 1;
  } else {
    $new = 1;
  }
}




if($_SERVER["SERVER_NAME"] == 'kupez.nyandoma.ru') {
  $auth_key = md5($_GET['api_id']."_".$_GET['viewer_id']."_h9IjOkxIMwoW8agQkW3M");
  if($_GET['api_id'] != 2881875 or !$_GET['viewer_id'] or $_GET['auth_key'] != $auth_key) {
    header('Location:http://vk.com/app2881875');
  }

  if(isset($_GET['start'])) {  // выполнение действий при первом входе в приложение
 
    require_once('include/vkapi.class.php');
    $VKAPI = new vkapi(2881875,'h9IjOkxIMwoW8agQkW3M'); 

    if (isset($update) || isset($new)) { // если кэш пользовател€ пуст, либо пользовател€ нет в базе, то обновл€ютс€ полностью его данные (примерно раз в 10 дней)
      $res = $VKAPI->api('users.get',array('uids' => $_GET['viewer_id'], 'fields' => 'photo,sex'));
      $vkUser = array(
        'viewer_id' => $_GET['viewer_id'],
        'first_name' => win1251($res['response'][0]['first_name']),
        'last_name' => win1251($res['response'][0]['last_name']),
        'sex' => $res['response'][0]['sex'],
        'photo' => $res['response'][0]['photo'],
        'app_setup' => 0,
        'menu_left_set' => 0,
        'enter_last' => curTime()
      );
    }

    if (isset($update)) {
      // установил ли приложение
      $app = $VKAPI->api('isAppUser',array('uid'=>$_GET['viewer_id']));
      $vkUser['app_setup'] = $app['response'];

      // поместил ли в левое меню
      $mls = $VKAPI->api('getUserSettings',array('uid'=>$_GET['viewer_id']));
      $set = $mls['response']&256;
      $vkUser['menu_left_set'] = $set > 0 ? 1 : 0;

      $VK->Query("update vk_user set
first_name='".$vkUser['first_name']."',
last_name='".$vkUser['last_name']."',
sex='".$vkUser['sex']."',
photo='".$vkUser['photo']."',
app_setup=".$vkUser['app_setup'].",
menu_left_set=".$vkUser['menu_left_set'].",
enter_last=current_timestamp where viewer_id=".$_GET['viewer_id']);
    }

    xcache_set('user'.$_GET['viewer_id'], $vkUser, 864000);

    if (isset($new)) { // внесение пользовател€ и его данные в базу
      $VK->Query("insert into vk_user (
viewer_id,
first_name,
last_name,
sex,
photo,
enter_last
) values (
".$vkUser['viewer_id'].",
'".$vkUser['first_name']."',
'".$vkUser['last_name']."',
'".$vkUser['sex']."',
'".$vkUser['photo']."',
current_timestamp)");
    }

    // счЄтчик посетителей
    $visit_id = $VK->QRow("select id from visit where viewer_id=".$_GET['viewer_id']." and dtime_add>='".strftime("%Y-%m-%d")." 00:00:00' limit 1");
    if($visit_id)
      $VK->Query("update visit set count_day=count_day+1,dtime_add=current_timestamp where id=".$visit_id);
    else $VK->Query("insert into visit (viewer_id) values (".$_GET['viewer_id'].")");

    // сброс счЄтчика объ€влений
    if($vkUser['menu_left_set'] == 1) {
      $VKAPI->api('secure.setCounter', array('counter'=>0, 'uid'=>$_GET['viewer_id'], 'timestamp'=>time(), 'random'=>rand(1,1000)));
    }

  }
}



if(isset($_GET['hash'])) {
  $ex = explode('_',$_GET['hash']);
  $_GET['my_page'] = $ex[0];
  $_GET['id'] = isset($ex[1]) ? $ex[1] : '';
}


$WR = xcache_get('WR4');
if (!$WR) {
  $WR = $VK->QueryPtPArray("select viewer_id,admin from worker");
  xcache_set('WR4', $WR, 864000);
}

if (!isset($_GET['my_page'])) { $_GET['my_page'] = ''; }

if ($_COOKIE['enter'] and isset($WR[$vkUser['viewer_id']])) {
  switch ($_GET['my_page']) {
  case 'adminHint': include('superadmin/hint/hint_tpl.php');break;       // управление подсказками

  case 'client':        include('gazeta/client/client_tpl.php');break;       // список клиентов
  case 'clientInfo':  include('gazeta/client/clientInfo_tpl.php');break;     // информаци€ о клиенте

  case 'zayav':      include('gazeta/zayav/zayav_tpl.php');break;       // список за€вок
  case 'zayavAdd':     include('gazeta/zayav/zayavAdd_tpl.php');break;  // добавление за€вки
  case 'zayavView':  include('gazeta/zayav/zayavView_tpl.php');break;  // просмотр за€вок
  case 'zayavEdit':    include('gazeta/zayav/zayavEdit_tpl.php');break;  // редактирование за€вок

  case 'report':       include('gazeta/report/report_tpl.php');break;      // отчЄты

  case 'setup':       include('gazeta/setup/setup_tpl.php');break;      // настройки

  case 'nopage':      include('nopage_tpl.php');break;                // несуществующа€ страница
  case 'develop':      include('develop/develop_tpl.php');break;        // комментарии и вопросы по разработке
  default:            include('gazeta/zayav/zayav_tpl.php');break;
  }
} else {
  switch ($_GET['my_page']) {
  case 'vk-visit':      include('vk/visit/visit_tpl.php'); break;
  case 'vk-ob-user':    include('vk/visit/ob-user_tpl.php'); break;
  case 'vk-create':    include('vk/create/create_tpl.php'); break;
  case 'vk-myOb':    include('vk/myOb/myOb_tpl.php'); break;
  case 'vk-myObEdit':  include('vk/myOb/myObEdit_tpl.php'); break;
  default: include('vk/spisok/spisok_tpl.php'); break;
  }
}
?>

