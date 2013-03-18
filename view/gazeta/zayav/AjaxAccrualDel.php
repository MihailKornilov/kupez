<?php
require_once('../../include/AjaxHeader.php');

$VK->Query("delete from accrual where id=".$_GET['id']);
echo 1;
?>
