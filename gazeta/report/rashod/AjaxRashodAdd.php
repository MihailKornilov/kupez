<?php
require_once('../../../include/AjaxHeader.php');

$send->id=$VK->Query("insert into rashod (name,summa,viewer_id_add) values ('".iconv("UTF-8","WINDOWS-1251",$_POST['name'])."','".$_POST['summa']."',".$_GET['viewer_id'].")");

echo json_encode($send);
?>



