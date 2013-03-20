<?php
require_once('../../../../../include/AjaxHeader.php');

$find = "WHERE `status`=1 AND `sum`<0 AND DATE_FORMAT(`dtime_add`,'%Y')=".$_GET['year'];
if ($_GET['category'] > 0)
    $find .= " AND `rashod_category`=".$_GET['category'];

$spisok = $VK->QueryObjectArray("SELECT
                                    DISTINCT(DATE_FORMAT(`dtime_add`,'%m')) AS `month`,
                                    sum(sum)*-1 AS `sum`
                                 FROM `gazeta_money`
                                 ".$find." GROUP BY DATE_FORMAT(`dtime_add`,'%m')");
$send->sum = 0;
$send->spisok = array();
if (count($spisok) > 0) {
    $send->sum = round($VK->QRow("SELECT IFNULL(SUM(`sum`),0) FROM `gazeta_money` ".$find) * -1, 2);
    foreach($spisok as $sp) {
        array_push($send->spisok, array(
            'month' => abs($sp->month),
            'sum' => round($sp->sum * -1, 2) * -1
        ));
    }
}
echo json_encode($send);
?>
