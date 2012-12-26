<?php
require_once('../../../include/AjaxHeader.php');

$VK->Query("delete from worker where viewer_id=".$_POST['viewer_id']);

$send=1;
echo json_encode($send);
?>



