<?php
require_once('../../../include/AjaxHeader.php');

$VK->Query("UPDATE `vk_ob` SET day_active='0000-00-00' WHERE `id`=".$_GET['id']);

$send->id=1;

echo json_encode($send);
?>



