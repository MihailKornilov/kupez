<?php
require_once('../../include/AjaxHeader.php');

$VK->Query("insert into hint (txt) values ('".iconv("UTF-8","WINDOWS-1251",$_POST['txt'])."')");

echo 1;
?>



