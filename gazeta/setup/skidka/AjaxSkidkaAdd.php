<?php
require_once('../../../include/AjaxHeader.php');

$VK->Query("insert into setup_skidka (razmer,about,viewer_id_add) values (".$_POST['razmer'].",'".iconv("UTF-8","WINDOWS-1251",$_POST['about'])."',".$_GET['viewer_id'].")");

echo 1;
?>



