<?php
function _hashRead() {
	$_GET['p'] = isset($_GET['p']) ? $_GET['p'] : 'gazeta';
	if(empty($_GET['hash'])) {
		define('HASH_VALUES', false);
		if(isset($_GET['start'])) {// восстановление последней посещённой страницы
			$_GET['p'] = isset($_COOKIE['p']) ? $_COOKIE['p'] : $_GET['p'];
			$_GET['d'] = isset($_COOKIE['d']) ? $_COOKIE['d'] : '';
			$_GET['d1'] = isset($_COOKIE['d1']) ? $_COOKIE['d1'] : '';
			$_GET['id'] = isset($_COOKIE['id']) ? $_COOKIE['id'] : '';
		} else
			_hashCookieSet();
		return;
	}
	$ex = explode('.', $_GET['hash']);
	$r = explode('_', $ex[0]);
	unset($ex[0]);
	define('HASH_VALUES', empty($ex) ? false : implode('.', $ex));
	$_GET['p'] = $r[0];
	unset($_GET['d']);
	unset($_GET['d1']);
	unset($_GET['id']);
	switch($_GET['p']) {
		case 'client':
			if(isset($r[1]))
				if(preg_match(REGEXP_NUMERIC, $r[1])) {
					$_GET['d'] = 'info';
					$_GET['id'] = intval($r[1]);
				}
			break;
		case 'zayav':
			if(isset($r[1]))
				if(preg_match(REGEXP_NUMERIC, $r[1])) {
					$_GET['d'] = 'info';
					$_GET['id'] = intval($r[1]);
				} else {
					$_GET['d'] = $r[1];
					if(isset($r[2]))
						$_GET['id'] = intval($r[2]);
				}
			break;
		default:
			if(isset($r[1])) {
				$_GET['d'] = $r[1];
				if(isset($r[2]))
					$_GET['d1'] = $r[2];
			}
	}
	_hashCookieSet();
}//_hashRead()
function _hashCookieSet() {
	setcookie('p', $_GET['p'], time() + 2592000, '/');
	setcookie('d', isset($_GET['d']) ? $_GET['d'] : '', time() + 2592000, '/');
	setcookie('d1', isset($_GET['d1']) ? $_GET['d1'] : '', time() + 2592000, '/');
	setcookie('id', isset($_GET['id']) ? $_GET['id'] : '', time() + 2592000, '/');
}//_hashCookieSet()
function _cacheClear() {
	xcache_unset(CACHE_PREFIX.'setup_global');
}//_cacheClear()

