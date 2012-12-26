<?php
require_once('../../include/AjaxHeader.php');

$spisok=$VK->QueryObjectArray("select * from gazeta_nomer where day_public like '".$_GET['year']."%' order by general_nomer");
if(count($spisok)>0)
	{
	$curr=time();
	foreach($spisok as $n=>$sp)
		{
		$d=explode("-",$sp->day_public);
		$data=abs($d[2])." ".$MonthCut[$d[1]];

		$send[$n]->uid=$sp->general_nomer;
		$send[$n]->title=iconv("WINDOWS-1251","UTF-8",$sp->week_nomer." (".$sp->general_nomer.") выход ".$data);
		$send[$n]->content=iconv("WINDOWS-1251","UTF-8","<B".($curr>strtotime($sp->day_print)+86400?' class=grey':'').">".$sp->week_nomer."</B> <SPAN".($curr>strtotime($sp->day_print)+86400?' class=grey':'').">(".$sp->general_nomer.")</SPAN><TT>выход ".$data."</TT>");
		}
	}

echo json_encode($send);
?>



