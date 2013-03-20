<?php
require_once('../../../../include/AjaxHeader.php');

$txt = textFormat(win1251($_POST['txt']));

if(!isset($_POST['cat'])) $_POST['cat'] = 0;
if ($_POST['cat'] > 0) {
    $catName = $VK->QRow('SELECT `name` FROM `setup_rashod_category` WHERE `id`='.$_POST['cat']);
    $txt = '<b>'.$catName.'</b>'.($txt ? ': ' : '').$txt;
}

$send->id = $VK->Query("INSERT INTO `gazeta_money` (
rashod_category,
sum,
prim,
kassa,
viewer_id_add
) values (
".$_POST['cat'].",
".$_POST['sum'].",
'".$txt."',
".$_POST['kassa'].",
".$_GET['viewer_id']."
)");

if ($_POST['kassa'] == 1) {
  $VK->Query("INSERT INTO `gazeta_kassa` (
        sum,
        txt,
        money_id,
        viewer_id_add
        ) values (
        ".$_POST['sum'].",
        '".$txt."',
        ".$send->id.",
        ".VIEWER_ID."
)");
}

echo json_encode($send);;
?>



