<?php
require_once('../../../include/AjaxHeader.php');

$VK->Query("update setup_skidka set razmer=".$_POST['razmer'].",about='".iconv("UTF-8","WINDOWS-1251",$_POST['about'])."' where id=".$_POST['id']);

echo 1;
?>



