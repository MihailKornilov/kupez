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
	xcache_unset(CACHE_PREFIX.'person');
	xcache_unset(CACHE_PREFIX.'rubric');
	xcache_unset(CACHE_PREFIX.'rubric_sub');
	xcache_unset(CACHE_PREFIX.'gn');
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
		(SA ? '<script type="text/javascript" src="http://nyandoma'.(LOCAL ? '' : '.ru').'/js/errors.js?'.VERSION.'"></script>' : '').

		//Стороние скрипты
		'<script type="text/javascript" src="http://nyandoma'.(LOCAL ? '' : '.ru').'/js/jquery-2.0.3.min.js"></script>'.
		'<script type="text/javascript" src="http://nyandoma'.(LOCAL ? '' : '.ru').'/vk/xd_connection'.(DEBUG ? '' : '.min').'.js"></script>'.

		//Установка начального значения таймера.
		(SA ? '<script type="text/javascript">var TIME=(new Date()).getTime();</script>' : '').

		'<script type="text/javascript">'.
			(LOCAL ? 'for(var i in VK)if(typeof VK[i]=="function")VK[i]=function(){return false};' : '').
			'var DOMAIN="'.DOMAIN.'",'.
				'VALUES="'.VALUES.'",'.
				'VIEWER_ID='.VIEWER_ID.','.
				'GN_FIRST_ACTIVE='.GN_FIRST_ACTIVE.','.
				'GN_LAST_ACTIVE='.GN_LAST_ACTIVE.';'.
		'</script>'.

		//Подключение api VK. Стили VK должны стоять до основных стилей сайта
		'<link href="http://nyandoma'.(LOCAL ? '' : '.ru').'/vk/vk'.(DEBUG ? '' : '.min').'.css?'.VERSION.'" rel="stylesheet" type="text/css" />'.
		'<script type="text/javascript" src="http://nyandoma'.(LOCAL ? '' : '.ru').'/vk/vk'.(DEBUG ? '' : '.min').'.js?'.VERSION.'"></script>'.

		'<script type="text/javascript" src="'.SITE.'/js/G_values.js?'.G_VALUES_VERSION.'"></script>'.

		'<link href="'.SITE.'/css/main.css?'.VERSION.'" rel="stylesheet" type="text/css" />'.
		'<script type="text/javascript" src="'.SITE.'/js/main.js?'.VERSION.'"></script>'.

		($_GET['p'] == 'gazeta' ? '<link href="'.SITE.'/css/gazeta.css?'.VERSION.'" rel="stylesheet" type="text/css" />' : '').
		($_GET['p'] == 'gazeta' ? '<script type="text/javascript" src="'.SITE.'/js/gazeta.js?'.VERSION.'"></script>' : '').

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
		$html .=
			'<div id="admin">'.
				//  ($_GET['p'] != 'sa' && !SA_VIEWER_ID ? '<a href="'.URL.'&p=sa&pre_p='.$_GET['p'].$d.$d1.$id.'">Admin</a> :: ' : '').
				'<a class="debug_toggle'.(DEBUG ? ' on' : '').'">В'.(DEBUG ? 'ы' : '').'ключить Debug</a> :: '.
				'<a id="cache_clear">Очисить кэш ('.VERSION.')</a> :: '.
				'sql <b>'.$sqlCount.'</b> ('.round($sqlTime, 3).') :: '.
				'php '.round(microtime(true) - TIME, 3).' :: '.
				'js <em></em>'.
			'</div>';
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
		(SA && DEBUG ? $sqlQuery : '').
		'</div>'.
		'</body></html>';
}//_footer()

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
		"\n".'POLOSA_SPISOK='.query_selJson('SELECT `id`,`name` FROM `setup_polosa_cost` ORDER BY `sort`').','.
		"\n".'POLOSA_CENA_ASS='.query_ptpJson('SELECT `id`,ROUND(`cena`) FROM `setup_polosa_cost` ORDER BY `id`').','.
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
			'txt:"'.FullData($r['day_public'], 0, 0, 1).'"}');

	$save .= "\n".'GN={'.implode(',', $gn).'};';

	$fp = fopen(PATH.'/js/G_values.js', 'w+');
	fwrite($fp, $save);
	fclose($fp);

	query("UPDATE `setup_global` SET `g_values`=`g_values`+1");
	xcache_unset(CACHE_PREFIX.'setup_global');
} // end of GvaluesCreate()



