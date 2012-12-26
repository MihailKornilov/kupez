<?php
function oplataEnd($count)
	{
	$ost=$count%10;
	$ost10=$count/10%10;

	if($ost10==1) return 'ей';
	else
		switch($ost)
			{
			case '1': return '';
			case '2': return 'а';
			case '3': return 'а';
			case '4': return 'а';
			default: return 'ей';
			}
	}

require_once('../../include/AjaxHeader.php');

$spisok=$VK->QueryObjectArray("select * from vk_user");
foreach($spisok as $sp)
	$vkUs[$sp->viewer_id]="<A href='http://vk.com/id".$sp->viewer_id."' target='_blank'>".$sp->last_name." ".$sp->first_name."</A>";

$lineSize=1000; // количество выводимых строк на одну страницу

$find="where status=1";
if($_GET['client']) $find.=" and client_id=".$_GET['client'];

$send[0]->page=0;
$send[0]->count=$VK->QRow("select count(id) from oplata ".$find);
if($_GET['category']>0) $fCount="ѕоказан".($send[0]->count%10==1?'':'о')." "; else $fCount="¬сего ";
$send[0]->result=iconv("WINDOWS-1251","UTF-8",$fCount.$send[0]->count." платеж".oplataEnd($send[0]->count));

$spisok=$VK->QueryObjectArray("select * from oplata ".$find." order by id desc limit ".(($_GET['page']-1)*$lineSize).",".$lineSize);
if(count($spisok)>0)
	{
	foreach($spisok as $n=>$sp)
		{
		$send[$n]->id=$sp->id;
		$send[$n]->summa=round($sp->summa,2);
		$send[$n]->prim=iconv("WINDOWS-1251","UTF-8",$sp->prim);
		$send[$n]->dtime=iconv("WINDOWS-1251","UTF-8",FullDataTime($sp->dtime_add,1));
		$send[$n]->viewer_id=iconv("WINDOWS-1251","UTF-8",$vkUs[$sp->viewer_id_add]);
		}
	if(count($spisok)==$lineSize)
		{
		$count=$VK->QNumRows("select id from oplata ".$find." limit ".($_GET['page']*$lineSize).",".$lineSize);
		$_GET['page']++;
		if($count>0) $send[0]->page=$_GET['page'];
		}
	}

echo json_encode($send);
?>