function _header() {
	global $html;
	$html =
		'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.
		'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">'.

		'<head>'.
		'<meta http-equiv="content-type" content="text/html; charset=windows-1251" />'.
		'<title>КупецЪ - Приложение '.API_ID.'</title>'.

		//Отслеживание ошибок в скриптах
		(SA ? '<script type="text/javascript" src="http://nyandoma'.(LOCAL ? '' : '.ru').'/js/errors.js?'.VERSION.'"></script>' : '').

		//Стороние скрипты
		'<script type="text/javascript" src="http://nyandoma'.(LOCAL ? '' : '.ru').'/js/jquery-2.0.3.min.js"></script>'.
		'<script type="text/javascript" src="http://nyandoma'.(LOCAL ? '' : '.ru').'/vk/'.(DEBUG ? '' : 'min/').'xd_connection.js"></script>'.

		//Установка начального значения таймера.
		(SA ? '<script type="text/javascript">var TIME=(new Date()).getTime();</script>' : '').

		'<script type="text/javascript">'.
			(LOCAL ? 'for(var i in VK)if(typeof VK[i]=="function")VK[i]=function(){return false};' : '').
			'var DOMAIN="'.DOMAIN.'",'.
				'VALUES="'.VALUES.'",'.
				'VIEWER_ID='.VIEWER_ID.';'.
		'</script>'.

		//Подключение api VK. Стили VK должны стоять до основных стилей сайта
		'<link href="http://nyandoma'.(LOCAL ? '' : '.ru').'/vk/'.(DEBUG ? '' : 'min/').'vk.css?'.VERSION.'" rel="stylesheet" type="text/css" />'.
		'<script type="text/javascript" src="http://nyandoma'.(LOCAL ? '' : '.ru').'/vk/'.(DEBUG ? '' : 'min/').'vk.js?'.VERSION.'"></script>'.

		'<link href="'.SITE.'/css/main.css?'.VERSION.'" rel="stylesheet" type="text/css" />'.
		'<script type="text/javascript" src="'.SITE.'/js/main.js?'.VERSION.'"></script>'.
		($_GET['p'] == 'gazeta' ? '<script type="text/javascript" src="'.SITE.'/js/gazeta.js?'.VERSION.'"></script>' : '').
		'<script type="text/javascript" src="'.SITE.'/js/G_values.js?'.G_VALUES_VERSION.'"></script>'.

		'</head>'.
		'<body>'.
			'<div id="frameBody">'.
				'<iframe id="frameHidden" name="frameHidden"></iframe>';
}//_header()
function _footer() {
	global $html, $sqlQuery, $sqlCount, $sqlTime;
	if(SA) {
		$d = empty($_GET['d']) ? '' :'&pre_d='.$_GET['d'];
		$d1 = empty($_GET['d1']) ? '' :'&pre_d1='.$_GET['d1'];
		$id = empty($_GET['id']) ? '' :'&pre_id='.$_GET['id'];
		$html .= '<div id="admin">'.
			//  ($_GET['p'] != 'sa' && !SA_VIEWER_ID ? '<a href="'.URL.'&p=sa&pre_p='.$_GET['p'].$d.$d1.$id.'">Admin</a> :: ' : '').
			'<a class="debug_toggle'.(DEBUG ? ' on' : '').'">В'.(DEBUG ? 'ы' : '').'ключить Debug</a> :: '.
			'<a id="cache_clear">Очисить кэш ('.VERSION.')</a> :: '.
			'sql <b>'.$sqlCount.'</b> ('.round($sqlTime, 3).') :: '.
			'php '.round(microtime(true) - TIME, 3).' :: '.
			'js <EM></EM>'.
			'</div>'
			.(DEBUG ? $sqlQuery : '');
	}
	$getArr = array(
		'start' => 1,
		'api_url' => 1,
		'api_id' => 1,
		'api_settings' => 1,
		'viewer_id' => 1,
		'viewer_type' => 1,
		'sid' => 1,
		'secret' => 1,
		'access_token' => 1,
		'user_id' => 1,
		'group_id' => 1,
		'is_app_user' => 1,
		'auth_key' => 1,
		'language' => 1,
		'parent_language' => 1,
		'ad_info' => 1,
		'is_secure' => 1,
		'referrer' => 1,
		'lc_name' => 1,
		'hash' => 1
	);
	$gValues = array();
	foreach($_GET as $k => $val) {
		if(isset($getArr[$k]) || empty($_GET[$k])) continue;
		$gValues[] = '"'.$k.'":"'.$val.'"';
	}
	$html .= '<script type="text/javascript">hashSet({'.implode(',', $gValues).'})</script>'.
		'</div></body></html>';
}//_footer()




