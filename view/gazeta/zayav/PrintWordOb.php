<?php
require_once('../../include/clsMsDocGenerator.php');
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
if(count($spisok)>0)
	{
	$obDop=$VK->QueryPtPArray("select id,name from setup_ob_dop");

	$ids='';
	foreach($spisok as $sp)
		$ids.=",".$sp[0];
	$find=" and id in (0".$ids.")";

	$arrDop=$VK->QueryPtPArray("select id,name from setup_ob_dop"); // список наименований дополнительных параметров
	$obDop=$VK->QueryPtPArray("select zayav_id,ob_dop_id from gazeta_nomer_pub where zayav_id in (0".$ids.") and general_nomer=".$_GET['gn']);

	$PRINT='';
	$spisokRub=$VK->QueryObjectArray("select id,name from setup_rubrika order by sort");
	foreach($spisokRub as $spRub)
		{
		$cRub=$VK->QRow("select count(id) from zayav where category=1 and rubrika=".$spRub->id.$find); // смотрим, есть ли объявления в рубрике
		if($cRub>0)
			{
			$PRINT.="<DIV class=rub>".$spRub->name."</DIV>";
			$podRub=$VK->QueryObjectArray("select id,name from setup_pod_rubrika where rubrika_id=".$spRub->id." order by sort");
			if(count($podRub))
				foreach($podRub as $pr)
					{
					$spisok=$VK->QueryObjectArray("select * from zayav where category=1 and podrubrika=".$pr->id.$find." order by txt");
					if(count($spisok)>0)
						{
						$PRINT.="<DIV class=podrub>".$pr->name."</DIV>";
						foreach($spisok as $sp)
							{
							$contact=($sp->telefon?"<B>Тел.: ".$sp->telefon."</B>":'');
							if($sp->adres) $contact.=($sp->telefon?", ":'')."<B>Адрес: ".$sp->adres."</B>";
							$PRINT.="<DIV class=obUnit>".$sp->txt." ".$contact." ".dop($sp->id)."</DIV>";
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
						$PRINT.="<DIV class=obUnit>".$sp->txt." ".$contact." ".dop($sp->id)."</DIV>";
						}
				}
		}
	}
else $PRINT="Нет объявлений для номера ".$_GET['gn'];

$doc = new clsMsDocGenerator($pageOrientation='PORTRAIT',    $pageType='A4',    $cssFile='PrintWordOb.css',    $topMargin=0.5,    $rightMargin=1.0,    $bottomMargin=0.5,    $leftMargin=1.0);
$doc->addParagraph($PRINT);
$doc->output("objav_nomer".$_GET['gn']);
?>