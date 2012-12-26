<?php
require_once('../../include/AjaxHeader.php');

$send->client_id=$VK->QRow("select client_id from zayav where id=".$_GET['id']);
$VK->Query("delete from zayav where id=".$_GET['id']);
$VK->Query("delete from gazeta_nomer_pub where zayav_id=".$_GET['id']);
if($send->client_id>0) setClientBalans($send->client_id);
echo json_encode($send);
?>



