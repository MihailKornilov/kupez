<?php
function toMailSend() {
	echo "\n\n----\ntime: ".round(microtime(true) - TIME, 3);
	if(!defined('UPDATE_OK')) {
		query("UPDATE `setup_global` SET `access_token`=''");
		mail(CRON_MAIL, 'CRON kupez/vk_users_update.php', ob_get_contents());
	}
}

set_time_limit(1000);
error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('display_startup_errors', true);
ob_start();
register_shutdown_function('toMailSend');

define('CRON', true);
require_once dirname(dirname(__FILE__)).'/config.php';

$count = 100;
$g = query_assoc("SELECT * FROM `setup_global`");
$start = $g['cron_viewer_start'];

if(empty($g['access_token']))
	for($n = 0; $n < 10; $n++) {
		$sql = "SELECT * FROM `vk_user` WHERE LENGTH(`access_token`) ORDER BY `enter_last` DESC LIMIT 1";
		$r = query_assoc($sql);
		echo $n.'. Get token in '.$r['viewer_id'].': '.$r['access_token']."\n<br />";
		$_GET['access_token'] = $r['access_token'];
		$app = _vkapi('users.isAppUser', array('user_id'=>982006));
		if(isset($app['response'])) {
			query("UPDATE `setup_global` SET `access_token`='".$r['access_token']."'");
			$g['access_token'] = $r['access_token'];
			break;
		}
		query("UPDATE `vk_user` SET `access_token`='' WHERE `viewer_id`=".$r['viewer_id']);
		sleep(1);
	}
$_GET['access_token'] = $g['access_token'];



$all = query_value("SELECT COUNT(*) FROM `vk_user`");
if($start >= $all)
	$start = 0;

$viewer = array();
$sql = "SELECT * FROM `vk_user` ORDER BY `dtime_add` ASC LIMIT ".$start.",".$count;
//$sql = "SELECT * FROM `vk_user` WHERE `viewer_id`=166424274";
$q = query($sql);
while($r = mysql_fetch_assoc($q))
	$viewer[$r['viewer_id']] = $r;

$res = _vkapi('users.get', array(
	'user_ids' => implode(',', array_keys($viewer)),
	'access_token' => '',
	'fields' => 'photo,'.
				'sex,'.
				'country,'.
				'city'
));

if(!empty($res['response'])) {
	$uarr = array();
	foreach($res['response'] as $u) {
		sleep(1);
		$app = _vkapi('users.isAppUser', array('user_id'=>$u['id']));
		if(!isset($app['response'])) {
			print_r($app);
			exit;
		}

		sleep(1);
		$rule = _vkapi('account.getAppPermissions', array('user_id'=>$u['id']));
		if(!isset($rule['response'])) {
			print_r($rule);
			exit;
		}

		$u += array(
			'is_app_user' => intval($app['response']),
			'rule_menu_left' => $rule['response']&256 ? 1 : 0,
			'rule_notify' => $rule['response']&1 ? 1 : 0
		);
		viewerSettingsHistory($viewer[$u['id']], $u);

		$uarr[] =
			"(".$u['id'].",
				'".addslashes(win1251($u['first_name']))."',
				'".addslashes(win1251($u['last_name']))."',
				".$u['sex'].",
				'".addslashes($u['photo'])."',
				".(empty($u['country']) ? 0 : $u['country']['id']).",
				'".(empty($u['country']) ? '' : addslashes(win1251($u['country']['title'])))."',
				".(empty($u['city']) ? 0 : $u['city']['id']).",
				'".(empty($u['city']) ? '' : addslashes(win1251($u['city']['title'])))."',
				".$u['is_app_user'].",
				".$u['rule_menu_left'].",
				".$u['rule_notify']."
			)";
	}

	$sql = "INSERT INTO `vk_user` (
				`viewer_id`,
				`first_name`,
				`last_name`,
				`sex`,
				`photo`,
				`country_id`,
				`country_name`,
				`city_id`,
				`city_name`,
				`is_app_user`,
				`rule_menu_left`,
				`rule_notify`
			)
			VALUES ".implode(',', $uarr)."
			ON DUPLICATE KEY UPDATE
				`first_name`=VALUES(`first_name`),
				`last_name`=VALUES(`last_name`),
				`sex`=VALUES(`sex`),
				`photo`=VALUES(`photo`),
				`country_id`=VALUES(`country_id`),
				`country_name`=VALUES(`country_name`),
				`city_id`=VALUES(`city_id`),
				`city_name`=VALUES(`city_name`),
				`is_app_user`=VALUES(`is_app_user`),
				`rule_menu_left`=VALUES(`rule_menu_left`),
				`rule_notify`=VALUES(`rule_notify`)";
	query($sql);

	echo implode('<br />', $uarr);
	query("UPDATE `setup_global` SET `cron_viewer_start`=".($start + $count));
	define('UPDATE_OK', 1);
} else
	print_r($res);

mysql_close();
exit;
