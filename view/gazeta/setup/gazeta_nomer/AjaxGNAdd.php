<?php
require_once('../../../../include/AjaxHeader.php');

$send->save = 1;
$gn = $VK->QRow("SELECT COUNT(`general_nomer`) FROM `gazeta_nomer` WHERE `general_nomer`=".$_POST['general_nomer']);
if ($gn > 0) $send->save = 0;

if ($send->save == 1) {
    $VK->Query("INSERT INTO `gazeta_nomer` (
        `week_nomer`,
        `general_nomer`,
        `day_print`,
        `day_public`
        ) VALUES (
        ".$_POST['week_nomer'].",
        ".$_POST['general_nomer'].",
        '".$_POST['day_print']."',
        '".$_POST['day_public']."')");
}

echo json_encode($send);
?>



