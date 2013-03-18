<?php
require_once('../../../../include/AjaxHeader.php');

$VK->Query("update setup_rubrika set name='".win1251($_POST['name'])."' where id=".$_POST['id']);
GvaluesCreate();
echo 1;
?>



