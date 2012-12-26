<?php
require_once('../../../include/AjaxHeader.php');

$VK->Query("delete from gazeta_nomer where id=".$_POST['id']);

echo 1;
?>



