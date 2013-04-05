<?php
require_once('../../../../include/AjaxHeader.php');

$VK->Query("update setup_ob_dop set cena=".$_POST['cena']." where id=".$_POST['id']);

GvaluesCreate();


$name = $VK->QRow('SELECT `name` FROM `setup_ob_dop` WHERE `id`='.$_POST['id']);
$VK->Query('INSERT INTO `gazeta_log`
                (`type`,`value`,`viewer_id_add`)
            VALUES
                (1062,"'.$name.'",'.VIEWER_ID.')');

echo 1;
