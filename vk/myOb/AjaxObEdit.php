<?php
function ostEnd($count) {
  $ost=$count%10;
  $ost10=$count/10%10;
  if($ost10==1) return 'ось ';
  else
    if($ost==1) return 'ся ';
    else return 'ось ';
}

function dayEnd($count) {
  $ost=$count%10;
  $ost10=$count/10%10;

  if($ost10==1) return ' дней';
  else
    switch($ost) {
      case '1': return ' день';
      case '2': return ' дня';
      case '3': return ' дня';
      case '4': return ' дня';
      default: return ' дней';
      }
  }

require_once('../../include/AjaxHeader.php');

if ($_POST['active'] == 1) {
	$srokSet = "date_add(current_timestamp,interval 30 day),";
} else {
  $srokSet = "'0000-00-00',";
}

$VK->Query("update zayav set
rubrika=".$_POST['rubrika'].",
podrubrika=".$_POST['podrubrika'].",
txt='".textFormat(win1251($_POST['txt']))."',
telefon='".textFormat(win1251($_POST['telefon']))."',
file='".$_POST['file']."',

country_id=".$_POST['country_id'].",
country_name='".win1251($_POST['country_name'])."',
city_id=".$_POST['city_id'].",
city_name='".win1251($_POST['city_name'])."',

active_day=".$srokSet."
viewer_id_show=".$_POST['viewer_id_show']."
where id=".$_POST['id']);

rubrikaCountUpdate($_POST['rubrika']);

$dtime = $VK->QRow("select active_day from zayav where id=".$_POST['id']);
$srok = strtotime($dtime) - time() + 86400;
$day = floor($srok / 86400);
$send->dtime = utf8("Остал".ostEnd($day).$day.dayEnd($day));

// установка показа имени из VK, если необходимо.
//$send->viewer_name = utf8($VK->QRow("select concat(first_name,' ',last_name) from vk_user where viewer_id=".$_POST['viewer_id']));

$send->time = getTime($T);

echo json_encode($send);
?>



