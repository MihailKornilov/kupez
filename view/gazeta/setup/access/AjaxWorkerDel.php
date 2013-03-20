<?php
require_once('../../../../include/AjaxHeader.php');

$VK->Query("UPDATE `vk_user` SET `gazeta_worker`=0 WHERE `viewer_id`=".$_POST['viewer_id']);

$send=1;
echo json_encode($send);
?>



