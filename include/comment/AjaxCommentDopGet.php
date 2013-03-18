<?php
require_once('../AjaxHeader.php');

$spisok=$VK->QueryObjectArray("select * from vk_user");
foreach($spisok as $sp)
	{
	$vkUs[$sp->viewer_id]->first_name=utf8($sp->first_name);
	$vkUs[$sp->viewer_id]->last_name=utf8($sp->last_name);
	$vkUs[$sp->viewer_id]->photo=$sp->photo;
	}

$spisok=$VK->QueryObjectArray("select * from vk_comment where parent_id=".$_GET['parent_id']." and status=1 and table_name='".$_GET['table_name']."' and table_id=".$_GET['table_id']." order by id");
foreach($spisok as $n=>$sp)
	{
	$send[$n]->id=$sp->id;
	$send[$n]->viewer_id=$sp->viewer_id_add;
	$send[$n]->first_name=$vkUs[$sp->viewer_id_add]->first_name;
	$send[$n]->last_name=$vkUs[$sp->viewer_id_add]->last_name;
	$send[$n]->photo=$vkUs[$sp->viewer_id_add]->photo;
	$send[$n]->txt=utf8($sp->txt);
	$send[$n]->dtime_add=utf8(FullDataTime($sp->dtime_add));
	}

echo json_encode($send);
?>
