<?php
require_once('../../include/AjaxHeader.php');

$VK->Query("insert into hint (txt) values ('".win1251($_POST['txt'])."')");

echo 1;
?>



