<?php
require_once('../../../include/AjaxHeader.php');

$spisok=$VK->QueryRowArray("select
vk_user.viewer_id,
vk_user.first_name,
vk_user.last_name,
vk_user.photo,
vk_user.sex,
vk_user.dtime_add,
worker.admin
from worker,vk_user where vk_user.viewer_id=worker.viewer_id and worker.viewer_id!=982006 order by worker.dtime_add");
$send[0]->count=count($spisok);
if($send[0]->count>0)
	foreach($spisok as $n=>$sp)
		{
		$send[$n]->viewer_id=$sp[0];
		$send[$n]->first_name=iconv("WINDOWS-1251","UTF-8",$sp[1]);
		$send[$n]->last_name=iconv("WINDOWS-1251","UTF-8",$sp[2]);
		$send[$n]->photo=$sp[3];
		$send[$n]->dtime_add=iconv("WINDOWS-1251","UTF-8","Добавлен".($sp[4]==1?'a':'')." ".FullData($sp[5]));
		$send[$n]->admin=$sp[6];
		}
echo json_encode($send);
?>



