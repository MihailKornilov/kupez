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

    $fio = array();
    foreach ($spisok as $sp) {
        if ($sp->client_id > 0) { array_push($fio, $sp->client_id); }
    }
    if (count($fio) > 0) { $c = $VK->ObjectAss("SELECT id,fio,org_name FROM gazeta_client WHERE id IN (".implode(',', array_unique($fio)).")"); }

    foreach($spisok as $sp) {
        $unit = array(
            'type' => $sp->type,
            'sum' => round($sp->sum, 2),
            'txt' => utf8($sp->prim),
            'dtime_add' => utf8(FullDataTime($sp->dtime_add)),
            'viewer_id' => $sp->viewer_id_add
        );

        if ($sp->zayav_id > 0) { $unit['zayav_id'] = $sp->zayav_id; }

        if ($sp->client_id > 0) { $unit['client_id'] = $sp->client_id; }
        if (isset($c[$sp->client_id])) { $unit['client_fio'] =  utf8($c[$sp->client_id]->org_name ? $c[$sp->client_id]->org_name : $c[$sp->client_id]->fio); }

        array_push($send->spisok, $unit);
    }
}
echo json_encode($send);
?>