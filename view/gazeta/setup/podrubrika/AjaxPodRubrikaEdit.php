<?php
require_once('../../../../include/AjaxHeader.php');

$VK->Query("update setup_pod_rubrika set name='".win1251($_POST['name'])."' where id=".$_POST['id']);
GvaluesCreate();

$rub_id = $VK->QRow('SELECT `rubrika_id` FROM `setup_pod_rubrika` WHERE `id`='.$_POST['id']);
$rub = $VK->QRow('SELECT `name` FROM `setup_rubrika` WHERE `id`='.$rub_id);

$VK->Query('INSERT INTO `gazeta_log`
                (`type`,`value`,`viewer_id_add`)
            VALUES
                (1072,"<u>'.win1251($_POST['name']).'</u> в рубрике <u>'.$rub.'</u>",'.VIEWER_ID.')');


echo 1;
