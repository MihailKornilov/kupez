<?php
require_once('../../../../include/AjaxHeader.php');


$razmer = $VK->QRow('SELECT `razmer` FROM `setup_skidka` WHERE `id`='.$_POST['id']);
$VK->Query("delete from setup_skidka where id=".$_POST['id']);

GvaluesCreate();

$VK->Query('INSERT INTO `gazeta_log`
                (`type`,`value`,`viewer_id_add`)
            VALUES
                (1053,"'.$razmer.'",'.VIEWER_ID.')');

echo 1;
