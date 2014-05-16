<?php
define('DOCUMENT_ROOT', dirname(__FILE__));
define('NAMES', 'cp1251');
define('DOMAIN', $_SERVER["SERVER_NAME"]);
define('LOCAL', DOMAIN == 'kupez');


require_once(DOCUMENT_ROOT.'/syncro.php');
require_once(VKPATH.'/vk.php');
_appAuth();
require_once(DOCUMENT_ROOT.'/view/main.php');

_dbConnect();
_getSetupGlobal();
_getVkUser();

function _getSetupGlobal() {//Получение глобальных данных
	$key = CACHE_PREFIX.'setup_global';
	$g = xcache_get($key);
	if(empty($g)) {
		$sql = "SELECT * FROM `setup_global` LIMIT 1";
		$g = mysql_fetch_assoc(query($sql));
		xcache_set($key, $g, 86400);
	}
	define('VERSION', $g['version']);
	define('G_VALUES_VERSION', $g['g_values']);
	define('KASSA_START', $g['kassa_start']);

	$sql = "SELECT
 				MIN(`general_nomer`) AS `first`,
				MAX(`general_nomer`) AS `max`
			FROM `gazeta_nomer` WHERE `day_print`>=DATE_FORMAT(NOW(),'%Y-%m-%d')";
	$gn = mysql_fetch_assoc(query($sql));
	define('GN_FIRST_ACTIVE', $gn['first']);
	define('GN_LAST_ACTIVE',  $gn['max']);
}//end of _getSetupGlobal()
function _getVkUser() {//Получение данных о пользователе
	$u = _viewer();
	define('VIEWER_NAME', $u['name']);
	define('VIEWER_ADMIN', $u['gazeta_admin']);
	define('GAZETA_ADMIN', $u['gazeta_admin']);
	define('GAZETA_WORKER', $u['gazeta_worker']);
}//end of _getVkUser()
