<?php
require_once('../../include/AjaxHeader.php');

$send->id=$VK->Query("insert into accrual (zayav_id,summa,prim,viewer_id_add) values (".$_POST['zayav_id'].",'".$_POST['summa']."','".iconv("UTF-8","WINDOWS-1251",$_POST['prim'])."',".$_GET['viewer_id'].")");

echo 1;
?>



