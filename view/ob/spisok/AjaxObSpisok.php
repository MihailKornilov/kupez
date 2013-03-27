<?php
require_once('../../../include/AjaxHeader.php');

$find = "WHERE `status`=1 AND `day_active`>='".strftime("%Y-%m-%d",time())."'";

if ($_GET['rub'] > 0) $find.=" AND rubrika=".$_GET['rub'];
if ($_GET['podrub'] > 0) $find.=" AND podrubrika=".$_GET['podrub'];
if ($_GET['foto_only'] == 1) $find.=" AND length(file)>0";
if ($_GET['city_id'] > 0) $find.=" AND city_id=".$_GET['city_id'];
if ($_GET['input']) {
  $input = win1251($_GET['input']);
  $find .= " AND `txt` LIKE '%".$input."%'";
}

$send = AjaxSpisokCreate("SELECT * FROM `vk_ob` ".$find);
if (count($send->spisok) > 0) {
    $spisok = $send->spisok;
    $send->spisok = array();
    $viewers = array(); // —писок пользователей, которые показывают ссылку на свою страницу в объ€влении
    foreach ($spisok as $sp) {
        if ($sp->viewer_id_show == 1) { array_push($viewers, $sp->viewer_id_add); }
    }
    if (count($viewers) > 0) {
        $viewers = $VK->QueryPtPArray("SELECT
                                     `viewer_id`,CONCAT(`first_name`,' ',`last_name`)
                                   FROM `vk_user`
                                   WHERE `viewer_id` IN (".implode(',',array_unique($viewers)).")");
    }

    foreach ($spisok as $sp) {
        if($_GET['input']) { $sp->txt = preg_replace("/(".$input.")/i","<TT>\\1</TT>", $sp->txt); }
        array_push($send->spisok, array(
            'id' => $sp->id,
            'rubrika' => $sp->rubrika,
            'podrubrika' => $sp->podrubrika,
            'txt' => utf8($sp->txt),
            'telefon' => utf8($sp->telefon),
            'file' => $sp->file,
            'dop' => $sp->dop,
            'viewer_id' => $sp->viewer_id_show ==1 ? $sp->viewer_id_add : 0,
            'viewer_name' => $sp->viewer_id_show ==1 ? utf8($viewers[$sp->viewer_id_add]) : '',
            'country_name' => utf8($sp->country_name),
            'city_name' => utf8($sp->city_name)
        ));
    }
}

$send->time = round(microtime(true) - TIME, 3);

echo json_encode($send);
?>