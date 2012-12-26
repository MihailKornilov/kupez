<?php
function zayavEnd($count)
	{
	$ost=$count%10;
	$ost10=$count/10%10;

	if($ost10==1) return 'й';
	else
		switch($ost)
			{
			case '1': return 'е';
			case '2': return '€';
			case '3': return '€';
			case '4': return '€';
			default: return 'й';
			}
	}

function activeEnd($count)
	{
	$ost=$count%10;
	$ost10=$count/10%10;

	if($ost10==1) return 'ых';
	else
		switch($ost)
			{
			case '1': return 'ое';
			default: return 'ых';
			}
	}

function dayEnd($count)
	{
	$ost=$count%10;
	$ost10=$count/10%10;

	if($ost10==1) return ' дней';
	else
		switch($ost)
			{
			case '1': return ' день';
			case '2': return ' дн€';
			case '3': return ' дн€';
			case '4': return ' дн€';
			default: return ' дней';
			}
	}

function ostEnd($count)
	{
	$ost=$count%10;
	$ost10=$count/10%10;
	if($ost10==1) return 'ось ';
	else
		if($ost==1) return 'с€ ';
		else return 'ось ';
	}

require_once('../../include/AjaxHeader.php');

$find="where status=1 and category=1 and vk_srok>0 and viewer_id_add=".$_GET['viewer_id'];
if($VK->QRow("select count(id) from zayav ".$find)==0)
	{
	$send[0]->result=iconv("WINDOWS-1251","UTF-8","¬ы ещЄ не размещали объ€влений");
	$send[0]->count=0;
	}
else
	{
	if($_GET['menu']==1)
		{
		$find.=" and vk_day_active>='".strftime("%Y-%m-%d",time())."'";
		$active=" активн";
		}
	if($_GET['menu']==2)
		{
		$find.=" and vk_day_active<'".strftime("%Y-%m-%d",time())."'";
		$archive=" в архиве";
		}
	$send[0]->count=$VK->QRow("select count(id) from zayav ".$find);
	if($send[0]->count>0)
		$send[0]->result=iconv("WINDOWS-1251","UTF-8","” ¬ас ".$send[0]->count.$active.($active?activeEnd($send[0]->count):'')." объ€влени".zayavEnd($send[0]->count).$archive);
	else $send[0]->result=iconv("WINDOWS-1251","UTF-8","ќбъ€влений не найдено");
	}
$send[0]->page=0;
$send[0]->viewer_id=$_GET['viewer_id'];

$vkUs=$VK->QueryObjectOne("select first_name,last_name from vk_user where viewer_id=".$_GET['viewer_id']." limit 1");

$CP=50;
$spisok=$VK->QueryObjectArray("select * from zayav ".$find." order by id desc limit ".(($_GET['page']-1)*$CP).",".$CP);
if(count($spisok)>0)
	{
	$rubrika=$VK->QueryPtPArray("select id,name from setup_rubrika");
	$podrubrika=$VK->QueryPtPArray("select id,name from setup_pod_rubrika");
	foreach($spisok as $n=>$sp)
		{
		$send[$n]->id=$sp->id;
		$send[$n]->txt=iconv("WINDOWS-1251","UTF-8",substr($sp->txt,0,70).(strlen($sp->txt)>95?'...':''));
		$send[$n]->rub=iconv("WINDOWS-1251","UTF-8",$rubrika[$sp->rubrika]);
		$send[$n]->podrub=iconv("WINDOWS-1251","UTF-8",$podrubrika[$sp->podrubrika]);
		$send[$n]->telefon=iconv("WINDOWS-1251","UTF-8",$sp->telefon);
		$send[$n]->adres=iconv("WINDOWS-1251","UTF-8",$sp->adres);
		$send[$n]->file=$sp->file;
		$send[$n]->dtime=iconv("WINDOWS-1251","UTF-8",FullDataTime($sp->dtime_add));
		if($sp->vk_viewer_id_show==1) $send[$n]->vk_name=iconv("WINDOWS-1251","UTF-8",$vkUs->first_name." ".$vkUs->last_name);
		$srok=strtotime($sp->vk_day_active)-time()+86400;
		if($srok>0)
			{
			$send[$n]->active=1;
			$day=ceil($srok/86400);
			$send[$n]->day_last=iconv("WINDOWS-1251","UTF-8","ќстал".ostEnd($day).$day.dayEnd($day));
			}	
		}
	if(count($spisok)==$CP)
		{
		$count=$VK->QNumRows("select id from zayav ".$find." limit ".($_GET['page']*$CP).",".$CP);
		$_GET['page']++;
		if($count>0) $send[0]->page=$_GET['page'];
		}
	}

echo json_encode($send);
?>



