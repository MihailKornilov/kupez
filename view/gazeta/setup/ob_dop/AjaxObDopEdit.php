<?php
require_once('../../../../include/AjaxHeader.php');

$VK->Query("update setup_ob_dop set cena=".$_POST['cena']." where id=".$_POST['id']);
GvaluesCreate();
echo 1;
?>



