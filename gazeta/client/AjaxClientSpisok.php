<?php
function clEnd($count)
	{
	$ost=$count%10;
	$ost10=$count/10%10;

	if($ost10==1) return '��';
	else
		switch($ost)
			{
			case '1': return '';
			case '2': return '�';
			case '3': return '�';
			case '4': return '�';
			default: return '��';
			}
	}

setlocale(LC_ALL, 'ru_RU.CP1251', 'rus_RUS.CP1251', 'Russian_Russia.1251');
require_once('../../include/AjaxHeader.php');

$find="where id";
if($_GET['input'])
	{
	$input=iconv("UTF-8", "WINDOWS-1251",$_GET['input']);
	$find.=" and (org_name LIKE '%".$input."%' or fio LIKE '%".$input."%' or telefon LIKE '%".$input."%' or adres LIKE '%".$input."%')";
	}
if($_GET['dolg']==1)
	{
	$find.=" and balans<0";
	$dolg=$VK->QRow("select sum(balans) from client ".$find);
	$dolg*=-1;
	}
if($_GET['person']>0) $find.=" and person=".$_GET['person'];

$cCount=$VK->QRow("select count(id) from client ".$find);
if($_GET['input'] or $_GET['dolg']==1) $fCount="������".($cCount%10==1?'':'�')." "; else $fCount="� ���� ";
$send[0]->result=iconv("WINDOWS-1251","UTF-8",$fCount.$cCount." ������".clEnd($cCount).($_GET['dolg']==1?".<EM>(����� ����� ����� = ".$dolg." ���.)</EM>":''));
$send[0]->page=0;
$send[0]->count=$cCount;

$spisok=$VK->QueryObjectArray("select * from client ".$find." order by id desc limit ".(($_GET['page']-1)*20).",20");
if(count($spisok)>0)
	{
	foreach($spisok as $n=>$sp)
		{
		$n++;
		if($_GET['input'])
			{
			$sp->org_name=preg_replace("/(".$input.")/i","<EM>\\1</EM>",$sp->org_name);
			$sp->fio=preg_replace("/(".$input.")/i","<EM>\\1</EM>",$sp->fio);
			$sp->telefon=preg_replace("/(".$input.")/i","<EM>\\1</EM>",$sp->telefon);
			$sp->adres=preg_replace("/(".$input.")/i","<EM>\\1</EM>",$sp->adres);
			$send[$n]->adres=iconv("WINDOWS-1251","UTF-8",$sp->adres);
			}
		$send[$n]->id=$sp->id;
		$send[$n]->org_name=iconv("WINDOWS-1251","UTF-8",$sp->org_name);
		$send[$n]->fio=iconv("WINDOWS-1251","UTF-8",$sp->fio);
		$send[$n]->telefon=iconv("WINDOWS-1251","UTF-8",$sp->telefon);
		$send[$n]->zayav_count=$sp->zayav_count;
		$send[$n]->balans=$sp->balans;
		}
	if(count($spisok)==20)
		{
		$count=$VK->QNumRows("select id from client ".$find." order by id desc limit ".($_GET['page']*20).",20");
		$_GET['page']++;
		if($count>0) $send[0]->page=$_GET['page'];
		}
	}

echo json_encode($send);
?>



