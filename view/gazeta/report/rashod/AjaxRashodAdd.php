<?php
require_once('../../../include/AjaxHeader.php');

$send->id=$VK->Query("insert into rashod (name,summa,viewer_id_add) values ('".win1251($_POST['name'])."','".$_POST['summa']."',".VIEWER_ID.")");

echo json_encode($send);
?>



