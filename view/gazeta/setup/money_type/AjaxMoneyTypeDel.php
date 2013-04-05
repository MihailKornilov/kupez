<?php
require_once('../../../../include/AjaxHeader.php');

$name = $VK->QRow('SELECT `name` FROM `setup_money_type` WHERE `id`='.$_POST['id']);

$VK->Query("delete from setup_money_type where id=".$_POST['id']);

GvaluesCreate();

$VK->Query('INSERT INTO `gazeta_log`
                (`type`,`value`,`viewer_id_add`)
            VALUES
                (1113,"'.$name.'",'.VIEWER_ID.')');
echo 1;
