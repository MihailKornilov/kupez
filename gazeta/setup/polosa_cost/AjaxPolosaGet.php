<?php
require_once('../../../include/AjaxHeader.php');

$spisok=$VK->QueryObjectArray("select * from setup_polosa_cost order by sort");
if(count($spisok)>0)
	foreach($spisok as $n=>$sp)
		{
		$send[$n]->id=$sp->id;
		$send[$n]->name=iconv("WINDOWS-1251","UTF-8",$sp->name);
		$send[$n]->cena=round($sp->cena,2);
		}
echo json_encode($send);
?>



