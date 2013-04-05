<?php
require_once('../../../../include/AjaxHeader.php');

$name = $VK->QRow('SELECT `name` FROM `setup_person` WHERE `id`='.$_POST['del']);

if($_POST['ost']>0) $VK->Query("update client set person=".$_POST['ost']." where person=".$_POST['del']);
$VK->Query("delete from setup_person where id=".$_POST['del']);

GvaluesCreate();

$VK->Query('INSERT INTO `gazeta_log`
                (`type`,`value`,`viewer_id_add`)
            VALUES
                (1013,"'.$name.'",'.VIEWER_ID.')');

echo 1;
?>