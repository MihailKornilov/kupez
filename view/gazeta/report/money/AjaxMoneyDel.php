<?php
require_once('../../../../include/AjaxHeader.php');

$money = $VK->QueryObjectOne('SELECT * FROM `gazeta_money` WHERE `id`='.$_GET['id']);
$VK->Query("DELETE FROM `gazeta_money` WHERE `id`='".$_GET['id']."'");

$VK->Query('INSERT INTO `gazeta_log`
                (`type`,`value`,`viewer_id_add`)
            VALUES
                (47,"'.round($money->sum, 2).'",'.VIEWER_ID.')');

$send->info = 1;
echo json_encode($send);
?>



