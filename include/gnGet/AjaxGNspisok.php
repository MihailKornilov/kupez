<?php
require_once('../AjaxHeader.php');

if($_GET['zayav_id']>0)
	{
	$gnPub=$VK->QueryPtPArray("select general_nomer,1 from gazeta_nomer_pub where zayav_id=".$_GET['zayav_id']);
	switch($_GET['category'])
		{
		case 1: $gnDop=$VK->QueryPtPArray("select general_nomer,ob_dop_id from gazeta_nomer_pub where zayav_id=".$_GET['zayav_id']); break;
		case 2: $gnDop=$VK->QueryPtPArray("select general_nomer,polosa_id from gazeta_nomer_pub where zayav_id=".$_GET['zayav_id']); break;
		}
	}

$send['gn_prev']='';
$spisok=$VK->QueryRowArray("select
gazeta_nomer.week_nomer,
gazeta_nomer.general_nomer,
gazeta_nomer.day_public,
gazeta_nomer_pub.summa,
gazeta_nomer_pub.ob_dop_id,
gazeta_nomer_pub.polosa_id
from gazeta_nomer,gazeta_nomer_pub where gazeta_nomer.general_nomer=gazeta_nomer_pub.general_nomer and  gazeta_nomer_pub.zayav_id=".$_GET['zayav_id']." and day_print<'".strftime('%Y-%m-%d',time())."'");
$send['pub'][0]->count=count($spisok);
if($send['pub'][0]->count>0)
	{
	switch($_GET['category'])
		{
		case 1: $gnDopName=$VK->QueryPtPArray("select id,name from setup_ob_dop"); $spn=4; break;
		case 2: $gnDopName=$VK->QueryPtPArray("select id,name from setup_polosa_cost"); $spn=5; break;
		}
	foreach($spisok as $n=>$sp)
		{
		$send['pub'][$n]->week_nomer=$sp[0];
		$send['pub'][$n]->general_nomer=$sp[1];
		$send['pub'][$n]->day_public=iconv("WINDOWS-1251","UTF-8",FullData($sp[2],0,1));
		$send['pub'][$n]->dop_name=iconv("WINDOWS-1251","UTF-8",$sp[$spn]>0?$gnDopName[$sp[$spn]]:'');
		$send['pub'][$n]->summa=round($sp[3],2);
		$send['pub'][$n]->viewed=1;
		$send['gn_prev'].=$sp[1].":".$sp[$spn].":".round($sp[3],2).",";
		}
	$send['gn_prev']=substr($send['gn_prev'],0,strlen($send['gn_prev'])-1);
	}


$spisok=$VK->QueryObjectArray("select * from gazeta_nomer where day_print>='".strftime('%Y-%m-%d',time())."' limit ".$_GET['gn_count']);
if(count($spisok)>0)
	foreach($spisok as $n=>$sp)
		{
		$send['gaz'][$n]->week_nomer=$sp->week_nomer;
		$send['gaz'][$n]->general_nomer=$sp->general_nomer;
		$send['gaz'][$n]->day_public=iconv("WINDOWS-1251","UTF-8",FullData($sp->day_public,0,1));
		$send['gaz'][$n]->sel=($gnPub[$sp->general_nomer]?1:0);
		$send['gaz'][$n]->dop=($gnDop[$sp->general_nomer]?$gnDop[$sp->general_nomer]:0);
		$send['gaz'][$n]->viewed=0;
		$n++;
		}

switch($_GET['category'])
	{
	case 1:
		$title0="Доп. параметр не указан";
		$spisokDop=$VK->QueryObjectArray("select * from setup_ob_dop order by id");
		break;
	case 2:
		$title0="Полоса не указана";
		$spisokDop=$VK->QueryObjectArray("select * from setup_polosa_cost order by sort");
		break;
	}

$send['dop'][0]->uid=0;
$send['dop'][0]->title=iconv("WINDOWS-1251","UTF-8",$title0);
$send['dop_cena'][0]=0;
$send['dop'][0]->count=count($spisokDop);
if($send['dop'][0]->count>0)
	foreach($spisokDop as $n=>$sp)
		{
		$send['dop'][$n+1]->uid=$sp->id;
		$send['dop'][$n+1]->title=iconv("WINDOWS-1251","UTF-8",$sp->name);
		$send['dop_cena'][$sp->id]=round($sp->cena,2);
		}

$send['skidka'][0]=0;
$spisok=$VK->QueryObjectArray("select * from setup_skidka order by id");
if(count($spisok)>0)
	foreach($spisok as $n=>$sp)
		$send['skidka'][$sp->id]=$sp->razmer;

echo json_encode($send);
?>