function ob() {//Главная страница с объявлениями
	$data = ob_spisok();
	$country =
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
	return
	'<script type="text/javascript">'.
		'var COUNTRIES='.query_selJson($country).','.
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
					'<div class="findHead region">Регион</div>'.
					'<input type="hidden" id="countries" />'.
					'<div class="city-sel dn"><input type="hidden" id="cities"></div>'.
					'<div class="findHead">Рубрики</div>'.
					_rightLink('rub', $rubric, 0).
					'<input type="hidden" id="rubsub" value="0" />'.
					'<div class="findHead">Дополнительно</div>'.
					_check('withfoto', 'Только с фото').
		'</table>'.
	'</div>';
}//ob()
function obFilter($v=array()) {
	return array(
		'page' => !empty($v['page']) && preg_match(REGEXP_NUMERIC, $v['page']) ? intval($v['page']) : 1,
		'limit' => !empty($v['limit']) && preg_match(REGEXP_NUMERIC, $v['limit']) ? intval($v['limit']) : 20,
		'find' => !empty($v['find']) ? win1251(htmlspecialchars(trim($v['find']))) : '',
		'country_id' => !empty($v['country_id']) && preg_match(REGEXP_NUMERIC, $v['country_id']) ? intval($v['country_id']) : 0,
		'city_id' => !empty($v['city_id']) && preg_match(REGEXP_NUMERIC, $v['city_id']) ? intval($v['city_id']) : 0,
		'rubric_id' => !empty($v['rubric_id']) && preg_match(REGEXP_NUMERIC, $v['rubric_id']) ? intval($v['rubric_id']) : 0,
		'rubric_sub_id' => !empty($v['rubric_sub_id']) && preg_match(REGEXP_NUMERIC, $v['rubric_sub_id']) ? intval($v['rubric_sub_id']) : 0,
		'withfoto' => isset($v['withfoto']) && preg_match(REGEXP_BOOL, $v['withfoto']) ? intval($v['withfoto']) : 0
	);
}//obSpisokFilter()
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
		$cond .= " AND length(file)>0";

	$all = query_value("SELECT COUNT(`id`) AS `all` FROM `vk_ob` WHERE ".$cond);

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
	$send['result'] = 'Показан'._end($all, '', 'о').' '.$all.' объявлен'._end($all, 'ие', 'ия', 'ий').$links;
	$send['filter'] = $filter;

	$start = ($page - 1) * $limit;
	$sql = "SELECT *
			FROM `vk_ob`
			WHERE ".$cond."
			ORDER BY `id` DESC
			LIMIT ".$start.",".$limit;
	$q = query($sql);
	$ob = array();
	$owner = array();
	while($r = mysql_fetch_assoc($q)) {
		if($filter['find']) {
			if(preg_match($reg, $r['txt']))
				$r['txt'] = preg_replace($reg, '<em>\\1</em>', $r['txt'], 1);
		}
		$ob[$r['id']] = $r;
		$owner[] = "'ob".$r['id']."'";
	}

	$sql = "SELECT * FROM `images` WHERE !`deleted` AND `sort`=0 AND `owner` IN (".implode(',', $owner).")";
	$q = query($sql);
	while($r = mysql_fetch_assoc($q)) {
		$ex = explode('ob', $r['owner']);
		$ob[$ex[1]]['img'] = array(
			'id' => $r['id'],
			'link' => $r['path'].$r['small_name']
		);
	}

	$send['spisok'] = '';
	foreach($ob as $r) {
		$send['spisok'] .=
		'<div class="ob-unit">'.
			'<table class="utab">'.
				'<tr><td class="txt">'.
						'<a class="rub" val="'.$r['rubric_id'].'">'._rubric($r['rubric_id']).'</a><u>»</u>'.
						($r['rubric_sub_id'] ? '<a class="rubsub" val="'.$r['rubric_id'].'_'.$r['rubric_sub_id'].'">'._rubricsub($r['rubric_sub_id']).'</a><u>»</u>' : '').
						$r['txt'].
						($r['telefon'] ? '<div class="tel">'.$r['telefon'].'</div>' : '').
						(isset($r['img']) ? '<td class="foto"><img src="'.$r['img']['link'].'" class="_iview" val="'.$r['img']['id'].'" />' : '').
				'<tr><td class="adres" colspan="2">'.
					($r['city_name'] ? $r['country_name'].', '.$r['city_name']  : '').
					($r['viewer_id_show'] ? _viewer($r['viewer_id_add'], 'link')  : '').
			'</table>'.
		'</div>';
	}
	if($start + $limit < $all) {
		$c = $all - $start - $limit;
		$c = $c > $limit ? $limit : $c;
		$send['spisok'] .=
			'<div class="_next ob_next" val="'.($page + 1).'">'.
				'<span>Показать ещё '.$c.' объявлен'._end($all, 'ие', 'ия', 'ий').'</span>'.
			'</div>';
	}

	return $send;
}//ob_spisok()

function ob_create() {
	query("UPDATE `images` SET `deleted`=1 WHERE `owner`='".VIEWER_ID."'");
	$dop = array(
		0 => 'Не выделять',
		1 => 'Обвести в рамку',
		2 => 'Выделить жирным шрифтом',
		3 => 'На чёрном фоне'
	);
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
					'<div class="vkCancel"><button>Отмена</button></div>'.
		'</table>'.

		'<div class="headName">Предосмотр объявления</div>'.
        '<div id="preview"></div>'.
	'</div>';
}//ob_create()

function ob_my() {
	return 'my';
}//ob_my()


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
*/