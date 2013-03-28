<?php
require_once('../../../../include/AjaxHeader.php');

$VK->Query("UPDATE `vk_user` SET `gazeta_worker`=0 WHERE `viewer_id`=".$_POST['viewer_id']);

$VK->Query('INSERT INTO `gazeta_log`
                (`type`,`value`,`viewer_id_add`)
            VALUES
                (1082,
                 (SELECT CONCAT(`first_name`," ",`last_name`) FROM `vk_user` WHERE `viewer_id`='.$_POST['viewer_id'].'),
                 '.VIEWER_ID.')');


$send=1;
echo json_encode($send);
?>



