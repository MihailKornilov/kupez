<?php
require_once('../../include/AjaxHeader.php');

// любое объ€вление размещаетс€ сроком не менее мес€ца

// если срок размещени€ в top меньше мес€ца, то общий срок устанавливаетс€ = 30 дней
$active_day = ($_POST['top_day'] > 30 ? $_POST['top_day'] : 30);

$idLast = $VK->Query("insert into zayav (
category,
rubrika,
podrubrika,
txt,
telefon,
file,
dop,

country_id,
country_name,
city_id,
city_name,

order_id,
order_votes,

viewer_id_add,
viewer_id_show,
whence,
active_day
) values (
1,
".$_POST['rubrika'].",
".$_POST['podrubrika'].",
'".win1251(textFormat($_POST['txt']))."',
'".win1251(textFormat($_POST['telefon']))."',
'".$_POST['file']."',
'".$_POST['dop']."',

".$_POST['country_id'].",
'".win1251($_POST['country_name'])."',
".$_POST['city_id'].",
'".win1251($_POST['city_name'])."',

".$_POST['order_id'].",
".$_POST['order_votes'].",

".$_GET['viewer_id'].",
".$_POST['viewer_id_show'].",
'vk',
date_add(current_timestamp,interval ".$active_day." day))");

// установка дн€, до которого объ€вление может находитьс€ в top
if ($_POST['top_day'] > 0) {
  $VK->Query("update zayav set top_day=date_add(current_timestamp,interval ".$_POST['top_day']." day) where id=".$idLast);
}

// установка соличества объ€влений пользователю
$ob_count = $VK->QRow("select count(id) from zayav where category=1 and whence='vk' and viewer_id_add=".$_GET['viewer_id']);
$VK->Query("update vk_user set ob_count='".$ob_count."' where viewer_id=".$_GET['viewer_id']);

rubrikaCountUpdate($_POST['rubrika']);

$send->time = getTime($T);

echo json_encode($send);
?>



