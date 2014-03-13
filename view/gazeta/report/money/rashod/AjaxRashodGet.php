<?php
require_once('../../../../../include/AjaxHeader.php');

$find = "WHERE `status`=1 AND `sum`<0";
$find .= " and dtime_add>='".$_GET['day_begin']." 00:00:00'";
$find .= " and dtime_add<='".$_GET['day_end']." 23:59:59'";
if ($_GET['category'] > 0)
    $find .= " AND `expense_id`=".$_GET['category'];

$send = AjaxSpisokCreate("SELECT * FROM `gazeta_money` ".$find, 'asc');
$send->sum = 0;
if (count($send->spisok) > 0) {
    $send->sum = $VK->QRow("SELECT IFNULL(SUM(`sum`),0) FROM `gazeta_money` ".$find) * -1;
    $spisok = $send->spisok;
    $send->spisok = array();
    foreach($spisok as $sp) {
        array_push($send->spisok, array(
            'cat' => $sp->expense_id,
            'zayav_id' => $sp->zayav_id,
            'sum' => round($sp->sum * -1, 2),
            'txt' => utf8($sp->prim),
            'dtime_add' => utf8(FullDataTime($sp->dtime_add)),
            'viewer_id' => $sp->viewer_id_add
        ));
    }
}
echo json_encode($send);
?>