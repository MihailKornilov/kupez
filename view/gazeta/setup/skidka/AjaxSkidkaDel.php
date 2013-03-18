<?php
require_once('../../../../include/AjaxHeader.php');

$VK->Query("delete from setup_skidka where id=".$_POST['id']);
GvaluesCreate();
echo 1;
?>



