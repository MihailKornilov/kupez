<?php
require_once('../../../include/AjaxHeader.php');

$sort=$VK->QRow("select ifnull(max(sort)+1,0) from setup_polosa_cost");
$VK->Query("insert into setup_polosa_cost (name,cena,sort,viewer_id_add) values ('".iconv("UTF-8","WINDOWS-1251",$_POST['name'])."','".$_POST['cena']."',".$sort.",".$_GET['viewer_id'].")");

echo 1;
?>



