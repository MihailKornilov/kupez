<?php
require_once('../../../../include/AjaxHeader.php');

$VK->Query("update setup_polosa_cost set name='".win1251($_POST['name'])."',cena='".$_POST['cena']."' where id=".$_POST['id']);
GvaluesCreate();
echo 1;
?>



