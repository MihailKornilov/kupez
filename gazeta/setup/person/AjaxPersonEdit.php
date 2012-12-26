<?php
require_once('../../../include/AjaxHeader.php');

$VK->Query("update setup_person set name='".iconv("UTF-8","WINDOWS-1251",$_POST['name'])."' where id=".$_POST['id']);

echo 1;
?>



