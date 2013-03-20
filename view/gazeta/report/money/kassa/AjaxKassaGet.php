<?php
require_once('../../../../../include/AjaxHeader.php');

$find = "WHERE id ";
$find .= " and dtime_add>='".$_GET['day_begin']." 00:00:00'";
$find .= " and dtime_add<='".$_GET['day_end']." 23:59:59'";

$send = AjaxSpisokCreate("SELECT * FROM `gazeta_kassa` ".$find, 'asc');
$send->sum = 0;
if (count($send->spisok) > 0) {
    $send->sum = round($VK->QRow("SELECT IFNULL(SUM(`sum`),0) FROM `gazeta_kassa` ".$find), 2) + KASSA_START;
    $spisok = $send->spisok;
    $send->spisok = array();
    foreach($spisok as $sp) {
        array_push($send->spisok, array(
            'zayav_id' => $sp->zayav_id,
            'sum' => round($sp->sum, 2),
            'txt' => utf8($sp->txt),
            'dtime_add' => utf8(FullDataTime($sp->dtime_add)),
            'viewer_id' => $sp->viewer_id_add
        ));
    }
}
echo json_encode($send);
?>