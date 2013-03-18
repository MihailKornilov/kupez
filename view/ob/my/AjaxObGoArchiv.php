<?php
require_once('../../include/AjaxHeader.php');

$VK->Query("update zayav set active_day='0000-00-00' where id=".$_GET['id']);

rubrikaCountUpdate($VK->QRow("select rubrika from zayav where id=".$_GET['id']));

$send->id=1;

echo json_encode($send);
?>



