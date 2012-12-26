<?php
require_once('../../include/AjaxHeader.php');

$VK->Query("insert into oplata (
client_id,
tip,
summa,
prim,
viewer_id_add
) values (
".$_POST['client_id'].",
".$_POST['tip'].",
".$_POST['summa'].",
'".iconv("UTF-8","WINDOWS-1251",$_POST['prim'])."',
".$_GET['viewer_id'].")");

$send->balans=setClientBalans($_POST['client_id']);

echo json_encode($send);
?>



