<?php
require_once('../../../../include/AjaxHeader.php');

$VK->Query("insert into setup_skidka (razmer,about,viewer_id_add) values (".$_POST['razmer'].",'".win1251($_POST['about'])."',".VIEWER_ID.")");

GvaluesCreate();

$VK->Query('INSERT INTO `gazeta_log`
                (`type`,`value`,`viewer_id_add`)
            VALUES
                (1051,"'.$_POST['razmer'].'",'.VIEWER_ID.')');


echo 1;
