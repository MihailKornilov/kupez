<?php
require_once('../../../include/AjaxHeader.php');

$spisok=$VK->QueryObjectArray("select id,name from setup_person where id!=".$_GET['del']." order by sort");
if(count($spisok)>0)
	foreach($spisok as $n=>$sp)
		{
		$send[$n]->uid=$sp->id;
		$send[$n]->title=iconv("WINDOWS-1251","UTF-8",$sp->name);
		}
echo json_encode($send);
?>



