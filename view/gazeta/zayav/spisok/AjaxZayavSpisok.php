<?php
require_once('../../../../include/AjaxHeader.php');

$find = 'WHERE `whence`="kupez"';

if($_GET['input']) {
    $fast = win1251($_GET['input']);
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
    if ($_GET['gazeta_nomer'] > 0) {
        $ids = $VK->ids("SELECT DISTINCT(`zayav_id`) FROM `gazeta_nomer_pub` WHERE `general_nomer`=".$_GET['gazeta_nomer']);
        $find .= " AND `id` IN (".($ids?$ids:0).")";
    } elseif ($_GET['year'] > 0) {
        $ids = $VK->ids("SELECT `general_nomer` FROM `gazeta_nomer` WHERE SUBSTR(`day_public`,1,4)=".$_GET['year']);
        $ids = $VK->ids('SELECT DISTINCT(`zayav_id`) FROM `gazeta_nomer_pub` WHERE `general_nomer` IN ('.($ids?$ids:0).')');
        $find .= " AND `id` IN (".($ids?$ids:0).")";
    }
}

$send = AjaxSpisokCreate("SELECT * FROM `zayav` ".$find);
if(count($send->spisok) > 0) {
  $spisok = $send->spisok;
  $send->spisok = array();
  foreach($spisok as $sp) {
    array_push($send->spisok, array(
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
        'dtime' => utf8(FullDataTime($sp->dtime_add))
    ));
/*
    $send[$n]->ob_dop='';
    if($sp->category==1) {
      $id = $VK->QRow("select ob_dop_id from gazeta_nomer_pub where zayav_id=".$sp->id." order by general_nomer limit 1");
      if ($id > 0) {
        $send[$n]->ob_dop = utf8($obDop[$id]);
      } else {
        $send[$n]->ob_dop = '';
      }
    }

    if(isset($_GET['fast'])) {
      $send[$n]->txt=utf8(preg_replace("/(".$fast.")/i","<TT>\\1</TT>",$sp->txt));
      $send[$n]->telefon=utf8(preg_replace("/(".$fast.")/i","<TT>\\1</TT>",$sp->telefon));
      $send[$n]->adres=utf8(preg_replace("/(".$fast.")/i","<TT>\\1</TT>",$sp->adres));
      }
    $send[$n]->client_id=$sp->client_id;
    $send[$n]->fio = utf8($fio);
    $send[$n]->file=$sp->file;
    */
    }
}

echo json_encode($send);
?>



