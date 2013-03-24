<?php
require_once('../../../../../include/AjaxHeader.php');

$find = "WHERE `status`=1 AND `sum`>0";
$find .= " AND dtime_add>='".$_GET['day_begin']." 00:00:00'";
$find .= " AND dtime_add<='".$_GET['day_end']." 23:59:59'";
if ($_GET['type'] > 0)
    $find .= " AND `type` = ".$_GET['type'];


$send = AjaxSpisokCreate("SELECT * FROM `gazeta_money` ".$find, 'asc');
$send->sum = 0;
if (count($send->spisok) > 0) {
    $send->sum = round($VK->QRow("SELECT IFNULL(SUM(`sum`),0) FROM `gazeta_money` ".$find), 2);
    $spisok = $send->spisok;
    $send->spisok = array();
    foreach($spisok as $sp) {
        array_push($send->spisok, array(
            'type' => $sp->type,
            'zayav_id' => $sp->zayav_id,
            'sum' => round($sp->sum, 2),
            'txt' => utf8($sp->prim),
            'dtime_add' => utf8(FullDataTime($sp->dtime_add)),
            'viewer_id' => $sp->viewer_id_add
        ));
    }
}
echo json_encode($send);
?>