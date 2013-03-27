<?php
require_once('../../../include/AjaxHeader.php');

if ($_POST['active'] == 1) {
	$srokSet = "date_add(current_timestamp,interval 30 day),";
} else {
  $srokSet = "'0000-00-00',";
}

$VK->Query("UPDATE `vk_ob` SET
rubrika=".$_POST['rubrika'].",
podrubrika=".$_POST['podrubrika'].",
txt='".textFormat(win1251($_POST['txt']))."',
telefon='".textFormat(win1251($_POST['telefon']))."',
file='".$_POST['file']."',

country_id=".$_POST['country_id'].",
country_name='".win1251($_POST['country_name'])."',
city_id=".$_POST['city_id'].",
city_name='".win1251($_POST['city_name'])."',

day_active=".$srokSet."
viewer_id_show=".$_POST['viewer_id_show']."
WHERE id=".$_POST['id']);

$dtime = $VK->QRow("SELECT `day_active` FROM `vk_ob` WHERE `id`=".$_POST['id']);
$srok = strtotime($dtime) - time() + 86400;
$day = floor($srok / 86400);
$send->dtime = utf8("Остал".ends($day, 'ся', 'ось').$day.ends($day, 'день', 'дня', 'дней'));

echo json_encode($send);
?>



