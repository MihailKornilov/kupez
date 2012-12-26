<?php
require_once('../../../include/AjaxHeader.php');

$spisok=$VK->QueryObjectArray("select * from gazeta_nomer where day_begin like '".$_GET['year']."-%' order by general_nomer");
$send[0]->count=count($spisok);
if($send[0]->count>0)
	{
	$curr=time();
	foreach($spisok as $n=>$sp)
		{
		$send[$n]->id=$sp->id;
		$send[$n]->grey=($curr>strtotime($sp->day_print)+86400?'grey':'');
		$send[$n]->general_nomer=$sp->general_nomer;
		$send[$n]->week_nomer=$sp->week_nomer;
		$send[$n]->day_txt=iconv("WINDOWS-1251","UTF-8",$sp->day_txt);

		$send[$n]->day_begin_val=$sp->day_begin;
		$send[$n]->day_begin=iconv("WINDOWS-1251","UTF-8",FullData($sp->day_begin,1));
		$send[$n]->day_end_val=$sp->day_end;
		$send[$n]->day_end=iconv("WINDOWS-1251","UTF-8",FullData($sp->day_end,1));
	
		$send[$n]->day_print=iconv("WINDOWS-1251","UTF-8",FullData($sp->day_print,1));
		$send[$n]->day_print_val=$sp->day_print;
		$send[$n]->day_public_val=$sp->day_public;
		$send[$n]->day_public=iconv("WINDOWS-1251","UTF-8",FullData($sp->day_public,1));
		$send[$n]->zayav_count=$VK->QRow("select count(id) from gazeta_nomer_pub where general_nomer=".$sp->general_nomer);
		}
	}
else
	{
	$txt="Ќомера газет, которые будут выходить в ".$_GET['year']." году, не определены.";
	$txt.="<BR><BR><A href='javascript:' onclick=gazNomerSpisokCreate(".$_GET['year'].",this);><B>—оздать список</B>...</A>";
	$send[0]->txt=iconv("WINDOWS-1251","UTF-8",$txt);
	}
echo json_encode($send);
?>



