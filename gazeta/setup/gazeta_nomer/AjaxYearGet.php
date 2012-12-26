<?php
require_once('../../../include/AjaxHeader.php');

$begin=$VK->QRow("select day_begin from gazeta_nomer order by day_begin limit 1");

if(!$begin)
	{
	$send->begin=strftime("%Y",time());
	$send->end=$send->begin;
	}
else
	{
	$send->begin=substr($begin,0,4);
	$end=$VK->QRow("select day_end from gazeta_nomer order by day_end desc limit 1");
	if($end)
		{
		$send->end=substr($end,0,4);
		if($send->end==$send->begin) $send->end=$send->begin+1;
		}
	else $send->end=$send->begin+1;
	}

echo json_encode($send);
?>
