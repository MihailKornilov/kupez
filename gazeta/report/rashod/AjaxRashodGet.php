<?php
require_once('../../../include/AjaxHeader.php');

$spisok=$VK->QueryObjectArray("select * from vk_user");
foreach($spisok as $sp)
	$vkUs[$sp->viewer_id]="<A href='http://vk.com/id".$sp->viewer_id."' target='_blank'>".$sp->last_name." ".$sp->first_name."</A>";


$spisok=$VK->QueryObjectArray("select * from rashod order by id");
if(count($spisok)>0)
	foreach($spisok as $n=>$sp)
		{
		$send[$n]->id=$sp->id;
		$send[$n]->name=iconv("WINDOWS-1251","UTF-8",$sp->name);
		$send[$n]->summa=round($sp->summa);
		$send[$n]->dtime=iconv("WINDOWS-1251","UTF-8",FullData($sp->dtime_add));
		$send[$n]->viewer_id=iconv("WINDOWS-1251","UTF-8",$vkUs[$sp->viewer_id_add]);
		}
echo json_encode($send);
?>



