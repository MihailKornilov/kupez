<?php
require_once('../../../../include/AjaxHeader.php');

$find = 'WHERE `dtime_add`>="'.$_GET['day_begin'].' 00:00:00" AND '.
              '`dtime_add`<="'.$_GET['day_end'].' 23:59:59"';

if ($_GET['worker'] > 0) $find .= ' AND `viewer_id_add`='.$_GET['worker'];

if ($_GET['type'] > 0) {
    switch ($_GET['type']) {
        default: $find .= ' AND type='.$_GET['type']; break;
        case 1: $find .= ' AND type IN (11,12,13,14,21,22,23,24)'; break;
        case 3: $find .= ' AND type IN (31,32,33,34)'; break;
        case 4: $find .= ' AND type IN (41,42,43,44,45,46)'; break;
        case 6: $find .= ' AND type IN (61,62,63,64)'; break;
        case 1000: $find .= ' AND `type`>1000 AND `type`<2000'; break;
    }
}
$send->all = $VK->QRow("SELECT COUNT(`id`) FROM `gazeta_log` ".$find);
$send->next = 0;
$send->spisok = array();

$send = AjaxSpisokCreate("SELECT * FROM `gazeta_log` ".$find);
if (count($send->spisok) > 0) {
    $spisok = $send->spisok;
    $send->spisok = array();
    $fio = array();
    foreach ($spisok as $sp) {
        if ($sp->client_id > 0) { array_push($fio, $sp->client_id); }
    }

    if (count($fio) > 0) { $c = $VK->ObjectAss("SELECT id,fio,org_name FROM gazeta_client WHERE id IN (".implode(',', array_unique($fio)).")"); }

    foreach ($spisok as $sp) {
        $unit = array(
            'id' => $sp->id,
            'type' => $sp->type,
            'dtime' => utf8(FullDataTime($sp->dtime_add, 1)),
            'viewer_id' => $sp->viewer_id_add
        );

        if ($sp->client_id > 0) { $unit['client_id'] = $sp->client_id; }
        if (isset($c[$sp->client_id])) { $unit['client_fio'] =  utf8($c[$sp->client_id]->org_name ? $c[$sp->client_id]->org_name : $c[$sp->client_id]->fio); }

        if ($sp->zayav_id > 0) { $unit['zayav_id'] = $sp->zayav_id; }

        if ($sp->value) { $unit['value'] = utf8($sp->value); }
        if ($sp->dop) { $unit['dop'] = utf8($sp->dop); }

        array_push($send->spisok, $unit);
    }
}

echo json_encode($send);
?>



