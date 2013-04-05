<?php
require_once('../../../../include/AjaxHeader.php');

$VK->Query("update setup_skidka set razmer=".$_POST['razmer'].",about='".win1251($_POST['about'])."' where id=".$_POST['id']);

GvaluesCreate();

$VK->Query('INSERT INTO `gazeta_log`
                (`type`,`value`,`viewer_id_add`)
            VALUES
                (1052,"'.$_POST['razmer'].'",'.VIEWER_ID.')');

echo 1;
