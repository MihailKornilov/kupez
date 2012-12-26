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
'".iconv("UTF-8","WINDOWS-1251",$_POST['org_name'])."',
'".iconv("UTF-8","WINDOWS-1251",$_POST['fio'])."',
'".iconv("UTF-8","WINDOWS-1251",$_POST['telefon'])."',
'".iconv("UTF-8","WINDOWS-1251",$_POST['adres'])."',
".$_GET['viewer_id'].")");

echo json_encode($send);
?>



