<?php
require_once('../../../../include/AjaxHeader.php');

$VK->Query("update setup_rashod_category set name='".win1251($_POST['name'])."' where id=".$_POST['id']);

GvaluesCreate();

$VK->Query('INSERT INTO `gazeta_log`
                (`type`,`value`,`viewer_id_add`)
            VALUES
                (1102,"'.win1251($_POST['name']).'",'.VIEWER_ID.')');
echo 1;
