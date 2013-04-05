<?php
require_once('../../../../include/AjaxHeader.php');


$name = $VK->QRow('SELECT `name` FROM `setup_polosa_cost` WHERE `id`='.$_POST['id']);


$VK->Query("delete from setup_polosa_cost where id=".$_POST['id']);
GvaluesCreate();

$VK->Query('INSERT INTO `gazeta_log`
                (`type`,`value`,`viewer_id_add`)
            VALUES
                (1043,"'.$name.'",'.VIEWER_ID.')');

echo 1;




