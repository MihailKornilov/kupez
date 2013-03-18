<?php
require_once('../../include/AjaxHeader.php');

$spisok=$VK->QueryObjectArray("select id,name from setup_rubrika order by sort");
if(count($spisok)>0)
	foreach($spisok as $n=>$sp)
		{
		$send[$n]->uid=$sp->id;
		$send[$n]->title=utf8($sp->name);
		}

echo json_encode($send);
?>



