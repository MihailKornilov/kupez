<?php
define('DOCUMENT_ROOT', dirname(__FILE__));
define('NAMES', 'cp1251');

$SA[166424274] = 1;
require_once(DOCUMENT_ROOT.'/syncro.php');
require_once(API_PATH.'/vk.php');
_appAuth();
require_once(DOCUMENT_ROOT.'/view/main.php');

_dbConnect();
_getSetupGlobal();
_getVkUser();

function _getSetupGlobal() {//Получение глобальных данных
	if(CRON)
		return;
	$key = CACHE_PREFIX.'setup_global';
	$g = xcache_get($key);
	if(empty($g)) {
		$sql = "SELECT * FROM `setup_global` LIMIT 1";
		$g = query_assoc($sql);
		xcache_set($key, $g, 86400);
	}
	define('VERSION', $g['version']);
	define('G_VALUES_VERSION', $g['g_values']);

	$key = CACHE_PREFIX.'gn_first_max';
	$gn = xcache_get($key);
	if(empty($gn)) {
		$sql = "SELECT
	                MIN(`general_nomer`) AS `first`,
					MAX(`general_nomer`) AS `max`
				FROM `gazeta_nomer` WHERE `day_print`>=DATE_FORMAT(NOW(),'%Y-%m-%d')";
		$gn = query_assoc($sql);
		xcache_set($key, $gn, 86400);
	}
	define('GN_FIRST_ACTIVE', $gn['first']);
	define('GN_LAST_ACTIVE',  $gn['max']);
}//end of _getSetupGlobal()
function _getVkUser() {//Получение данных о пользователе
	if(CRON)
		return;
	$u = _viewer();
	if(!empty($u['new']))
		_historyInsert(1, array('viewer_id'=>VIEWER_ID), 'vk_history');

	define('VIEWER_NAME', $u['name']);
	define('VIEWER_ADMIN', $u['gazeta_admin']);
	define('GAZETA_ADMIN', $u['gazeta_admin']);
	define('GAZETA_WORKER', $u['gazeta_worker']);

	if(APP_START) { //учёт посещений
		$day = strftime('%Y-%m-%d');
		$sql = "SELECT `id` FROM `vk_visit` WHERE `viewer_id`=".VIEWER_ID." AND `day`='".$day."' LIMIT 1";
		$id = query_value($sql);
		$sql = "INSERT INTO `vk_visit` (
				`id`,
				`viewer_id`,
				`day`,
				`is_secure`
			 ) VALUES (
				".($id === false ? 0 : $id).",
				".VIEWER_ID.",
				'".$day."',
				"._isbool($_GET['is_secure'])."
			 ) ON DUPLICATE KEY UPDATE
				`count_day`=`count_day`+1,
				`is_secure`="._isbool($_GET['is_secure']);
		query($sql);
		query("UPDATE `vk_user` SET `count_day`=".($id === false ? 1 : "`count_day`+1")." WHERE `viewer_id`=".VIEWER_ID);
	}
}//end of _getVkUser()
