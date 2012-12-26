<?php
require_once('../../../include/AjaxHeader.php');

$VK->Query("update setup_global set ".$_GET['name']."=".$_GET['value']);
echo 1;
?>



