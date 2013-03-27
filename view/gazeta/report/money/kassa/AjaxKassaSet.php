<?php
require_once('../../../../../include/AjaxHeader.php');

$kassa_sum = $VK->QRow("select sum(sum) from gazeta_kassa");

$send->id = $VK->Query("update setup_global set kassa_start=".($_POST['summa'] - $kassa_sum));

$VK->Query('INSERT INTO `gazeta_log`
                (`type`,`value`,`viewer_id_add`)
            VALUES
                (71,"'.$_POST['summa'].'",'.VIEWER_ID.')');


echo json_encode($send);;
?>



