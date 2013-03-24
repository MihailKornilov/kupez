<?php
require_once('../../../../include/AjaxHeader.php');

$c = $VK->QueryObjectOne('SELECT `fio`,`org_name` FROM `gazeta_client` WHERE `id`='.$_GET['id']);
$VK->Query("DELETE FROM `gazeta_client` WHERE `id`='".$_GET['id']."'");

$VK->Query('INSERT INTO `gazeta_log`
                (`type`,`value`,`viewer_id_add`)
            VALUES
                (53,"'.($c->org_name ? $c->org_name : $c->fio).'",'.VIEWER_ID.')');

$send->info = 1;
echo json_encode($send);
?>



