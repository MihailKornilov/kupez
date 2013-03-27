<?php
require_once('../../../include/AjaxHeader.php');

$VK->Query("UPDATE `vk_ob` SET `status`=0 WHERE `id`=".$_GET['id']);

$send->id=1;

echo json_encode($send);
?>



