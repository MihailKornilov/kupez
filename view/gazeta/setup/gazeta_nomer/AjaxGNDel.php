<?php
require_once('../../../../include/AjaxHeader.php');

$VK->Query("delete from gazeta_nomer where general_nomer=".$_POST['general_nomer']);

echo 1;
?>



