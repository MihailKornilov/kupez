<?php
require_once('../../../../include/AjaxHeader.php');

$VK->Query("DELETE FROM `gazeta_zayav` WHERE `id`='".$_GET['id']."'");
$VK->Query("DELETE FROM `gazeta_nomer_pub` WHERE `zayav_id`='".$_GET['id']."'");

$VK->Query('INSERT INTO `gazeta_log`
                (`type`,`value`,`viewer_id_add`)
            VALUES
                (6'.$_GET['category'].',"'.$_GET['id'].'",'.VIEWER_ID.')');

if ($_GET['client_id']) setClientBalans($_GET['client_id']);

$send->info = 1;

echo json_encode($send);
?>



