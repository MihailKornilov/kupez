<?php
require_once('../../../include/AjaxHeader.php');

$VK->Query("update setup_ob_dop set name='".iconv("UTF-8","WINDOWS-1251",$_POST['name'])."',cena=".$_POST['cena']." where id=".$_POST['id']);

echo 1;
?>



