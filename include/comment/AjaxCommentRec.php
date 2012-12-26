<?php
require_once('../AjaxHeader.php');

$VK->Query("update vk_comment set status=1,viewer_id_del=0,dtime_del='0000-00-00 00:00:00' where id=".$_POST['rec']);
$comm=$VK->QueryObjectOne("select table_name,table_id from vk_comment where id=".$_POST['rec']);
$send->count=$VK->QRow("select count(id) from vk_comment where parent_id=0 and status=1 and table_name='".$comm->table_name."' and table_id=".$comm->table_id);

echo json_encode($send);
?>
