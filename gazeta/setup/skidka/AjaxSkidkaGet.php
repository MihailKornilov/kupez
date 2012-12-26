<?php
require_once('../../../include/AjaxHeader.php');

$spisok=$VK->QueryObjectArray("select * from setup_skidka order by razmer");
if(count($spisok)>0)
	foreach($spisok as $n=>$sp)
		{
		$send[$n]->id=$sp->id;
		$send[$n]->razmer=$sp->razmer;
		$send[$n]->about=iconv("WINDOWS-1251","UTF-8",$sp->about);
		}
echo json_encode($send);
?>



