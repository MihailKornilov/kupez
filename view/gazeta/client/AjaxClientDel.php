<?php
require_once('../../include/AjaxHeader.php');

$VK->Query("delete from client where id=".$_GET['id']);

$send->info=1;
echo json_encode($send);
?>



