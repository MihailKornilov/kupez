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

setlocale(LC_ALL, 'ru_RU.CP1251', 'rus_RUS.CP1251', 'Russian_Russia.1251');
require_once('../../include/AjaxHeader.php');

$find="where status=1 and category=1 and active_day>='".strftime("%Y-%m-%d",time())."'";
if($_GET['rub']>0) $find.=" and rubrika=".$_GET['rub'];
if($_GET['podrub']>0) $find.=" and podrubrika=".$_GET['podrub'];
if($_GET['input'])
	{
	$input=iconv("UTF-8", "WINDOWS-1251",$_GET['input']);
	$find.=" and txt LIKE '%".$input."%'";
	}

$obCount=$VK->QRow("select count(id) from zayav ".$find);
$send->result=iconv("WINDOWS-1251","UTF-8","Показано ".$obCount." объявлени".obEnd($obCount));

$spisokRub=$VK->QueryObjectArray("select id,name from setup_rubrika ".($_GET['rub']>0?"where id=".$_GET['rub']:'')." order by sort");	// берём список рубрик
foreach($spisokRub as $spRub)
	{
	$cRub=$VK->QRow("select count(id) from zayav ".$find." and rubrika=".$spRub->id); // смотрим, есть ли объявления в рубрике
	if($cRub>0)
		{
		$html.="<DIV class=rub>".$spRub->name."</DIV>";
		$podRub=$VK->QueryObjectArray("select id,name from setup_pod_rubrika where rubrika_id=".$spRub->id.($_GET['podrub']>0?" and id=".$_GET['podrub']:'')." order by sort");
		if(count($podRub))
			foreach($podRub as $pr)
				{
				$spisok=$VK->QueryObjectArray("select * from zayav ".$find." and podrubrika=".$pr->id." order by txt");	// смотрим есть ли объявления в подрубрике
				if(count($spisok)>0)
					{
					$html.="<DIV class=podrub>".$pr->name."</DIV>";
					foreach($spisok as $sp)
						{
						$contact=($sp->telefon?"<B>Тел.: ".$sp->telefon."</B>":'');
						if($sp->adres) $contact.=($sp->telefon?", ":'')."<B>Адрес: ".$sp->adres."</B>";
						if($sp->viewer_id_show==1)
							{
							$vkUs=$VK->QueryObjectOne("select first_name,last_name from vk_user where viewer_id=".$sp->viewer_id_add." limit 1");
							$contact.="<EM>(<A href='http://vk.com/id".$sp->viewer_id_add."' target=_vk>".$vkUs->first_name." ".$vkUs->last_name."</A>)</EM>";
							}
						if($_GET['input']) $sp->txt=preg_replace("/(".$input.")/i","<TT>\\1</TT>",$sp->txt);
						$html.="<DIV class=unitgaz><DIV class='".$sp->dop."'><I>&bull;</I>".$sp->txt." ".$contact."</DIV></DIV>";
						}
					$html.="<BR>";
					}
				}
		// выводим список рубрики, у которых подрубрика = 0
		if($_GET['podrub']==0)
			{
			$spisok=$VK->QueryObjectArray("select * from zayav ".$find." and rubrika=".$spRub->id." and podrubrika=0 order by txt");
			if(count($spisok)>0)
				foreach($spisok as $sp)
					{
					$contact=($sp->telefon?"<B>Тел.: ".$sp->telefon."</B>":'');
					if($sp->adres) $contact.=($sp->telefon?", ":'')."<B>Адрес: ".$sp->adres."</B>";
					if($sp->viewer_id_show==1)
						{
						$vkUs=$VK->QueryObjectOne("select first_name,last_name from vk_user where viewer_id=".$sp->viewer_id_add." limit 1");
						$contact.="<EM>(<A href='http://vk.com/id".$sp->viewer_id_add."' target=_vk>".$vkUs->first_name." ".$vkUs->last_name."</A>)</EM>";
						}
					if($_GET['input']) $sp->txt=preg_replace("/(".$input.")/i","<TT>\\1</TT>",$sp->txt);
					$html.="<DIV class=unitgaz><DIV class='".$sp->dop."'><I>&bull;</I>".$sp->txt." ".$contact."</DIV></DIV>";
					}
				}
			}
	}

$send->html=iconv("WINDOWS-1251","UTF-8",$html);

echo json_encode($send);
?>



