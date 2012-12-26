<?php
require_once('../../../include/AjaxHeader.php');

$spisok=$VK->QueryObjectArray("select id,name from setup_pod_rubrika where rubrika_id=".$_GET['rubrika_id']." order by sort");
if(count($spisok)>0)
	foreach($spisok as $n=>$sp)
		{
		$send[$n]->id=$sp->id;
		$send[$n]->name=iconv("WINDOWS-1251","UTF-8",$sp->name);
		}
echo json_encode($send);
?>



