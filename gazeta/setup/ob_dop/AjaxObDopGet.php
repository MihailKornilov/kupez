<?php
require_once('../../../include/AjaxHeader.php');

$spisok=$VK->QueryObjectArray("select * from setup_ob_dop order by id");
if(count($spisok)>0)
	foreach($spisok as $n=>$sp)
		{
		$send[$n]->id=$sp->id;
		$send[$n]->name=iconv("WINDOWS-1251","UTF-8",$sp->name);
		$send[$n]->cena=$sp->cena;
		}
echo json_encode($send);
?>



