<?php
require_once('../../../include/AjaxHeader.php');

$VK->Query("insert into setup_ob_dop (name,cena,viewer_id_add) values ('".iconv("UTF-8","WINDOWS-1251",$_POST['name'])."',".$_POST['cena'].",".$_GET['viewer_id'].")");

echo 1;
?>



