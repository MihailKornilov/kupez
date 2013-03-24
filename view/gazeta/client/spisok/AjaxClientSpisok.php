<?php
require_once('../../../../include/AjaxHeader.php');

$find="WHERE `id`";
if($_GET['input']) {
    $input = win1251($_GET['input']);
    $find.=" AND (`org_name` LIKE '%".$input."%' OR
                  `fio` LIKE '%".$input."%' OR
                  `telefon` LIKE '%".$input."%' OR
                  `adres` LIKE '%".$input."%')";
}
if($_GET['dolg']==1) {
    $find .= " AND `balans`<0";
    $send->dolg = $VK->QRow("SELECT SUM(`balans`) FROM `client` ".$find);
}
if($_GET['person'] > 0)
    $find.=" AND `person`=".$_GET['person'];

$send->all = $VK->QRow("SELECT COUNT(`id`) FROM `gazeta_client` ".$find);
$send->next = 0;
$send->spisok = array();

$spisok = $VK->QueryObjectArray("SELECT * FROM `gazeta_client` ".$find." ORDER BY `id` DESC LIMIT ".$_GET['start'].",".$_GET['limit']);
if(count($spisok) > 0) {
    foreach($spisok as $sp) {
        if($_GET['input']) {
            $sp->org_name = preg_replace("/(".$input.")/i","<EM>\\1</EM>",$sp->org_name);
            $sp->fio = preg_replace("/(".$input.")/i","<EM>\\1</EM>",$sp->fio);
            $sp->telefon = preg_replace("/(".$input.")/i","<EM>\\1</EM>",$sp->telefon);
        }
        array_push($send->spisok, array(
            'id' => $sp->id,
            'org_name' => utf8($sp->org_name),
            'fio' => utf8($sp->fio),
            'telefon' => utf8($sp->telefon),
            'zayav_count' => $sp->zayav_count,
            'balans' => round($sp->balans, 2),


        ));
    }
    if(count($spisok) == $_GET['limit']) {
        if($VK->QNumRows("SELECT COUNT(`id`) FROM `gazeta_client` ".$find." LIMIT ".($_GET['start'] + $_GET['limit']).",".$_GET['limit']) > 0)
            $send->next = 1;

    }
}

echo json_encode($send);
?>