<?php
function _hashRead() {
	$_GET['p'] = empty($_GET['p']) ? (GAZETA_WORKER ? 'gazeta' : 'ob') : $_GET['p'];
	if(empty($_GET['hash'])) {
		define('HASH_VALUES', false);
		if(APP_START) {// восстановление последней посещённой страницы
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
	xcache_unset(CACHE_PREFIX.'gn');
	xcache_unset(CACHE_PREFIX.'gn_first_max');
	xcache_unset(CACHE_PREFIX.'person');
	xcache_unset(CACHE_PREFIX.'rubric');
	xcache_unset(CACHE_PREFIX.'rubric_sub');
	xcache_unset(CACHE_PREFIX.'obdop');
	xcache_unset(CACHE_PREFIX.'polosa');
	xcache_unset(CACHE_PREFIX.'invoice');
	xcache_unset(CACHE_PREFIX.'income');
	xcache_unset(CACHE_PREFIX.'expense');
	GvaluesCreate();
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
		(SA ? '<script type="text/javascript" src="//nyandoma'.(LOCAL ? '' : '.ru').'/js/errors.js?'.VERSION.'"></script>' : '').

		//Стороние скрипты
		'<script type="text/javascript" src="//nyandoma'.(LOCAL ? '' : '.ru').'/js/jquery-2.0.3.min.js"></script>'.
		'<script type="text/javascript" src="//'.(LOCAL ? 'nyandoma/vk' : 'vk.com/js/api').'/xd_connection.js?20"></script>'.

		//Установка начального значения таймера.
		(SA ? '<script type="text/javascript">var TIME=(new Date()).getTime();</script>' : '').

		'<script type="text/javascript">'.
			(LOCAL ? 'for(var i in VK)if(typeof VK[i]=="function")VK[i]=function(){return false};' : '').
			'var DOMAIN="'.DOMAIN.'",'.
				'VALUES="'.VALUES.'",'.
			($_GET['p'] == 'gazeta' ?
				'GN_FIRST_ACTIVE='.GN_FIRST_ACTIVE.','.
				'GN_LAST_ACTIVE='.GN_LAST_ACTIVE.','.
				'ADMIN='.VIEWER_ADMIN.','
			: '').
				'VIEWER_ID='.VIEWER_ID.';'.
		'</script>'.

		//Подключение api VK. Стили VK должны стоять до основных стилей сайта
		'<link href="http://nyandoma'.(LOCAL ? '' : '.ru').'/vk'.(defined('TEST') ? 'test' : '').'/vk'.(DEBUG ? '' : '.min').'.css?'.VERSION.'" rel="stylesheet" type="text/css" />'.
		'<script type="text/javascript" src="//nyandoma'.(LOCAL ? '' : '.ru').'/vk'.(defined('TEST') ? 'test' : '').'/vk'.(DEBUG ? '' : '.min').'.js?'.VERSION.'"></script>'.

		'<script type="text/javascript" src="'.SITE.'/js/G_values.js?'.G_VALUES_VERSION.'"></script>'.

		'<link href="'.SITE.'/css/main'.(DEBUG ? '' : '.min').'.css?'.VERSION.'" rel="stylesheet" type="text/css" />'.
		'<script type="text/javascript" src="'.SITE.'/js/main'.(DEBUG ? '' : '.min').'.js?'.VERSION.'"></script>'.

		($_GET['p'] == 'gazeta' ? '<link href="'.SITE.'/css/gazeta'.(DEBUG ? '' : '.min').'.css?'.VERSION.'" rel="stylesheet" type="text/css" />' : '').
		($_GET['p'] == 'gazeta' ? '<script type="text/javascript" src="'.SITE.'/js/gazeta'.(DEBUG ? '' : '.min').'.js?'.VERSION.'"></script>' : '').

		($_GET['p'] == 'admin' ? '<link href="'.SITE.'/css/admin'.(DEBUG ? '' : '.min').'.css?'.VERSION.'" rel="stylesheet" type="text/css" />' : '').
		($_GET['p'] == 'admin' ? '<script type="text/javascript" src="'.SITE.'/js/admin'.(DEBUG ? '' : '.min').'.js?'.VERSION.'"></script>' : '').

		'</head>'.
		'<body>'.
			'<div id="frameBody">'.
				'<iframe id="frameHidden" name="frameHidden"></iframe>';
}//_header()

function GvaluesCreate() {// составление файла G_values.js
	$sql = "SELECT * FROM `setup_global` LIMIT 1";
	$g = mysql_fetch_assoc(query($sql));

	$save = //'function _toSpisok(s){var a=[];for(k in s)a.push({uid:k,title:s[k]});return a}'.
		'function _toAss(s){var a=[];for(var n=0;n<s.length;a[s[n].uid]=s[n].title,n++);return a}'.

	"\n".'var WORKER_SPISOK='.query_selJson("SELECT `viewer_id`,CONCAT(`first_name`,' ',`last_name`) FROM `vk_user`
											 WHERE `gazeta_worker`=1
											   AND `viewer_id`!=982006
											 ORDER BY `dtime_add`").','.
		"\n".'CATEGORY_SPISOK=[{uid:1,title:"Объявление"},{uid:2,title:"Реклама"},{uid:3,title:"Поздравление"},{uid:4,title:"Статья"}],'.
		"\n".'PERSON_SPISOK='.query_selJson("SELECT `id`,`name` FROM `setup_person` ORDER BY `sort`").','.
		"\n".'RUBRIC_SPISOK='.query_selJson("SELECT `id`,`name` FROM `setup_rubric` ORDER BY `sort`").','.
		"\n".'RUBRIC_ASS=_toAss(RUBRIC_SPISOK),'.
		"\n".'INCOME_SPISOK='.query_selJson("SELECT `id`,`name` FROM `setup_income` ORDER BY `sort`").','.
		"\n".'SKIDKA_SPISOK='.query_selJson("SELECT `razmer`,CONCAT(`razmer`,'%') FROM `setup_skidka` ORDER BY `razmer`").','.
		"\n".'TXT_LEN_FIRST='.$g['txt_len_first'].','.
		"\n".'TXT_CENA_FIRST='.$g['txt_cena_first'].','.
		"\n".'TXT_LEN_NEXT='.$g['txt_len_next'].','.
		"\n".'TXT_CENA_NEXT='.$g['txt_cena_next'].','.
		"\n".'OBDOP_SPISOK='.query_selJson('SELECT `id`,`name` FROM `setup_ob_dop` ORDER BY `id`').','.
		"\n".'OBDOP_CENA_ASS='.query_ptpJson('SELECT `id`,`cena` FROM `setup_ob_dop` ORDER BY `id`').','.
		"\n".'POLOSA_COUNT=[{uid:4,title:4},{uid:6,title:6},{uid:8,title:8},{uid:10,title:10},{uid:12,title:12}],'.
		"\n".'POLOSA_SPISOK='.query_selJson('SELECT `id`,`name` FROM `setup_polosa_cost` ORDER BY `sort`').','.
		"\n".'POLOSA_CENA_ASS='.query_ptpJson('SELECT `id`,ROUND(`cena`) FROM `setup_polosa_cost` ORDER BY `id`').','.
		"\n".'POLOSA_NUM='.query_ptpJson('SELECT `id`,`polosa` FROM `setup_polosa_cost` WHERE `polosa`').','.
		"\n".'INVOICE_SPISOK='.query_selJson("SELECT `id`,`name` FROM `gazeta_invoice` ORDER BY `id`").','.
		"\n".'EXPENSE_SPISOK='.query_selJson('SELECT `id`,`name` FROM `setup_expense` ORDER BY `sort`').','.
		"\n".'EXPENSE_WORKER='.query_ptpJson("SELECT `id`,`show_worker` FROM `setup_expense` WHERE `show_worker`").','.
		"\n".'COUNTRY_SPISOK=['.
			'{uid:1,title:"Россия"},'.
			'{uid:2,title:"Украина"},'.
			'{uid:3,title:"Беларусь"},'.
			'{uid:4,title:"Казахстан"},'.
			'{uid:5,title:"Азербайджан"},'.
			'{uid:6,title:"Армения"},'.
			'{uid:7,title:"Грузия"},'.
			'{uid:8,title:"Израиль"},'.
			'{uid:11,title:"Кыргызстан"},'.
			'{uid:12,title:"Латвия"},'.
			'{uid:13,title:"Литва"},'.
			'{uid:14,title:"Эстония"},'.
			'{uid:15,title:"Молдова"},'.
			'{uid:16,title:"Таджикистан"},'.
			'{uid:17,title:"Туркмения"},'.
			'{uid:18,title:"Узбекистан"}],'.
		"\n".'COUNTRY_ASS=_toAss(COUNTRY_SPISOK),'.
		'';

	$sql = "SELECT * FROM `setup_rubric_sub` ORDER BY `rubric_id`,`sort`";
	$q = query($sql);
	$sub = array();
	while($r = mysql_fetch_assoc($q)) {
		if(!isset($sub[$r['rubric_id']]))
			$sub[$r['rubric_id']] = array();
		$sub[$r['rubric_id']][] = '{uid:'.$r['id'].',title:"'.$r['name'].'"}';
	}
	$v = array();
	foreach($sub as $n => $sp)
		$v[] = $n.':['.implode(',', $sp).']';
	$save .= "\n".'RUBRIC_SUB_SPISOK={'.implode(',', $v).'},'.
			 "\n".'RUBRIC_SUB_ASS={0:""};'.
			 "\n".'for(k in RUBRIC_SUB_SPISOK){for(n=0;n<RUBRIC_SUB_SPISOK[k].length;n++){var sp=RUBRIC_SUB_SPISOK[k][n];RUBRIC_SUB_ASS[sp.uid]=sp.title;}}';


	$sql = "SELECT * FROM `gazeta_nomer` ORDER BY `general_nomer`";
	$q = query($sql);
	$gn = array();
	while($r = mysql_fetch_assoc($q))
		array_push($gn, "\n".$r['general_nomer'].':{'.
			'week:'.$r['week_nomer'].','.
			'pub:"'.$r['day_public'].'",'.
			'txt:"'.FullData($r['day_public'], 0, 0, 1).'",'.
			'pc:'.$r['polosa_count'].
			'}');

	$save .= "\n".'GN={'.implode(',', $gn).'};';

	$fp = fopen(PATH.'/js/G_values.js', 'w+');
	fwrite($fp, $save);
	fclose($fp);

	query("UPDATE `setup_global` SET `g_values`=`g_values`+1");
	xcache_unset(CACHE_PREFIX.'setup_global');
} // end of GvaluesCreate()
function _rubric($rubric_id=false) {//Список изделий для заявок
	if(!defined('RUBRIC_LOADED') || $rubric_id === false) {
		$key = CACHE_PREFIX.'rubric';
		$arr = xcache_get($key);
		if(empty($arr)) {
			$sql = "SELECT `id`,`name` FROM `setup_rubric` ORDER BY `sort`";
			$q = query($sql);
			while($r = mysql_fetch_assoc($q))
				$arr[$r['id']] = $r['name'];
			xcache_set($key, $arr, 86400);
		}
		if(!defined('RUBRIC_LOADED')) {
			foreach($arr as $id => $name)
				define('RUBRIC_'.$id, $name);
			define('RUBRIC_0', '');
			define('RUBRIC_LOADED', true);
		}
	}
	return $rubric_id !== false ? constant('RUBRIC_'.$rubric_id) : $arr;
}//_rubric()
function _rubricsub($item_id=false) {//Список изделий для заявок
	if(!defined('RUBRICSUB_LOADED') || $item_id === false) {
		$key = CACHE_PREFIX.'rubric_sub';
		$arr = xcache_get($key);
		if(empty($arr)) {
			$sql = "SELECT `id`,`name` FROM `setup_rubric_sub` ORDER BY `sort`";
			$q = query($sql);
			while($r = mysql_fetch_assoc($q))
				$arr[$r['id']] = $r['name'];
			xcache_set($key, $arr, 86400);
		}
		if(!defined('RUBRICSUB_LOADED')) {
			foreach($arr as $id => $name)
				define('RUBRICSUB_'.$id, $name);
			define('RUBRICSUB_0', '');
			define('RUBRICSUB_LOADED', true);
		}
	}
	return $item_id !== false ? constant('RUBRICSUB_'.$item_id) : $arr;
}//_rubricsub()

function viewerSettingsHistory($old, $u) {
	if($old['is_app_user'] != $u['is_app_user'])
		_historyInsert(
			$u['is_app_user'] ? 2 : 3,
			array('viewer_id' => $u['id']),
			'vk_history'
		);

	if($old['rule_menu_left'] != $u['rule_menu_left'])
		_historyInsert(
			$u['rule_menu_left'] ? 4 : 5,
			array('viewer_id' => $u['id']),
			'vk_history'
		);

	if($old['rule_notify'] != $u['rule_notify'])
		_historyInsert(
			$u['rule_notify'] ? 6 : 7,
			array('viewer_id' => $u['id']),
			'vk_history'
		);
}//viewerSettingsHistory()


function ob() {//Главная страница с объявлениями
	if($insert_id = _isnum(@$_GET['insert_id'])) {
		$wallpost = _isbool(@$_GET['wallpost']);
		_historyInsert(
			$wallpost ? 8 : 9,
			array('ob_id' => $insert_id),
			'vk_history'
		);
	}

	$sql =
		"SELECT
			`country_id`,
			`country_name`
		FROM `vk_ob`
		WHERE !`deleted`
		  AND `country_id`
		  AND `country_name`!=''
		  AND `day_active`>=DATE_FORMAT(NOW(), '%Y-%m-%d')
		GROUP BY `country_id`
		ORDER BY `country_name`";
	$country = query_ass($sql);

	$sql = "SELECT
				`city_id`,
				`city_name`,
				`country_id`
			FROM `vk_ob`
			WHERE !`deleted`
			  AND `city_id`
			  AND `city_name`!=''
			  AND `day_active`>=DATE_FORMAT(NOW(), '%Y-%m-%d')
			GROUP BY `city_id`
			ORDER BY `city_name`";
	$q = query($sql);
	$sub = array();
	while($r = mysql_fetch_assoc($q)) {
		if(!isset($sub[$r['country_id']]))
			$sub[$r['country_id']] = array();
		$sub[$r['country_id']][] = '{uid:'.$r['city_id'].',title:"'.$r['city_name'].'"}';
	}
	$city = array();
	foreach($sub as $n => $sp)
		$city[] = $n.':['.implode(',', $sp).']';

	$rubric = array(0 => 'Все объявления') + _rubric();
	//Количество объявлений для каждой рубрики
	$sql = "SELECT
				`rubric_id`,
				COUNT(`id`) AS `count`
			FROM `vk_ob`
			WHERE !`deleted`
			  AND `day_active`>=DATE_FORMAT(NOW(), '%Y-%m-%d')
			GROUP BY `rubric_id`";
	$q = query($sql);
	while($r = mysql_fetch_assoc($q))
		$rubric[$r['rubric_id']] .= '<b>'.$r['count'].'</b>';

	$counts = '';
	if(SA) {
		$userDay =    query_value("SELECT COUNT(*) FROM `vk_user` WHERE `enter_last` LIKE '".strftime('%Y-%m-%d')."%'");
		$userNew =    query_value("SELECT COUNT(*) FROM `vk_user` WHERE `dtime_add` LIKE '".strftime('%Y-%m-%d')."%'");
		$user24 =     query_value("SELECT COUNT(*) FROM `vk_user` WHERE `enter_last`>DATE_SUB(NOW(), INTERVAL 1 DAY)");
		$userMon =    query_value("SELECT COUNT(*) FROM `vk_user` WHERE `enter_last` LIKE '".strftime('%Y-%m-')."%'");
		$user30days = query_value("SELECT COUNT(*) FROM `vk_user` WHERE `enter_last`>DATE_SUB(NOW(), INTERVAL 30 DAY)");

		$obDay =    query_value("SELECT COUNT(*) FROM `vk_ob` WHERE !`deleted` AND !`gazeta_id` AND `dtime_add` LIKE '".strftime('%Y-%m-%d')."%'");
		$ob24 =     query_value("SELECT COUNT(*) FROM `vk_ob` WHERE !`deleted` AND !`gazeta_id` AND `dtime_add`>DATE_SUB(NOW(), INTERVAL 1 DAY)");
		$obMon =    query_value("SELECT COUNT(*) FROM `vk_ob` WHERE !`deleted` AND !`gazeta_id` AND `dtime_add` LIKE '".strftime('%Y-%m-')."%'");
		$ob30days = query_value("SELECT COUNT(*) FROM `vk_ob` WHERE !`deleted` AND !`gazeta_id` AND `dtime_add`>DATE_SUB(NOW(), INTERVAL 30 DAY)");

		$counts =
			'<table class="stat">'.
				'<tr><td class="label r">Сегодня:'.
					'<td><a href="'.URL.'&p=admin"><b>'.$userDay.'</b></a>'.($userNew ? ' <span class="new">+'.$userNew.'</span>' : '').
					'<td>'.($obDay ? $obDay : '').
				'<tr><td class="label r">24 часа:<td>'.$user24.'<td>'.($ob24 ? $ob24 : '').
				'<tr><td class="label r">'._monthDef(strftime('%m')).':<td>'.$userMon.'<td>'.($obMon ? $obMon : '').
				'<tr><td class="label r">30 дней:<td>'.$user30days.'<td>'.($ob30days ? $ob30days : '').
			'</table>';
	}

	$data = ob_spisok(array(
		'country_id' => count($country) == 1 ? key($country) : 0
	));

	return
	'<script type="text/javascript">'.
		'var COUNTRIES='._selJson($country).','.
			'CITIES={'.implode(',', $city).'};'.
	'</script>'.
	'<div class="ob-spisok">'.
		'<table class="tfind">'.
			'<tr><td><div id="find"></div>'.
				'<th><div class="vkButton"><button>Разместить объявление</button></div>'.
		'</table>'.
		'<div class="result">'.$data['result'].'</div>'.
		'<table class="tabLR">'.
			'<tr><td class="left">'.$data['spisok'].
				'<td class="right">'.
					'<div id="filter_pre">'.
						'<div id="filter">'.
							'<div class="findHead region">Регион</div>'.
							'<input type="hidden" id="countries"'.(count($country) == 1 ? ' value="'.key($country).'"' : '').' />'.
							'<div class="city-sel'.(count($country) == 1 ? '' : ' dn').'"><input type="hidden" id="cities"></div>'.
							'<div class="findHead">Рубрики</div>'.
							_rightLink('rub', $rubric).
							'<input type="hidden" id="rubsub" value="0" />'.
							'<div class="findHead">Дополнительно</div>'.
							_check('withfoto', 'Только с фото').
					  (SA ? _check('nokupez', 'Не КупецЪ') : '').
							$counts.
						'</div>'.
					'</div>'.
		'</table>'.
	'</div>';
}//ob()
function obFilter($v=array()) {
	return array(
		'page' => !empty($v['page']) && preg_match(REGEXP_NUMERIC, $v['page']) ? intval($v['page']) : 1,
		'limit' => !empty($v['limit']) && preg_match(REGEXP_NUMERIC, $v['limit']) ? intval($v['limit']) : 20,
		'find' => !empty($v['find']) ? win1251(htmlspecialchars(trim($v['find']))) : '',
		'find_query' => !empty($v['find_query']) && preg_match(REGEXP_NUMERIC, $v['find_query']) ? intval($v['find_query']) : 0,
		'country_id' => !empty($v['country_id']) && preg_match(REGEXP_NUMERIC, $v['country_id']) ? intval($v['country_id']) : 0,
		'city_id' => !empty($v['city_id']) && preg_match(REGEXP_NUMERIC, $v['city_id']) ? intval($v['city_id']) : 0,
		'rubric_id' => !empty($v['rubric_id']) && preg_match(REGEXP_NUMERIC, $v['rubric_id']) ? intval($v['rubric_id']) : 0,
		'rubric_sub_id' => !empty($v['rubric_sub_id']) && preg_match(REGEXP_NUMERIC, $v['rubric_sub_id']) ? intval($v['rubric_sub_id']) : 0,
		'withfoto' => isset($v['withfoto']) && preg_match(REGEXP_BOOL, $v['withfoto']) ? intval($v['withfoto']) : 0,
		'nokupez' => SA && isset($v['nokupez']) && preg_match(REGEXP_BOOL, $v['nokupez']) ? intval($v['nokupez']) : 0
	);
}//obFilter()
function ob_spisok($v=array()) {
	$filter = obFilter($v);

	$limit = $filter['limit'];
	$page = $filter['page'];

	$cond = "!`deleted` AND `day_active`>=DATE_FORMAT(NOW(), '%Y-%m-%d')";

	if($filter['find']) {
		$cond .= " AND `txt` LIKE '%".$filter['find']."%'";
		$reg = '/('.$filter['find'].')/i';
	}
	if($filter['country_id'])
		$cond .= " AND `country_id`=".$filter['country_id'];
	if($filter['city_id'])
		$cond .= " AND `city_id`=".$filter['city_id'];
	if($filter['rubric_id'])
		$cond .= " AND `rubric_id`=".$filter['rubric_id'];
	if($filter['rubric_sub_id'])
		$cond .= " AND `rubric_sub_id`=".$filter['rubric_sub_id'];
	if($filter['withfoto'])
		$cond .= " AND `image_id`";
	if(SA && $filter['nokupez'])
		$cond .= " AND !`gazeta_id`";

	$all = query_value("SELECT COUNT(`id`) AS `all` FROM `vk_ob` WHERE ".$cond);

	if($page == 1 && $filter['find_query'] && $filter['find']) {
		$sql = "INSERT INTO `vk_ob_find_query` (
						`txt`,
						`rows`,
						`viewer_id_add`
					) VALUES (
						'".addslashes($filter['find'])."',
						".$all.",
						".VIEWER_ID."
					)";
		query($sql);
	}

	$links = '<a href="'.URL.'&p=ob&d=my" class="my">Мои объявления</a>'.
			 (GAZETA_WORKER ? '<a href="'.URL.'&p=gazeta&d=zayav" class="prog">Войти в программу</a>' : '');
	if(!$all)
		return array(
			'all' => 0,
			'result' => 'Объявлений не найдено.'.$links,
			'spisok' => '<div class="_empty">Объявлений не найдено.</div>',
			'filter' => $filter
		);

	$send['all'] = $all;
	$send['result'] = 'Показано '.$all.' объявлен'._end($all, 'ие', 'ия', 'ий').$links;
	$send['filter'] = $filter;

	$start = ($page - 1) * $limit;
	$sql = "SELECT *
			FROM `vk_ob`
			WHERE ".$cond."
			ORDER BY `id` DESC
			LIMIT ".$start.",".$limit;
	$q = query($sql);
	$ob = array();
	while($r = mysql_fetch_assoc($q)) {
		if($filter['find']) {
			if(preg_match($reg, $r['txt']))
				$r['txt'] = preg_replace($reg, '<em>\\1</em>', $r['txt'], 1);
		}
		$ob[$r['id']] = $r;
	}

	$send['spisok'] = '';
	foreach($ob as $r)
		$send['spisok'] .= ob_unit($r);

	if($start + $limit < $all) {
		$c = $all - $start - $limit;
		$c = $c > $limit ? $limit : $c;
		$send['spisok'] .=
			'<div class="_next ob_next" val="'.($page + 1).'">'.
			'<span>Показать ещё '.$c.' объявлен'._end($c, 'ие', 'ия', 'ий').'</span>'.
			'</div>';
	}

	return $send;
}//ob_spisok()
function ob_unit($r) {
	return
	'<div class="ob-unit'.(isset($r['edited']) ? ' edited' : '').'"'.(SA ? ' val="'.$r['id'].'"' : '').'>'.
	(SA ?
		'<div class="sa-edit">'.
			'<span class="dt">'.FullDataTime($r['dtime_add']).'</span>'.
			($r['viewer_id_add'] ? '<a href="'.URL.'&p=admin&d=user&id='.$r['viewer_id_add'].'">'._viewer($r['viewer_id_add'], 'name').'</a>' : '').
			($r['gazeta_id'] ? 'КупецЪ' : '').
			'<div class="ed">'.
				'<a class="to-arch">в архив</a>'.
				'<div class="img_edit"></div>'.
			'</div>'.
		'</div>'
	: '').
		'<table class="utab">'.
			'<tr><td class="txt">'.
					'<a class="rub" val="'.$r['rubric_id'].'">'._rubric($r['rubric_id']).'</a><u>»</u>'.
					($r['rubric_sub_id'] ? '<a class="rubsub" val="'.$r['rubric_id'].'_'.$r['rubric_sub_id'].'">'._rubricsub($r['rubric_sub_id']).'</a><u>»</u>' : '').
					$r['txt'].
					($r['telefon'] ? '<div class="tel">'.$r['telefon'].'</div>' : '').
		($r['image_id'] ?
				'<td class="foto"><img src="'.$r['image_link'].'" class="_iview" val="'.$r['image_id'].'" />'
		: '').
			'<tr><td class="adres" colspan="2">'.
				($r['city_name'] ? $r['country_name'].', '.$r['city_name']  : '').
				($r['viewer_id_show'] ? _viewer($r['viewer_id_add'], 'link')  : '').
		'</table>'.
	'</div>';
}//ob_unit()

function ob_create() {
	query("UPDATE `images` SET `deleted`=1 WHERE `owner`='".VIEWER_ID."'");
	$dop = array(
		0 => 'Не выделять',
		1 => 'Обвести в рамку',
		2 => 'Выделить жирным шрифтом',
		3 => 'На чёрном фоне'
	);
	switch(@$_GET['back']) {
		case 'my': $back = '&d=my'; break;
		default: $back = '';
	}
	return
	'<script type="text/javascript">var VIEWER_LINK="'.addslashes(_viewer(VIEWER_ID, 'link')).'";</script>'.
	'<div id="ob-create">'.
		'<div class="headName">Создание нового объявления</div>'.
		'<div class="_info">'.
			'<p>Пожалуйста, заполните все необходимые поля. После размещения объявление сразу становится доступно для других пользователей ВКонтакте.'.
			'<p>Сотрудники приложения Купецъ оставляют за собой право изменять или запретить к показу объявление, если оно нарушает <a>правила</a>.'.
			'<p>Объявление будет размещено сроком на 1 месяц, в дальнейшем Вы сможете продлить этот срок.'.
		'</div>'.
		'<table class="tab">'.
			'<tr><td class="label">Рубрика:'.
				'<td><input type="hidden" id="rubric_id" />'.
					'<input type="hidden" id="rubric_sub_id" />'.
			'<tr><td class="label top">Текст:<td><textarea id="txt"></textarea>'.
			'<tr><td class="label">Контактные телефоны:<td><input type="text" id="telefon" maxlength="200" />'.
			'<tr><td><td>'._imageAdd(array('owner'=>VIEWER_ID)).
			'<tr><td class="label topi">Регион:'.
				'<td><input type="hidden" id="country_id" value="'._viewer(VIEWER_ID, 'country_id').'" />'.
					'<input type="hidden" id="city_id" />'.
			'<tr><td class="label">Показывать имя из VK:<td>'._check('viewer_id_show').
			'<tr><td class="label">Платные сервисы:<td>'._check('pay_service').
		'</table>'.

		'<table class="tab pay dn">'.
			'<tr><td class="label"><td>'._radio('dop', $dop, 0, 1).
			//'<tr><td class="label">Поднять объявление:<td>'._check('to_top').
		'</table>'.

		'<table class="tab">'.
			'<tr><td class="label">'.
				'<td><div class="vkButton"><button>Разместить объявление<span></span></button></div>'.
					'<div class="vkCancel" val="'.$back.'"><button>Отмена</button></div>'.
		'</table>'.

		'<div class="headName">Предосмотр объявления</div>'.
		'<div id="preview"></div>'.
	'</div>';
}//ob_create()

function ob_my() {
	$data = ob_my_spisok();
	$menu = array(
		0 => 'Все объявления',
		1 => 'Активные',
		2 => 'Архив',
	);
	return
	'<div id="ob-my">'.
		'<div class="path"><a href="'.URL.'&p=ob">КупецЪ</a> » Мои объявления</div>'.
		'<div class="result">'.$data['result'].'</div>'.
		'<table class="tabLR">'.
			'<tr><td class="left">'.$data['spisok'].
				'<td class="right">'.
					'<div id="buttonCreate"><a href="'.URL.'&p=ob&d=create&back=my">Новое объявление</a></div>'.
					_rightLink('menu', $menu).
		'</table>'.
	'</div>';
}//ob_my()
function obMyFilter($v=array()) {
	return array(
		'page' => !empty($v['page']) && preg_match(REGEXP_NUMERIC, $v['page']) ? intval($v['page']) : 1,
		'limit' => !empty($v['limit']) && preg_match(REGEXP_NUMERIC, $v['limit']) ? intval($v['limit']) : 20,
		'menu' => isset($v['menu']) && preg_match(REGEXP_NUMERIC, $v['menu']) ? intval($v['menu']) : 0
	);
}//obMyFilter()
function ob_my_spisok($v=array()) {
	$filter = obMyFilter($v);

	$limit = $filter['limit'];
	$page = $filter['page'];

	$cond = "!`deleted` AND `viewer_id_add`=".VIEWER_ID;

	switch($filter['menu']) {
		case 1: $cond .= " AND `day_active`>=DATE_FORMAT(NOW(),'%Y-%m-%d')"; break;
		case 2: $cond .= " AND `day_active`<DATE_FORMAT(NOW(),'%Y-%m-%d')"; break;
	}

	$all = query_value("SELECT COUNT(`id`) AS `all` FROM `vk_ob` WHERE ".$cond);

	if(!$all)
		return array(
			'all' => 0,
			'result' => 'Объявлений не найдено.',
			'spisok' => '<div class="_empty">Объявлений не найдено.</div>',
			'filter' => $filter
		);

	$send['all'] = $all;
	$send['result'] = 'Показан'._end($all, '', 'о').' '.$all.' объявлен'._end($all, 'ие', 'ия', 'ий');
	$send['filter'] = $filter;
	$send['spisok'] = '';

	$start = ($page - 1) * $limit;
	$sql = "SELECT *
			FROM `vk_ob`
			WHERE ".$cond."
			ORDER BY `id` DESC
			LIMIT ".$start.",".$limit;
	$q = query($sql);
	while($r = mysql_fetch_assoc($q))
		$send['spisok'] .= ob_my_unit($r);

	if($start + $limit < $all) {
		$c = $all - $start - $limit;
		$c = $c > $limit ? $limit : $c;
		$send['spisok'] .=
			'<div class="_next" val="'.($page + 1).'">'.
			'<span>Показать ещё '.$c.' объявлен'._end($c, 'ие', 'ия', 'ий').'</span>'.
			'</div>';
	}

	return $send;
}//ob_my_spisok()
function ob_my_unit($r) {
	$dayTime = strtotime($r['day_active']) - time() + 86400;
	$dayLast = $dayTime > 0 ? floor($dayTime / 86400) : 0;
	return
	'<div class="ob-unit'.($dayLast ? '' : ' arc').(isset($r['edited']) ? ' edited' : '').'" val="'.$r['id'].'">'.
		'<div class="edit">'.
			FullData($r['dtime_add'], 0, 1).
			'<span class="last">'.
				($dayLast ? 'Остал'._end($dayLast, 'ся ', 'ось ').$dayLast._end($dayLast, ' день', ' дня', ' дней') : 'в архиве').
			'</span>'.
			'<div class="icon">'.
				'<div class="img_edit'._tooltip('Редактировать', -50).'</div>'.
				'<div class="img_del'._tooltip('Удалить', -29).'</div>'.
			'</div>'.
		'</div>'.
		'<table class="utab">'.
			'<tr><td class="txt">'.
					'<span class="rub">'._rubric($r['rubric_id']).'</span><u>»</u>'.
					($r['rubric_sub_id'] ? '<span class="rubsub">'._rubricsub($r['rubric_sub_id']).'</span><u>»</u>' : '').
					nl2br($r['txt']).
					($r['telefon'] ? '<div class="tel">'.$r['telefon'].'</div>' : '').
					($r['image_id'] ? '<td class="foto"><img src="'.$r['image_link'].'" class="_iview" val="'.$r['image_id'].'" />' : '').
			'<tr><td class="adres" colspan="2">'.
				($r['city_name'] ? $r['country_name'].', '.$r['city_name']  : '').
				($r['viewer_id_show'] ? _viewer($r['viewer_id_add'], 'link')  : '').
		'</table>'.
	'</div>';
}//ob_my_unit()

function ob_history() {
	$data = ob_history_data();
	return
		'<table class="tabLR">'.
			'<tr><td class="left">'.$data['spisok'].
				'<td class="right">'.
		'</table>';
}//ob_history()
function ob_history_types($v) {
	switch($v['type']) {
		case 1: return 'Новый <a href="'.URL.'&p=admin&d=user&id='.$v['viewer_id'].'">посетитель</a>.';

		case 2: return (!$v['viewer_id_add'] ? '<a href="'.URL.'&p=admin&d=user&id='.$v['viewer_id'].'">'._viewer($v['viewer_id'], 'name').'</a> у' : 'У').
					   'становил'.(_viewer($v['viewer_id'], 'sex') == 1 ? 'a' : '').' приложение.';
		case 3: return (!$v['viewer_id_add'] ? '<a href="'.URL.'&p=admin&d=user&id='.$v['viewer_id'].'">'._viewer($v['viewer_id'], 'name').'</a> у' : 'У').
						'далил'.(_viewer($v['viewer_id'], 'sex') == 1 ? 'a' : '').' приложение.';

		case 4: return (!$v['viewer_id_add'] ? '<a href="'.URL.'&p=admin&d=user&id='.$v['viewer_id'].'">'._viewer($v['viewer_id'], 'name').'</a> д' : 'Д').
						'обавил'.(_viewer($v['viewer_id'], 'sex') == 1 ? 'a' : '').' приложение в левое меню.';
		case 5: return (!$v['viewer_id_add'] ? '<a href="'.URL.'&p=admin&d=user&id='.$v['viewer_id'].'">'._viewer($v['viewer_id'], 'name').'</a> у' : 'У').
						'далил'.(_viewer($v['viewer_id'], 'sex') == 1 ? 'a' : '').' приложение из левого меню.';

		case 6: return (!$v['viewer_id_add'] ? '<a href="'.URL.'&p=admin&d=user&id='.$v['viewer_id'].'">'._viewer($v['viewer_id'], 'name').'</a> р' : 'Р').
						'азрешил'.(_viewer($v['viewer_id'], 'sex') == 1 ? 'a' : '').' приложению отправлять уведомления.';
		case 7: return (!$v['viewer_id_add'] ? '<a href="'.URL.'&p=admin&d=user&id='.$v['viewer_id'].'">'._viewer($v['viewer_id'], 'name').'</a> з' : 'З').
						'апретил'.(_viewer($v['viewer_id'], 'sex') == 1 ? 'a' : '').' приложению отправлять уведомления.';

		case 8: return 'Разместил объявление '.$v['ob_id'].' на стене своей страницы.';
		case 9: return 'Отклонил размещение объявления '.$v['ob_id'].' на стене своей страницы.';

		default: return $v['type'];
	}
}//ob_history_types()
function ob_history_data($v=array()) {
	return _history(
		'ob_history_types',
		array(),
		$v,
		array(
			'table' => 'vk_history'
		)
	);
}//history()




/*
function to_new_images() {//Перенос картинок в новый формат
	define('IMLINK', 'http://'.DOMAIN.'/files/images/');
	define('IMPATH', PATH.'files/images/');
	$sql = "SELECT * FROM `vk_ob` WHERE LENGTH(file) LIMIT 300";
	$q = query($sql);
	while($r = mysql_fetch_assoc($q)) {
		$sort = 0;
		$image_id = 0;
		$image_link = '';
		foreach(explode('_', $r['file']) as $i) {
			$name = str_replace('http://kupez.nyandoma.ru/files/images/', '', $i);

			$name_small = IMPATH.$name.'s.jpg';
			$im = imagecreatefromjpeg($name_small);
			$x_small = imagesx($im);
			$y_small = imagesy($im);
			$name_small_new = 'ob'.$r['id'].'-'.$name.($name[strlen($name) - 1] != '-' ? '-' : '').'s.jpg';
			rename($name_small, IMPATH.$name_small_new);

			$name_big = IMPATH.$name.'b.jpg';
			$im = imagecreatefromjpeg(PATH.'files/images/'.$name.'b.jpg');
			$x_big = imagesx($im);
			$y_big = imagesy($im);
			$name_big_new = 'ob'.$r['id'].'-'.$name.($name[strlen($name) - 1] != '-' ? '-' : '').'b.jpg';
			rename($name_big, IMPATH.$name_big_new);

			echo $name_small_new.' = '.$x_small.'x'.$y_small.'<br />';
			$sql = "INSERT INTO `images` (
					  `path`,
					  `small_name`,
					  `small_x`,
					  `small_y`,
					  `big_name`,
					  `big_x`,
					  `big_y`,
					  `owner`,
					  `sort`,
					  `viewer_id_add`,
					  `dtime_add`
				  ) VALUES (
					  '".addslashes(IMLINK)."',
					  '".$name_small_new."',
					  ".$x_small.",
					  ".$y_small.",
					  '".$name_big_new."',
					  ".$x_big.",
					  ".$y_big.",
					  'ob".$r['id']."',
					  ".$sort.",
					  ".$r['viewer_id_add'].",
					  '".$r['dtime_add']."'
				  )";
			query($sql);
			if(!$sort) {
				$image_id = mysql_insert_id();
				$image_link = IMLINK.$name_small_new;
			}
			$sort++;
		}
		query("UPDATE `vk_ob`
			   SET `file`='',
				   `image_id`=".$image_id.",
				   `image_link`='".$image_link."'
			   WHERE `id`=".$r['id']);
	}
}


		//присвоение gazeta_id заявкам
		$sql = "SELECT * FROM `vk_ob` WHERE !`viewer_id_add` AND !`gazeta_id` limit 1000";
		$q = query($sql);
		while($r = mysql_fetch_assoc($q))
			query("UPDATE `vk_ob` SET gazeta_id=IFNULL((
				SELECT id FROM `gazeta_zayav` WHERE category=1 AND `dtime_add`='".$r['dtime_add']."' LIMIT 1
			),0) WHERE `id`=".$r['id']);
*/