/*
// Проверка пользователя на наличие в базе. Также обновление при первом входе в Контакт
function vkUserCheck($vku, $update = false)
{
    if ($update or !isset($vku['viewer_id'])) {
        require_once('include/vkapi.class.php');
        $VKAPI = new vkapi(API_ID, API_SECRET);
        $res = $VKAPI->api('users.get',array('uids' => VIEWER_ID, 'fields' => 'photo,sex,country,city'));
        $vku['viewer_id'] = VIEWER_ID;
        $vku['first_name'] = win1251($res['response'][0]['first_name']);
        $vku['last_name'] = win1251($res['response'][0]['last_name']);
        $vku['sex'] = $res['response'][0]['sex'];
        $vku['photo'] = $res['response'][0]['photo'];
        $vku['country_id'] = isset($res['response'][0]['country']) ? $res['response'][0]['country'] : 0;
        $vku['city_id'] = isset($res['response'][0]['city']) ? $res['response'][0]['city'] : 0;
        $vku['menu_left_set'] = 0;
        $vku['enter_last'] = curTime();

        // установил ли приложение
        $app = $VKAPI->api('isAppUser',array('uid'=>VIEWER_ID));
        $vku['app_setup'] = $app['response'];
        // поместил ли в левое меню
        $mls = $VKAPI->api('getUserSettings',array('uid'=>VIEWER_ID));
        $vku['menu_left_set'] = ($mls['response']&256) > 0 ? 1 : 0;
        global $VK;
        $VK->Query('INSERT INTO `vk_user` (
                    `viewer_id`,
                    `first_name`,
                    `last_name`,
                    `sex`,
                    `photo`,
                    `app_setup`,
                    `menu_left_set`,
                    `country_id`,
                    `city_id`,
                    `enter_last`
                    ) values (
                    '.VIEWER_ID.',
                    "'.$vku['first_name'].'",
                    "'.$vku['last_name'].'",
                    '.$vku['sex'].',
                    "'.$vku['photo'].'",
                    '.$vku['app_setup'].',
                    '.$vku['menu_left_set'].',
                    '.$vku['country_id'].',
                    '.$vku['city_id'].',
                    current_timestamp)
                    ON DUPLICATE KEY UPDATE
                    `first_name`="'.$vku['first_name'].'",
                    `last_name`="'.$vku['last_name'].'",
                    `sex`='.$vku['sex'].',
                    `photo`="'.$vku['photo'].'",
                    `app_setup`='.$vku['app_setup'].',
                    `menu_left_set`='.$vku['menu_left_set'].',
                    `country_id`='.$vku['country_id'].',
                    `city_id`='.$vku['city_id'].',
                    `enter_last`=current_timestamp
                    ');

        // сброс счётчика объявлений
        if($vku['menu_left_set'] == 1) {
            $VKAPI->api('secure.setCounter', array('counter'=>0, 'uid'=>VIEWER_ID, 'timestamp'=>time(), 'random'=>rand(1,1000)));
        }
        // счётчик посетителей
        $id = $VK->QRow('SELECT `id` FROM `vk_visit` WHERE `viewer_id`='.VIEWER_ID.' AND `dtime_add`>="'.strftime("%Y-%m-%d").' 00:00:00" LIMIT 1');
        $VK->Query('INSERT INTO `vk_visit` (`id`,`viewer_id`)
                                 VALUES ('.($id ? $id : 0).','.VIEWER_ID.')
                                 ON DUPLICATE KEY UPDATE `count_day`=`count_day`+1,`dtime_add`=current_timestamp');
        $VK->Query('UPDATE `vk_user` SET
                           `count_day`='.($id ? '`count_day`+1' : 1).',
                           `enter_last`=current_timestamp where viewer_id='.VIEWER_ID);
    }
    return $vku;
}

// Несуществующая страница
function nopage($p, $d)
{
?>
<DIV class=nopage>
    Ошибка: несуществующая страница.<BR><BR>
    <DIV class=vkButton onclick="location.href='<?=URL.'&p='.@$p.'&d='.@$d?>'";><BUTTON>Назад</BUTTON></DIV>
</DIV>
<?php
}

// установка баланса клиента
function setClientBalans($client_id = 0) {
	if ($client_id > 0) {
		global $VK;
		$rashod = $VK->QRow("SELECT SUM(`summa`) FROM `gazeta_zayav` WHERE `client_id`=".$client_id);
		$prihod = $VK->QRow("SELECT SUM(`sum`) FROM `gazeta_money` WHERE `status`=1 AND `client_id`=".$client_id);
		$balans = $prihod - $rashod;
		$zayav_count = $VK->QRow("SELECT COUNT(`id`) FROM `gazeta_zayav` WHERE `client_id`=".$client_id);
		$VK->Query("UPDATE `gazeta_client` SET
                        `balans`=".$balans.",
                        `zayav_count`=".$zayav_count." WHERE `id`=".$client_id);
		return $balans;
	} else {
		return 0;
	}
}

$zayavCategory = array(
  1 => 'Объявление',
  2 => 'Реклама',
  3 => 'Поздравление',
  4 => 'Статья'
);


// форматирование текста для внесения в базу
function textFormat($txt) {
	$txt = str_replace("'","&#039;", $txt);
	$txt = str_replace("<","&lt;", $txt);
	$txt = str_replace(">","&gt;", $txt);
	return str_replace("\n","<BR>", $txt);
}

function textUnFormat($txt) {
	$txt=str_replace("&#039;","'",$txt);
	$txt=str_replace("&lt;","<",$txt);
	$txt=str_replace("&gt;",">",$txt);
	return str_replace("<BR>","\n",$txt);
}



// установка баланса клиента
function setClientBalans($client_id = 0) {
	if ($client_id > 0) {
		global $VK;
		$rashod = $VK->QRow("select sum(summa) from zayav where client_id=".$client_id);
		$prihod = $VK->QRow("select sum(summa) from oplata where status=1 and client_id=".$client_id);
		$balans = $prihod - $rashod;
		$VK->Query("update client set balans=".$balans." where id=".$client_id);
		return $balans;
	} else {
		return 0;
	}
}

$WeekName = array(
	1=>'пн',
	2=>'вт',
	3=>'ср',
	4=>'чт',
	5=>'пт',
	6=>'сб',
	0=>'вс'
);
// обновление количества объявлений для рубрики
function rubrikaCountUpdate($rub) {
	global $VK;
	$count = $VK->QRow("select count(id) from zayav where rubrika=".$rub." and status=1 and category=1 and active_day>='".strftime("%Y-%m-%d",time())."'");
	$VK->Query("update setup_rubrika set ob_count=".$count." where id=".$rub);
	xcache_unset('rubrikaCount');
	xcache_unset('obSpisokFirst');
}

*/