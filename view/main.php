<?php
function _hashRead() {
	$_GET['p'] = isset($_GET['p']) ? $_GET['p'] : 'gazeta';
	if(empty($_GET['hash'])) {
		define('HASH_VALUES', false);
		if(isset($_GET['start'])) {// �������������� ��������� ���������� ��������
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
	GvaluesCreate();
}//_cacheClear()

function _header() {
	global $html;
	$html =
		'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.
		'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">'.

		'<head>'.
		'<meta http-equiv="content-type" content="text/html; charset=windows-1251" />'.
		'<title>������ - ���������� '.API_ID.'</title>'.

		//������������ ������ � ��������
		(SA ? '<script type="text/javascript" src="http://nyandoma'.(LOCAL ? '' : '.ru').'/js/errors.js?'.VERSION.'"></script>' : '').

		//�������� �������
		'<script type="text/javascript" src="http://nyandoma'.(LOCAL ? '' : '.ru').'/js/jquery-2.0.3.min.js"></script>'.
		'<script type="text/javascript" src="http://nyandoma'.(LOCAL ? '' : '.ru').'/vk/xd_connection'.(DEBUG ? '' : '.min').'.js"></script>'.

		//��������� ���������� �������� �������.
		(SA ? '<script type="text/javascript">var TIME=(new Date()).getTime();</script>' : '').

		'<script type="text/javascript">'.
			(LOCAL ? 'for(var i in VK)if(typeof VK[i]=="function")VK[i]=function(){return false};' : '').
			'var DOMAIN="'.DOMAIN.'",'.
				'VALUES="'.VALUES.'",'.
				'VIEWER_ID='.VIEWER_ID.';'.
		'</script>'.

		//����������� api VK. ����� VK ������ ������ �� �������� ������ �����
		'<link href="http://nyandoma'.(LOCAL ? '' : '.ru').'/vk/vk'.(DEBUG ? '' : '.min').'.css?'.VERSION.'" rel="stylesheet" type="text/css" />'.
		'<script type="text/javascript" src="http://nyandoma'.(LOCAL ? '' : '.ru').'/vk/vk'.(DEBUG ? '' : '.min').'.js?'.VERSION.'"></script>'.

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
			'<a class="debug_toggle'.(DEBUG ? ' on' : '').'">�'.(DEBUG ? '�' : '').'������� Debug</a> :: '.
			'<a id="cache_clear">������� ��� ('.VERSION.')</a> :: '.
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

function GvaluesCreate() {// ����������� ����� G_values.js
	$sql = "SELECT * FROM `setup_global` LIMIT 1";
	$g = mysql_fetch_assoc(query($sql));

	$save = //'function _toSpisok(s){var a=[];for(k in s)a.push({uid:k,title:s[k]});return a}'.
		//'function _toAss(s){var a=[];for(var n=0;n<s.length;a[s[n].uid]=s[n].title,n++);return a}'.

		'var CATEGORY_SPISOK=[{uid:1,title:"����������"},{uid:2,title:"�������"},{uid:3,title:"������������"},{uid:4,title:"������"}],'.
		"\n".'PERSON_SPISOK='.query_selJson("SELECT `id`,`name` FROM `setup_person` ORDER BY `sort`").','.
		"\n".'RUBRIC_SPISOK='.query_selJson("SELECT `id`,`name` FROM `setup_rubric` ORDER BY `sort`").','.
		"\n".'MONEY_TYPE_SPISOK='.query_selJson("SELECT `id`,`name` FROM `setup_money_type` ORDER BY `sort`").','.
		"\n".'SKIDKA_SPISOK='.query_selJson("SELECT `razmer`,CONCAT(`razmer`,'%') FROM `setup_skidka` ORDER BY `razmer`").','.
		"\n".'TXT_LEN_FIRST='.$g['txt_len_first'].','.
		"\n".'TXT_CENA_FIRST='.$g['txt_cena_first'].','.
		"\n".'TXT_LEN_NEXT='.$g['txt_len_next'].','.
		"\n".'TXT_CENA_NEXT='.$g['txt_cena_next'].','.
		"\n".'POLOSA_SPISOK='.query_selJson('SELECT `id`,`name` FROM `setup_polosa_cost` ORDER BY `sort`').','.
		"\n".'POLOSA_CENA_ASS='.query_ptpJson('SELECT `id`,ROUND(`cena`) FROM `setup_polosa_cost` ORDER BY `id`').','.
		"\n".'OBDOP_SPISOK='.query_selJson('SELECT `id`,`name` FROM `setup_ob_dop` ORDER BY `id`').','.
		"\n".'OBDOP_CENA_ASS='.query_ptpJson('SELECT `id`,`cena` FROM `setup_ob_dop` ORDER BY `id`').','.
		"\n".'RASHOD_SPISOK='.query_selJson('SELECT `id`,`name` FROM `setup_rashod_category` ORDER BY `sort`').',';


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
	$save .= "\n".'RUBRIC_SUB_SPISOK={'.implode(',', $v).'},';


	$sql = "SELECT * FROM `gazeta_nomer` ORDER BY `general_nomer`";
	$q = query($sql);
	$gn = array();
	while($r = mysql_fetch_assoc($q))
		array_push($gn, "\n".$r['general_nomer'].':{'.
			'week:'.$r['week_nomer'].','.
			'pub:"'.$r['day_public'].'",'.
			'txt:"'.FullData($r['day_public'], 0, 0, 1).'"}');

	$save .= "\n".'GN={'.implode(',', $gn).'};';
	/*
	$save .= "\nG.countries_spisok = [{uid:1,title:'������'},{uid:2,title:'�������'},{uid:3,title:'��������'},{uid:4,title:'���������'},{uid:5,title:'�����������'},{uid:6,title:'�������'},{uid:7,title:'������'},{uid:8,title:'�������'},{uid:11,title:'����������'},{uid:12,title:'������'},{uid:13,title:'�����'},{uid:14,title:'�������'},{uid:15,title:'�������'},{uid:16,title:'�����������'},{uid:17,title:'���������'},{uid:18,title:'����������'}];";
	*/

	$fp = fopen(PATH.'/js/G_values.js', 'w+');
	fwrite($fp, $save);
	fclose($fp);

	query("UPDATE `setup_global` SET `g_values`=`g_values`+1");
	xcache_unset(CACHE_PREFIX.'setup_global');
} // end of GvaluesCreate()



/*
// �������� ������������ �� ������� � ����. ����� ���������� ��� ������ ����� � �������
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

        // ��������� �� ����������
        $app = $VKAPI->api('isAppUser',array('uid'=>VIEWER_ID));
        $vku['app_setup'] = $app['response'];
        // �������� �� � ����� ����
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

        // ����� �������� ����������
        if($vku['menu_left_set'] == 1) {
            $VKAPI->api('secure.setCounter', array('counter'=>0, 'uid'=>VIEWER_ID, 'timestamp'=>time(), 'random'=>rand(1,1000)));
        }
        // ������� �����������
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