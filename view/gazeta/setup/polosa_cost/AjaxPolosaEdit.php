<?php
require_once('../../../../include/AjaxHeader.php');

$VK->Query("update setup_polosa_cost set name='".win1251($_POST['name'])."',cena='".$_POST['cena']."' where id=".$_POST['id']);
GvaluesCreate();

$VK->Query('INSERT INTO `gazeta_log`
                (`type`,`value`,`viewer_id_add`)
            VALUES
                (1042,"'.win1251($_POST['name']).'",'.VIEWER_ID.')');

echo 1;
?>



