<?php
require_once('../../../../include/AjaxHeader.php');

$txt = textFormat(win1251($_POST['txt']));

if (!isset($_POST['cat'])) $_POST['cat'] = 0;
if (!isset($_POST['type'])) $_POST['type'] = 0;
if (!isset($_POST['client_id'])) $_POST['client_id'] = 0;
if (!isset($_POST['zayav_id'])) $_POST['zayav_id'] = 0;

$send->id = $VK->Query("INSERT INTO `gazeta_money` (
type,
expense_id,
sum,
prim,
client_id,
zayav_id,
kassa,
viewer_id_add
) values (
".$_POST['type'].",
".$_POST['cat'].",
".$_POST['sum'].",
'".$txt."',
".$_POST['client_id'].",
".$_POST['zayav_id'].",
".$_POST['kassa'].",
".$_GET['viewer_id']."
)");

if ($_POST['client_id'] > 0) setClientBalans($_POST['client_id']);

// Если есть категория расходов
if ($_POST['cat'] > 0) {
    $catName = $VK->QRow('SELECT `name` FROM `setup_expense` WHERE `id`='.$_POST['cat']);
    $dop = $catName.($txt ? ': ' : '').$txt;
    $txt = '<b>'.$catName.($txt ? ': ' : '').'</b>'.$txt;
} else $dop = $txt;

// Если это приход, составление
if ($_POST['type'] > 0) {
    $name = $VK->QRow('SELECT `name` FROM `setup_money_type` WHERE `id`='.$_POST['type']);
    $dop = $name.($txt ? ': ' : '').$txt;
}

$VK->Query('INSERT INTO `gazeta_log`
                  (`type`,
                   `client_id`,
                   `zayav_id`,
                   `value`,
                   `dop`,
                   `viewer_id_add`)
                VALUES
                  (4'.($_POST['client_id'] == 0 ? 5 : 6).',
                   '.$_POST['client_id'].',
                   '.$_POST['zayav_id'].',
                   "'.round($_POST['sum'], 2).'",
                   "'.$dop.'",
                   '.VIEWER_ID.')');

if ($_POST['kassa'] == 1) {
    $VK->Query("INSERT INTO `gazeta_kassa` (
        sum,
        txt,
        client_id,
        `zayav_id`,
        money_id,
        viewer_id_add
        ) values (
        ".$_POST['sum'].",
        '".$txt."',
        ".$_POST['client_id'].",
        ".$_POST['zayav_id'].",
        ".$send->id.",
        ".VIEWER_ID."
)");
}

echo json_encode($send);;
?>



