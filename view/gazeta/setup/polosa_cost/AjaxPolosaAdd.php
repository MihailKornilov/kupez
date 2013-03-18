<?php
require_once('../../../../include/AjaxHeader.php');

$sort = $VK->QRow("select ifnull(max(sort)+1,0) from setup_polosa_cost");
$VK->Query("INSERT INTO `setup_polosa_cost` (
    `name`,
    `cena`,
    `sort`,
    `viewer_id_add`
    ) values (
    '".win1251($_POST['name'])."',
    '".$_POST['cena']."',
    ".$sort.",
    ".VIEWER_ID.")");
GvaluesCreate();
echo 1;
?>



