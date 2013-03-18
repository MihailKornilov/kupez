<?php
require_once('../../../../include/AjaxHeader.php');

$VK->Query("delete from setup_polosa_cost where id=".$_POST['id']);
GvaluesCreate();
echo 1;
?>



