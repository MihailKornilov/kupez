<?php
require_once('../AjaxHeader.php');

$send->id=$VK->Query("insert into client (
person,
org_name,
fio,
telefon,
adres,
viewer_id_add
) values (
".$_POST['person'].",
'".win1251($_POST['org_name'])."',
'".win1251($_POST['fio'])."',
'".win1251($_POST['telefon'])."',
'".win1251($_POST['adres'])."',
".VIEWER_ID.")");

echo json_encode($send);
?>



