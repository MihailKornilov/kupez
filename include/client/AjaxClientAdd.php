<?php
require_once('../AjaxHeader.php');

$send->id = $VK->Query("INSERT INTO `gazeta_client` (
    `id`,
    `person`,
    `fio`,
    `telefon`,
    `org_name`,
    `adres`,
    `inn`,
    `kpp`,
    `email`,
    `skidka`,
    `viewer_id_add`
) values (
    ".$_POST['id'].",
    ".$_POST['person'].",
    '".win1251(textFormat($_POST['fio']))."',
    '".win1251(textFormat($_POST['telefon']))."',
    '".win1251(textFormat($_POST['org_name']))."',
    '".win1251(textFormat($_POST['adres']))."',
    '".win1251(textFormat($_POST['inn']))."',
    '".win1251(textFormat($_POST['kpp']))."',
    '".win1251(textFormat($_POST['email']))."',
    ".$_POST['skidka'].",
    ".VIEWER_ID.")
ON DUPLICATE KEY UPDATE
    `person`=".$_POST['person'].",
    `fio`='".win1251(textFormat($_POST['fio']))."',
    `telefon`='".win1251(textFormat($_POST['telefon']))."',
    `org_name`='".win1251(textFormat($_POST['org_name']))."',
    `adres`='".win1251(textFormat($_POST['adres']))."',
    `inn`='".win1251(textFormat($_POST['inn']))."',
    `kpp`='".win1251(textFormat($_POST['kpp']))."',
    `email`='".win1251(textFormat($_POST['email']))."',
    `skidka`=".$_POST['skidka']);

if($_POST['id'] > 0) $send->id = $_POST['id'];

$VK->Query('INSERT INTO `gazeta_log`
                (`type`,`client_id`,`viewer_id_add`)
            VALUES
                ('.($_POST['id'] == 0 ? 51 : 52).','.$send->id.','.VIEWER_ID.')');

echo json_encode($send);
?>



