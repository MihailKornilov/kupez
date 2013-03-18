<?php
function obEnd($count)
	{
	$ost=$count%10;
	$ost10=$count/10%10;

	if($ost10==1) return '�';
	else
		switch($ost)
			{
			case '1': return '�';
			case '2': return '�';
			case '3': return '�';
			case '4': return '�';
			default: return '�';
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
	$send->result=utf8("�������� ".$obCount." ���������".obEnd($obCount));

	$arrDop=$VK->QueryPtPArray("select id,name from setup_ob_dop"); // ������ ������������ �������������� ����������
	$obDop=$VK->QueryPtPArray("select zayav_id,ob_dop_id from gazeta_nomer_pub where zayav_id in (0".$ids.") and general_nomer=".$_GET['gn']);

	$spisokRub=$VK->QueryObjectArray("select id,name from setup_rubrika order by sort");
	foreach($spisokRub as $spRub)
		{
		$cRub=$VK->QRow("select count(id) from zayav where category=1 and rubrika=".$spRub->id.$find); // �������, ���� �� ���������� � �������
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
							$contact=($sp->telefon?"<B>���.: ".$sp->telefon."</B>":'');
							if($sp->adres) $contact.=($sp->telefon?", ":'')."<B>�����: ".$sp->adres."</B>";
							$html.="<DIV class=obUnit><EM>&bull;</EM><A href='".$URL."&my_page=zayavView&id=".$sp->id."'>".$sp->txt."</A> ".$contact.dop($sp->id)."</DIV>";
							}
						}
					}
				// ������� ������ �������, � ������� ���������� = 0
				$spisok=$VK->QueryObjectArray("select * from zayav where category=1 and rubrika=".$spRub->id.$find." and podrubrika=0 order by txt");
				if(count($spisok)>0)
					foreach($spisok as $sp)
						{
						$contact=($sp->telefon?"<B>���.: ".$sp->telefon."</B>":'');
						if($sp->adres) $contact.=($sp->telefon?", ":'')."<B>�����: ".$sp->adres."</B>";
						$html.="<DIV class=obUnit><EM>&bull;</EM><A href='".$URL."&my_page=zayavView&id=".$sp->id."'>".$sp->txt."</A> ".$contact.dop($sp->id)."</DIV>";
						}
				}
		}
	}
else
	{
	$send->result=utf8("���������� �� �������");
	$html="<DIV class=noob>���������� ���.</DIV>";
	}

$send->html=utf8($html);

echo json_encode($send);
?>



