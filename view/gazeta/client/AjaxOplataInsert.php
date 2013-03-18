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
'".win1251($_POST['prim'])."',
".VIEWER_ID.")");

$send->balans=setClientBalans($_POST['client_id']);

echo json_encode($send);
?>



