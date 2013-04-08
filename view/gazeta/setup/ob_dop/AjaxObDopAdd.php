<?php
require_once('../../../include/AjaxHeader.php');

$VK->Query("insert into setup_ob_dop (name,cena,viewer_id_add) values ('".win1251($_POST['name'])."',".$_POST['cena'].",".VIEWER_ID.")");

echo 1;
?>



