<?php
require_once('../../../../include/AjaxHeader.php');

$VK->Query("update setup_global set ".$_POST['name']."=".$_POST['val']);
GvaluesCreate();
echo 1;
?>



