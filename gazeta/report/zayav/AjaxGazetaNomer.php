<?php
/*
 * возвращает количества заявок по категориям для конкретного номера выпуска
 * значения:
 * $_GET['nomer'] - general_nomer
*/

require_once('../../../include/AjaxHeader.php');

$spisok = $VK->QueryObjectArray("select zayav_id from gazeta_nomer_pub where general_nomer=".$_GET['nomer']);
$ids = '0';
foreach ($spisok as $sp) { $ids .= ','.$sp->zayav_id; }

for ($n = 1; $n <= 4; $n++) {
  $send->zayav[$n] = $VK->QRow("select count(id) from zayav where id in (".$ids.") and category=".$n);
}

$send->time = getTime($T);

echo json_encode($send);
?>



