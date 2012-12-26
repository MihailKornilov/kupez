<?php
function zayavEnd($count)
	{
	$ost=$count%10;
	$ost10=$count/10%10;

	if($ost10==1) return 'ок';
	else
		switch($ost)
			{
			case '1': return 'ка';
			case '2': return 'ки';
			case '3': return 'ки';
			case '4': return 'ки';
			default: return 'ок';
			}
	}

require_once('../../include/AjaxHeader.php');

if($_GET['fast'])
	{
	$fast=iconv("UTF-8", "WINDOWS-1251",$_GET['fast']);
	$fast=preg_replace( '/\s+/','',$fast);
	if(preg_match("|^[\d]+$|",$fast)) $orid=" or id=".$fast;
	$find="where
replace(txt,' ','') LIKE '%".$fast."%' or 
replace(telefon,' ','') LIKE '%".$fast."%' or 
replace(adres,' ','') LIKE '%".$fast."%' or 

txt LIKE '%".$fast."%' or
telefon LIKE '%".$fast."%' or
adres LIKE '%".$fast."%'".$orid;
	}
else
	{
	$find="where id";
	if($_GET['category']>0) $find.=" and category=".$_GET['category'];
	if($_GET['client']) $find.=" and client_id=".$_GET['client'];
	if($_GET['gazeta_nomer']>0)
		{
		$spisok=$VK->QueryRowArray("select distinct(zayav_id) from gazeta_nomer_pub where general_nomer=".$_GET['gazeta_nomer']);
		if(count($spisok)>0)
			foreach($spisok as $sp)
				$ids.=",".$sp[0];
		$find.=" and id in (0".$ids.")";
		}
	}

$zCount=$VK->QRow("select count(id) from zayav ".$find);
if($_GET['category']>0 or $_GET['gazeta_nomer']>0 or $_GET['fast']) $fCount="Найден".($zCount%10==1?'а':'о')." "; else $fCount="Всего ";
$send[0]->result=iconv("WINDOWS-1251","UTF-8",$fCount.$zCount." заяв".zayavEnd($zCount));
$send[0]->page=0;
$send[0]->count=$zCount;

$spisok=$VK->QueryObjectArray("select * from zayav ".$find." order by id ".($_GET['desc']==0?'desc':'')." limit ".(($_GET['page']-1)*20).",20");
if(count($spisok)>0)
	{
	$rubrika=$VK->QueryPtPArray("select id,name from setup_rubrika");
	$podrubrika=$VK->QueryPtPArray("select id,name from setup_pod_rubrika");
	$obDop=$VK->QueryPtPArray("select id,name from setup_ob_dop");
	foreach($spisok as $n=>$sp)
		{
		$n++;
		if($sp->client_id>0) $fio=$VK->QRow("select fio from client where id=".$sp->client_id);
		$send[$n]->ob_dop='';
		if($sp->category==1)
			{
			$id=$VK->QRow("select ob_dop_id from gazeta_nomer_pub where zayav_id=".$sp->id." order by general_nomer limit 1");
			$send[$n]->ob_dop=iconv("WINDOWS-1251","UTF-8",$obDop[$id]);
			}
		$send[$n]->id=$sp->id;
		$send[$n]->cat_id=$sp->category;
		$send[$n]->cat_name=iconv("WINDOWS-1251","UTF-8",$zayavCategory[$sp->category]);
		if($_GET['fast']) {
			$send[$n]->txt=iconv("WINDOWS-1251","UTF-8",preg_replace("/(".$fast.")/i","<TT>\\1</TT>",$sp->txt));
			$send[$n]->telefon=iconv("WINDOWS-1251","UTF-8",preg_replace("/(".$fast.")/i","<TT>\\1</TT>",$sp->telefon));
			$send[$n]->adres=iconv("WINDOWS-1251","UTF-8",preg_replace("/(".$fast.")/i","<TT>\\1</TT>",$sp->adres));
			}
		else $send[$n]->txt=iconv("WINDOWS-1251","UTF-8",$sp->txt);
		$send[$n]->rub=iconv("WINDOWS-1251","UTF-8",$rubrika[$sp->rubrika]);
		$send[$n]->podrub=iconv("WINDOWS-1251","UTF-8",$podrubrika[$sp->podrubrika]);
		$send[$n]->client_id=$sp->client_id;
		$send[$n]->fio=iconv("WINDOWS-1251","UTF-8",$fio);
		$send[$n]->size_x=round($sp->size_x,1);
		$send[$n]->size_y=round($sp->size_y,1);
		$send[$n]->kv_sm=round($sp->size_x*$sp->size_y,2);
		$send[$n]->summa=round($sp->summa,2);
		$send[$n]->summa_manual=$sp->summa_manual;
		$send[$n]->file=$sp->file;
		$send[$n]->dtime=iconv("WINDOWS-1251","UTF-8",FullData($sp->dtime_add));
		}
	if(count($spisok)==20)
		{
		$count=$VK->QNumRows("select id from zayav ".$find." limit ".($_GET['page']*20).",20");
		$_GET['page']++;
		if($count>0) $send[0]->page=$_GET['page'];
		}
	}

echo json_encode($send);
?>



