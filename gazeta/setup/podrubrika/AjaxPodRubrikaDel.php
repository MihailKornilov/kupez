<?php
require_once('../../../include/AjaxHeader.php');

$VK->Query("delete from setup_pod_rubrika where id=".$_POST['id']);

echo 1;
?>



