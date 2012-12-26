<?php
require_once('../../../include/AjaxHeader.php');

$VK->Query("update gazeta_nomer set
week_nomer=".$_POST['week_nomer'].",
general_nomer=".$_POST['general_nomer'].",
day_begin='".$_POST['day_begin']."',
day_end='".$_POST['day_end']."',
day_print='".$_POST['day_print']."',
day_public='".$_POST['day_public']."'
where id=".$_POST['id']);

echo json_encode($send);
?>



