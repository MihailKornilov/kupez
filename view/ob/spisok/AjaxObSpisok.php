<?php
require_once('../../../include/AjaxHeader.php');

$find = "where status=1 and category=1 and active_day>='".strftime("%Y-%m-%d",time())."'";

if ($_GET['rub'] > 0) $find.=" and rubrika=".$_GET['rub'];
if ($_GET['podrub'] > 0) $find.=" and podrubrika=".$_GET['podrub'];
if ($_GET['foto_only'] == 1) $find.=" and length(file)>0";
if ($_GET['city_id'] > 0) $find.=" and city_id=".$_GET['city_id'];
if ($_GET['input']) {
  $input = win1251($_GET['input']);
  $find .= " and txt LIKE '%".$input."%'";
}

$send->all = $VK->QRow("select count(id) from gazeta_zayav ".$find);
$send->next = 0;
$send->spisok = array();

$spisok = $VK->QueryObjectArray("select * from gazeta_zayav ".$find." order by id desc limit ".$_GET['start'].",".$_GET['limit']);
if (count($spisok) > 0) {
  $viewers = array(); // —писок пользователей, которые показывают ссылку на свою страницу в объ€влении
  foreach ($spisok as $sp) {
    if ($sp->viewer_id_show == 1) { array_push($viewers, $sp->viewer_id_add); }
  }
  if (count($viewers) > 0) {
    $viewers = $VK->QueryPtPArray("select viewer_id,concat(first_name,' ',last_name) from vk_user where viewer_id in (".implode(',',array_unique($viewers)).")");
  }

  foreach ($spisok as $sp) {
    if($_GET['input']) { $sp->txt = preg_replace("/(".$input.")/i","<TT>\\1</TT>", $sp->txt); }
    array_push($send->spisok, array(
      'id' => $sp->id,
      'rubrika' => $sp->rubrika,
      'podrubrika' => $sp->podrubrika,
      'txt' => utf8($sp->txt),
      'telefon' => utf8($sp->telefon),
      'adres' => utf8($sp->adres),
      'file' => $sp->file,
      'dop' => $sp->dop,
      'viewer_id' => $sp->viewer_id_show ==1 ? $sp->viewer_id_add : 0,
      'viewer_name' => $sp->viewer_id_show ==1 ? utf8($viewers[$sp->viewer_id_add]) : '',
      'country_name' => utf8($sp->country_name),
      'city_name' => utf8($sp->city_name)
    ));
  }
  if(count($spisok) == $_GET['limit']) {
    if($VK->QNumRows("select id from gazeta_zayav ".$find." limit ".($_GET['start'] + $_GET['limit']).",".$_GET['limit']) > 0) {
      $send->next = 1;
    }
  }
}

$json = json_encode($send);
if ($_GET['cache_new'] == 2) {
  xcache_set('obSpisokFirst', $json, 86400);
  xcache_unset('rubrikaCount');
}

echo $json;
?>
