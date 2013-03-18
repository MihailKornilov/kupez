<?php
require_once('../../../../include/AjaxHeader.php');

$VK->Query("update setup_skidka set razmer=".$_POST['razmer'].",about='".win1251($_POST['about'])."' where id=".$_POST['id']);
GvaluesCreate();
echo 1;
?>



