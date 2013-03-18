<?php
require_once('../AjaxHeader.php');

$user=$VK->QueryObjectOne("select first_name,last_name,photo from vk_user where viewer_id=".VIEWER_ID);
$send[0]->autor_viewer_id=VIEWER_ID;
$send[0]->autor_first_name=utf8($user->first_name);
$send[0]->autor_last_name=utf8($user->last_name);
$send[0]->autor_photo=$user->photo;

$spisok=$VK->QueryObjectArray("select * from vk_user");
foreach($spisok as $sp)
	{
	$vkUs[$sp->viewer_id]->first_name=utf8($sp->first_name);
	$vkUs[$sp->viewer_id]->last_name=utf8($sp->last_name);
	$vkUs[$sp->viewer_id]->photo=$sp->photo;
	}

$spisok=$VK->QueryObjectArray("select * from vk_comment where parent_id=0 and status=1 and table_name='".$_GET['table_name']."' and table_id=".$_GET['table_id']." order by id desc");
$send[0]->count=count($spisok);
if($send[0]->count>0)
	foreach($spisok as $n=>$sp)
		{
		$send[$n]->id=$sp->id;
		$send[$n]->viewer_id=$sp->viewer_id_add;
		$send[$n]->first_name=$vkUs[$sp->viewer_id_add]->first_name;
		$send[$n]->last_name=$vkUs[$sp->viewer_id_add]->last_name;
		$send[$n]->photo=$vkUs[$sp->viewer_id_add]->photo;
		$send[$n]->txt=utf8($sp->txt);
		$send[$n]->child=$sp->child_count;
		$send[$n]->dtime_add=utf8(FullDataTime($sp->dtime_add));
		}

echo json_encode($send);
?>
