<?php
require_once('../../../include/AjaxHeader.php');
if($_POST['ost']>0) $VK->Query("update client set person=".$_POST['ost']." where person=".$_POST['del']);
$VK->Query("delete from setup_person where id=".$_POST['del']);
echo 1;
?>



