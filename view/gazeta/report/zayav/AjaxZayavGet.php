<?php
require_once('../../../include/AjaxHeader.php');

$mon = '';
if ($_GET['mon'] > 0) { $mon = $_GET['mon'] < 10 ? '0'.$_GET['mon'] : $_GET['mon']; }
$find = $_GET['year']."-".$mon."%";

for ($n = 1; $n <= 4; $n++) {
  $send->main[$n] = $VK->QRow("select count(id) from zayav where dtime_add like '".$find."' and category=".$n);
}

if ($_GET['allmon']) {
  for ($n = 1; $n <= 12; $n++) {
    $send->allmon[$n] = $VK->QRow("select count(id) from zayav where dtime_add like '".$_GET['year']."-".($n <10 ? '0'.$n : $n)."%'");
    if ($send->allmon[$n] ==0 ) { $send->allmon[$n] = ''; }
  }
}

$send->nomer = array();
$spisok = $VK->QueryObjectArray("select * from gazeta_nomer where day_public like '".$find."%' order by general_nomer");
if (count($spisok) > 0) {
  foreach ($spisok as $sp) {
    $count = $VK->QueryRowOne("select count(id) from gazeta_nomer_pub where general_nomer=".$sp->general_nomer);
    $d = explode("-",$sp->day_public);
    array_push($send->nomer, array(
      'week_nomer' => $sp->week_nomer,
      'general_nomer' => $sp->general_nomer,
      'public' => utf8(abs($d[2])." ".$MonthCut[$d[1]]),
      'count' => $count[0]>0?$count[0]:''
    ));
  }
}

$send->time = getTime($T);

echo json_encode($send);
?>



