<?php
require_once('../../../include/AjaxHeader.php');

$VK->Query("update setup_polosa_cost set name='".iconv("UTF-8","WINDOWS-1251",$_POST['name'])."',cena='".$_POST['cena']."' where id=".$_POST['id']);

echo 1;
?>



