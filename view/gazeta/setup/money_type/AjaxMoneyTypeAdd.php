<?php
require_once('../../../../include/AjaxHeader.php');

$sort=$VK->QRow("select ifnull(max(sort)+1,0) from setup_money_type");
$send->id=$VK->Query("insert into setup_money_type (name,viewer_id_add,sort) values ('".win1251($_POST['name'])."',".VIEWER_ID.",".$sort.")");

GvaluesCreate();

$VK->Query('INSERT INTO `gazeta_log`
                (`type`,`value`,`viewer_id_add`)
            VALUES
                (1111,"'.win1251($_POST['name']).'",'.VIEWER_ID.')');


echo json_encode($send);
