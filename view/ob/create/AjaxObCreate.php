<?php
require_once('../../../include/AjaxHeader.php');

// любое объ€вление размещаетс€ сроком не менее мес€ца

// если срок размещени€ в top меньше мес€ца, то общий срок устанавливаетс€ = 30 дней
$day_active = ($_POST['top_day'] > 30 ? $_POST['top_day'] : 30);

$send->id = $VK->Query("INSERT INTO `vk_ob` (
`rubrika`,
`podrubrika`,
`txt`,
`telefon`,
`file`,
`dop`,

`country_id`,
`country_name`,
`city_id`,
`city_name`,

`order_id`,
`order_votes`,

`viewer_id_add`,
`viewer_id_show`,
`day_active`
) VALUES (
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

DATE_ADD(CURRENT_TIMESTAMP,INTERVAL ".$day_active." DAY))");

// установка дн€, до которого объ€вление может находитьс€ в top
if ($_POST['top_day'] > 0) {
  $VK->Query("UPDATE `vk_ob` SET `top_day`=DATE_ADD(CURRENT_TIMESTAMP,INTERVAL ".$_POST['top_day']." DAY) WWHERE `id`=".$send->id);
}

// установка соличества объ€влений пользователю
$VK->Query("INSERT INTO `vk_user`
            (`viewer_id`,`ob_count`)
              SELECT
                ".$_GET['viewer_id']." AS `viewer_id`,
                COUNT(`id`) AS `ob_count`
              FROM `vk_ob`
              WHERE `viewer_id_add`=".$_GET['viewer_id']."
            ON DUPLICATE KEY UPDATE
              `ob_count`=VALUES(`ob_count`)");

echo json_encode($send);
?>



