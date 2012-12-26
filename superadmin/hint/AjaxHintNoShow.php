<?php
require_once('../../include/AjaxHeader.php');

$VK->Query("insert into hint_no_show (viewer_id,hint_id) values (".$_GET['viewer_id'].",".$_POST['hint_id'].")");

echo 1;
?>



