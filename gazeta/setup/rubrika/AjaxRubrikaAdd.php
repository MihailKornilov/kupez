<?php
require_once('../../../include/AjaxHeader.php');

$sort=$VK->QRow("select ifnull(max(sort)+1,0) from setup_rubrika");
$send->id=$VK->Query("insert into setup_rubrika (name,viewer_id_add,sort) values ('".iconv("UTF-8","WINDOWS-1251",$_POST['name'])."',".$_GET['viewer_id'].",".$sort.")");

echo json_encode($send);
?>



