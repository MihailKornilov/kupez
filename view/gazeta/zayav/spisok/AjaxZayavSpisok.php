<?php
require_once('../../../../include/AjaxHeader.php');

function repl($pole) { return utf8(preg_replace("/(".$_GET['input'].")/i", "<tt>\\1</tt>", $pole, 1)); }

$find = 'WHERE id';

if($_GET['input']) {
    $fast = win1251($_GET['input']);
    $_GET['input'] = win1251($_GET['input']);
    $fast = preg_replace( '/\s+/','',$fast);
    $id = (preg_match("|^[\d]+$|", $fast) ? " OR `id`=".$fast : '');
    $find .= " AND (
            REPLACE(`txt`,' ','') LIKE '%".$fast."%' OR
            REPLACE(`telefon`,' ','') LIKE '%".$fast."%' OR
            REPLACE(`adres`,' ','') LIKE '%".$fast."%' OR
            `txt` LIKE '%".$fast."%' OR
            `telefon` LIKE '%".$fast."%' OR
            `adres` LIKE '%".$fast."%'".$id.")";
} else {
    if ($_GET['category'] > 0) $find.=" AND `category`=".$_GET['category'];
    if ($_GET['no_public'] > 0) $find.=" AND `gn_count`=0";
    else {
        if ($_GET['gazeta_nomer'] > 0) {
            $ids = $VK->ids("SELECT DISTINCT(`zayav_id`) FROM `gazeta_nomer_pub` WHERE `general_nomer`=".$_GET['gazeta_nomer']);
            $find .= " AND `id` IN (".($ids?$ids:0).")";
        } elseif ($_GET['year'] > 0) {
            $ids = $VK->ids("SELECT `general_nomer` FROM `gazeta_nomer` WHERE SUBSTR(`day_public`,1,4)=".$_GET['year']);
            $ids = $VK->ids('SELECT DISTINCT(`zayav_id`) FROM `gazeta_nomer_pub` WHERE `general_nomer` IN ('.($ids?$ids:0).')');
            $find .= " AND `id` IN (".($ids?$ids:0).")";
        }
    }
}

$send = AjaxSpisokCreate("SELECT * FROM `gazeta_zayav` ".$find);
if (count($send->spisok) > 0) {
    $spisok = $send->spisok;
    $send->spisok = array();

    $client = array();
    foreach($spisok as $sp) {
        if ($sp->client_id > 0) array_push($client, $sp->client_id);
    }

    if (count($client) > 0)
        $client = $VK->objectAss('SELECT `id`, `fio`, `org_name` FROM `gazeta_client` WHERE `id` IN ('.implode(',', array_unique($client)).')');

    foreach($spisok as $sp) {
        $push = array(
            'id' => $sp->id,
            'category' => $sp->category,
            'rubrika' => $sp->rubrika,
            'podrubrika' => $sp->podrubrika,
            'summa' => round($sp->summa, 2),
            'summa_manual' => $sp->summa_manual,
            'txt' => utf8($sp->txt),
            'size_x' => round($sp->size_x, 1),
            'size_y' => round($sp->size_y, 1),
            'kv_sm' => round($sp->size_x * $sp->size_y, 2),
            'file' => $sp->file,
            'dtime' => utf8(FullDataTime($sp->dtime_add))
        );

        if ($sp->client_id > 0) {
            $push['client_id'] = $sp->client_id;
            $push['client_fio'] = utf8($client[$sp->client_id]->org_name ? $client[$sp->client_id]->org_name : $client[$sp->client_id]->fio);
        }

        if($_GET['input']) {
            if (preg_match("/(".$_GET['input'].")/i", $sp->telefon)) $push['telefon'] = repl($sp->telefon);
            if (preg_match("/(".$_GET['input'].")/i", $sp->adres)) $push['adres'] = repl($sp->adres);
        }

        array_push($send->spisok, $push);
    }
}

echo json_encode($send);
?>



