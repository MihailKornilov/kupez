<?php
require_once('../../../../include/AjaxHeader.php');

$name = $VK->QRow('SELECT `name` FROM `setup_rubrika` WHERE `id`='.$_POST['id']);

$VK->Query("delete from setup_rubrika where id=".$_POST['id']);
GvaluesCreate();

$VK->Query('INSERT INTO `gazeta_log`
                (`type`,`value`,`viewer_id_add`)
            VALUES
                (1023,"'.$name.'",'.VIEWER_ID.')');

echo 1;
?>



