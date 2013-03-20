<?php
require_once('../../../../../include/AjaxHeader.php');

$send->id = $VK->Query("INSERT INTO `gazeta_kassa` (
sum,
txt,
viewer_id_add
) values (
".$_POST['sum'].",
'".textFormat(win1251($_POST['txt']))."',
".VIEWER_ID."
)");

echo json_encode($send);;
?>



