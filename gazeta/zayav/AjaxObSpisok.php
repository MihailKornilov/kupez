<?php
function obEnd($count)
	{
	$ost=$count%10;
	$ost10=$count/10%10;

	if($ost10==1) return 'й';
	else
		switch($ost)
			{
			case '1': return 'е';
			case '2': return 'я';
			case '3': return 'я';
			case '4': return 'я';
			default: return 'й';
			}
	}

require_once('../../include/AjaxHeader.php');

function dop($zayav_id)
	{
	global $obDop,$arrDop;
	$d=$obDop[$zayav_id];
	if($d>0)
		return " <SPAN class=dop>(".$arrDop[$d].")</SPAN>";
	else return '';
	}

$spisok=$VK->QueryRowArray("select distinct(zayav_id) from gazeta_nomer_pub where general_nomer=".$_GET['gn']);
$send->count=count($spisok);
if($send->count>0)
	{
	foreach($spisok as $sp)
		$ids.=",".$sp[0];
	$find.=" and id in (0".$ids.")";

	$obCount=$VK->QRow("select count(id) from zayav where category=1".$find);
	$send->result=iconv("WINDOWS-1251","UTF-8","Показано ".$obCount." объявлени".obEnd($obCount));

	$arrDop=$VK->QueryPtPArray("select id,name from setup_ob_dop"); // список наименований дополнительных параметров
	$obDop=$VK->QueryPtPArray("select zayav_id,ob_dop_id from gazeta_nomer_pub where zayav_id in (0".$ids.") and general_nomer=".$_GET['gn']);

	$spisokRub=$VK->QueryObjectArray("select id,name from setup_rubrika order by sort");
	foreach($spisokRub as $spRub)
		{
		$cRub=$VK->QRow("select count(id) from zayav where category=1 and rubrika=".$spRub->id.$find); // смотрим, есть ли объявления в рубрике
		if($cRub>0)
			{
			$html.="<H4>".$spRub->name."</H4>";
			$podRub=$VK->QueryObjectArray("select id,name from setup_pod_rubrika where rubrika_id=".$spRub->id." order by sort");
			if(count($podRub))
				foreach($podRub as $pr)
					{
					$spisok=$VK->QueryObjectArray("select * from zayav where category=1 and podrubrika=".$pr->id.$find." order by txt");
					if(count($spisok)>0)
						{
						$html.="<H5>".$pr->name."</H5>";
						foreach($spisok as $sp)
							{
							$contact=($sp->telefon?"<B>Тел.: ".$sp->telefon."</B>":'');
							if($sp->adres) $contact.=($sp->telefon?", ":'')."<B>Адрес: ".$sp->adres."</B>";
							$html.="<DIV class=obUnit><EM>&bull;</EM><A href='".$URL."&my_page=zayavView&id=".$sp->id."'>".$sp->txt."</A> ".$contact.dop($sp->id)."</DIV>";
							}
						}
					}
				// выводим список рубрики, у которых подрубрика = 0
				$spisok=$VK->QueryObjectArray("select * from zayav where category=1 and rubrika=".$spRub->id.$find." and podrubrika=0 order by txt");
				if(count($spisok)>0)
					foreach($spisok as $sp)
						{
						$contact=($sp->telefon?"<B>Тел.: ".$sp->telefon."</B>":'');
						if($sp->adres) $contact.=($sp->telefon?", ":'')."<B>Адрес: ".$sp->adres."</B>";
						$html.="<DIV class=obUnit><EM>&bull;</EM><A href='".$URL."&my_page=zayavView&id=".$sp->id."'>".$sp->txt."</A> ".$contact.dop($sp->id)."</DIV>";
						}
				}
		}
	}
else
	{
	$send->result=iconv("WINDOWS-1251","UTF-8","Объявлений не найдено");
	$html="<DIV class=noob>Объявлений нет.</DIV>";
	}

$send->html=iconv("WINDOWS-1251","UTF-8",$html);

echo json_encode($send);
?>



