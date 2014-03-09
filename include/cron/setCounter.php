<?php
$PATH="/home/httpd/vhosts/nyandoma.ru/subdomains/kupez/httpdocs/include/";
require_once($PATH.'class_MysqlDB.php');
require_once($PATH.'vkapi.class.php');

$mysql = array(
	'host'=>'a6460.mysql.mchost.ru',
	'user'=>'a6460_kupez',
	'pass'=>'4909099',
	'database'=>'a6460_kupez',
	'names'=>'cp1251'
);

$VK = new MysqlDB($mysql['host'],$mysql['user'],$mysql['pass'],$mysql['database'],$mysql['names']);

$spisok=$VK->QueryObjectArray("select * from vk_user where menu_left_set=1 order by enter_last desc");
if(!count($spisok))
	die('No users.');

$VKAPI = new vkapi(2881875,'h9IjOkxIMwoW8agQkW3M');
$send = count($spisok)." users\n\n";
foreach($spisok as $sp) {
	$sql = "SELECT COUNT(`id`)
				FROM `vk_ob`
				WHERE `status`=1
				  AND `day_active`>='".strftime("%Y-%m-%d")."'
				  AND `dtime_add`>'".$sp->enter_last."'";
	$zayav = $VK->QRow($sql);
	if($zayav) {
		if($zayav > 999)
			$zayav = 999;
		$VKAPI->api('secure.setCounter', array(
			'counter' => $zayav,
			'uid' => $sp->viewer_id,
			'timestamp' => time(),
			'random' => rand(1, 1000)
		));
	}
	$send .= $sp->viewer_id." = ".$zayav.".		enter_last=".$sp->enter_last."\n";
}

echo $send;
