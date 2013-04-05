<?php
require_once('../../../../include/AjaxHeader.php');

$VK->Query("delete from gazeta_nomer where general_nomer=".$_POST['general_nomer']);

$VK->Query('INSERT INTO `gazeta_log`
                (`type`,`value`,`viewer_id_add`)
            VALUES
                (1033,"'.$_POST['general_nomer'].'",'.VIEWER_ID.')');

echo 1;
