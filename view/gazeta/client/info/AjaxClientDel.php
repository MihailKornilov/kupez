<?php
require_once('../../../../include/AjaxHeader.php');

$VK->Query("DELETE FROM `gazeta_client` WHERE `id`='".$_GET['id']."'");

$send->info = 1;
echo json_encode($send);
?>



