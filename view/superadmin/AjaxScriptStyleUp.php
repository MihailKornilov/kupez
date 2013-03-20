<?php
require_once('../../include/AjaxHeader.php');

$VK->Query("update setup_global set script_style=script_style+1 limit 1");

echo 1;
?>



