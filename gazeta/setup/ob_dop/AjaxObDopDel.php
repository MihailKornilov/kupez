<?php
require_once('../../../include/AjaxHeader.php');

$VK->Query("delete from setup_ob_dop where id=".$_POST['id']);

echo 1;
?>



