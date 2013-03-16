<?php
function obEnd($count)
	{
	$ost=$count%10;
	$ost10=$count/10%10;

	if($ost10==1) return 'ι';
	else
		switch($ost)
			{
			case '1': return 'ε';
			case '2': return 'ÿ';
			case '3': return 'ÿ';
			case '4': return 'ÿ';
			default: return 'ι';
			}
	}

require_once('../../include/AjaxHeader.php');

$find="where category=1 and whence='vk'";
if($_GET['user']>0) $find.=" and viewer_id_add=".$_GET['user'];

$send[0]->count=$VK->QRow("select count(id) from zayav ".$find);
$send[0]->result=iconv("WINDOWS-1251","UTF-8",$fCount.$send[0]->count." ξαϊÿβλενθ".obEnd($send[0]->count));
$send[0]->page=0;

$CP=50;
$spisok=$VK->QueryObjectArray("select * from zayav ".$find." order by id desc limit ".(($_GET['page']-1)*$CP).",".$CP);
if(count($spisok)>0)
	{
	$rubrika=$VK->QueryPtPArray("select id,name from setup_rubrika");
	$podrubrika=$VK->QueryPtPArray("select id,name from setup_pod_rubrika");
	if($_GET['user']>0)
		{
		$vkUs=$VK->QueryObjectOne("select * from vk_user where viewer_id=".$_GET['user']." limit 1");
		$send[0]->vk_name=iconv("WINDOWS-1251","UTF-8",$vkUs->first_name." ".$vkUs->last_name);
		$send[0]->photo=$vkUs->photo;
		$send[0]->ob_count=iconv("WINDOWS-1251","UTF-8",$vkUs->ob_count." ξαϊÿβλενθ".obEnd($vkUs->ob_count));
		}
	foreach($spisok as $n=>$sp)
		{
		$send[$n]->id=$sp->id;
		$send[$n]->txt=iconv("WINDOWS-1251","UTF-8",$sp->txt);
		$send[$n]->rub=iconv("WINDOWS-1251","UTF-8",$rubrika[$sp->rubrika]);
		$send[$n]->podrub=iconv("WINDOWS-1251","UTF-8",$podrubrika[$sp->podrubrika]);
		$send[$n]->telefon=iconv("WINDOWS-1251","UTF-8",$sp->telefon);
		$send[$n]->adres=iconv("WINDOWS-1251","UTF-8",$sp->adres);
		$send[$n]->file=$sp->file;
		$send[$n]->dtime=iconv("WINDOWS-1251","UTF-8",FullDataTime($sp->dtime_add));
		$send[$n]->viewer_id=$sp->viewer_id_add;
		$send[$n]->viewer_id_show=$sp->viewer_id_show;
		if($_GET['user']==0) $vkUs=$VK->QueryObjectOne("select first_name,last_name from vk_user where viewer_id=".$sp->viewer_id_add." limit 1");
		$send[$n]->vk_name=iconv("WINDOWS-1251","UTF-8",$vkUs->first_name." ".$vkUs->last_name);
//		$send[$n]->dop=$sp->dop;
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



