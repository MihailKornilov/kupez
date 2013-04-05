<?php
require_once('../../../../include/AjaxHeader.php');

$podrub = $VK->QueryObjectOne('SELECT * FROM `setup_pod_rubrika` WHERE `id`='.$_POST['id']);

$VK->Query("delete from setup_pod_rubrika where id=".$_POST['id']);
GvaluesCreate();

$rub = $VK->QRow('SELECT `name` FROM `setup_rubrika` WHERE `id`='.$podrub->rubrika_id);

$VK->Query('INSERT INTO `gazeta_log`
                (`type`,`value`,`viewer_id_add`)
            VALUES
                (1073,"<u>'.$podrub->name.'</u> в рубрике <u>'.$rub.'</u>",'.VIEWER_ID.')');

echo 1;
