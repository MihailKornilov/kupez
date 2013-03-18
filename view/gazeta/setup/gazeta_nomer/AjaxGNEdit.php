<?php
require_once('../../../../include/AjaxHeader.php');

/*
`day_begin`='".$_POST['day_begin']."',
`day_end`='".$_POST['day_end']."',
*/
$send->save = 1;
if ($_POST['general_nomer'] != $_POST['general_nomer_prev']) {
    $gn = $VK->QRow("SELECT COUNT(`general_nomer`) FROM `gazeta_nomer` WHERE `general_nomer`=".$_POST['general_nomer']);
    if ($gn > 0) $send->save = 0;
}

if ($send->save == 1) {
    $VK->Query("UPDATE `gazeta_nomer` SET
        `week_nomer`=".$_POST['week_nomer'].",
        `general_nomer`=".$_POST['general_nomer'].",
        `day_print`='".$_POST['day_print']."',
        `day_public`='".$_POST['day_public']."'
        WHERE `general_nomer`=".$_POST['general_nomer_prev']);
}

echo json_encode($send);
?>



