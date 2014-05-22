<?php
function toMailSend() {
	echo "\n\n----\ntime: ".round(microtime(true) - TIME, 3);
	if(!defined('UPDATE_OK'))
		mail(CRON_MAIL, 'CRON kupez/vk_users_update.php', ob_get_contents());
}

set_time_limit(180);
ob_start();
register_shutdown_function('toMailSend');

define('CRON', true);
require_once dirname(dirname(__FILE__)).'/config.php';

$count = 20;
$start = query_value("SELECT `cron_viewer_start` FROM `setup_global`");
$all = query_value("SELECT COUNT(*) FROM `vk_user`");
if($start >= $all)
	$start = 0;

$ids = query_ids("SELECT `viewer_id` FROM `vk_user` ORDER BY `dtime_add` ASC LIMIT ".$start.",".$count);
$_GET['access_token'] = query_value("SELECT `access_token` FROM `vk_user` ORDER BY `enter_last` DESC LIMIT 1");

$res = _vkapi('users.get', array(
	'user_ids' => $ids,
	'fields' => 'photo,'.
				'sex,'.
				'country,'.
				'city'
));

if(!empty($res['response'])) {
	foreach($res['response'] as $u) {
		usleep(500000);
		$app = _vkapi('users.isAppUser', array('user_id'=>$u['id']));
		if(!isset($app['response'])) {
			print_r($app);
			exit;
		}

		usleep(500000);
		$rule = _vkapi('account.getAppPermissions', array('user_id'=>$u['id']));
		if(!isset($rule['response'])) {
			print_r($rule);
			exit;
		}

		$q[] =
			"(".$u['id'].",
				'".addslashes(win1251($u['first_name']))."',
				'".addslashes(win1251($u['last_name']))."',
				".$u['sex'].",
				'".addslashes($u['photo'])."',
				".(empty($u['country']) ? 0 : $u['country']['id']).",
				'".(empty($u['country']) ? '' : addslashes(win1251($u['country']['title'])))."',
				".(empty($u['city']) ? 0 : $u['city']['id']).",
				'".(empty($u['city']) ? '' : addslashes(win1251($u['city']['title'])))."',
				".intval($app['response']).",
				".($rule['response']&256 ? 1 : 0).",
				".($rule['response']&1 ? 1 : 0)."
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
			VALUES ".implode(',', $q)."
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
	echo implode('<br />', $q);

	query("UPDATE `setup_global` SET `cron_viewer_start`=".($start + $count));
	define('UPDATE_OK', 1);
} else
	print_r($res);

mysql_close();
exit;
