<?php
function obEnd($count)
  {
  $ost=$count%10;
  $ost10=$count/10%10;

  if($ost10==1) return 'й';
  else
    switch($ost)
      {
      case '1': return 'е';
      case '2': return 'я';
      case '3': return 'я';
      case '4': return 'я';
      default: return 'й';
      }
  }

setlocale(LC_ALL, 'ru_RU.CP1251', 'rus_RUS.CP1251', 'Russian_Russia.1251');
require_once('../../include/AjaxHeader.php');

$find = "where status=1 and category=1 and vk_day_active>='".strftime("%Y-%m-%d",time())."'";
if ($_GET['rub'] > 0) $find.=" and rubrika=".$_GET['rub'];
if ($_GET['podrub'] > 0) $find.=" and podrubrika=".$_GET['podrub'];
if ($_GET['foto_only'] == 1) $find.=" and length(file)>0";
if (isset($_GET['input'])) {
  $input = iconv("UTF-8", "WINDOWS-1251",$_GET['input']);
  $find .= " and txt LIKE '%".$input."%'";
}

$count = $VK->QRow("select count(id) from zayav ".$find);
if ($_GET['rub'] > 0 or
   $_GET['podrub'] > 0 or
   $_GET['foto_only'] == 1 or
   isset($_GET['input'])) { $fCount="Найдено "; } else { $fCount="Всего "; }

$send->result = utf8($fCount.$count." объявлени".obEnd($count));
$send->page = 0;
$send->spisok = array();

$maxCount = 50;
$spisok = $VK->QueryObjectArray("select * from zayav ".$find." order by id desc limit ".(($_GET['page']-1)*$maxCount).",".$maxCount);
if (count($spisok) > 0) {
  $ids = "0";
  foreach ($spisok as $sp) {
    if ($sp->vk_viewer_id_show == 1) { $ids .= ",".$sp->viewer_id_add; }
  }
  if ($ids != '0') {
    $vkUsers = $VK->QueryObjectArray("select viewer_id,first_name,last_name from vk_user where viewer_id in (".$ids.")");
    foreach ($vkUsers as $us) {
      $vkName[$us->viewer_id] = utf8($us->first_name." ".$us->last_name);
    }
  }

  foreach ($spisok as $sp) {
    if(isset($input)) { $sp->txt = preg_replace("/(".$input.")/i","<TT>\\1</TT>", $sp->txt); }
    $arr = array(
      'id' => $sp->id,
      'rubrika' => $sp->rubrika,
      'podrubrika' => $sp->podrubrika,
      'txt' => utf8($sp->txt),
      'telefon' => utf8($sp->telefon),
      'adres' => utf8($sp->adres),
      'file' => $sp->file,
      'dop' => $sp->dop,
      'viewer_id' => $sp->vk_viewer_id_show ==1 ? $sp->viewer_id_add : 0,
      'vk_name' => $sp->vk_viewer_id_show ==1 ? $vkName[$sp->viewer_id_add] : ''
    );
    array_push($send->spisok, $arr);
  }
  if(count($spisok) == $maxCount) {
    if($VK->QNumRows("select id from zayav ".$find." limit ".($_GET['page']*$maxCount).",".$maxCount) > 0) {
      $send->page = $_GET['page'] + 1;
    }
  }
}

$send->time = $_GET['viewer_id'] == 982006 ? getTime($T)." " : '';

$json = json_encode($send);
if (!xcache_get('obSpisokFirst') or $_GET['cache_new'] == 1) {
  xcache_set('obSpisokFirst', $json, 86400);
  xcache_unset('rubrikaCount');
  }
echo $json;
?>



