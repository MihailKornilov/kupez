<?php
require_once('../../../include/AjaxHeader.php');

$sort=$VK->QRow("select ifnull(max(sort)+1,0) from setup_pod_rubrika where rubrika_id=".$_POST['rubrika_id']);
$send->id=$VK->Query("insert into setup_pod_rubrika (rubrika_id,name,viewer_id_add,sort) values (".$_POST['rubrika_id'].",'".iconv("UTF-8","WINDOWS-1251",$_POST['name'])."',".$_GET['viewer_id'].",".$sort.")");

echo json_encode($send);
?>



