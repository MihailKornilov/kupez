<?php
require_once('../AjaxHeader.php');

if($_GET['value'])
	{
	$input=iconv("UTF-8","WINDOWS-1251",$_GET['value']);
	$find=" and (fio LIKE '%".$input."%' or telefon LIKE '%".$input."%' or adres LIKE '%".$input."%')";
	}

$spisok=$VK->QueryObjectArray("select * from client where id".$find." order by fio limit ".$_GET['limit']);
if(count($spisok)>0)
	foreach($spisok as $n=>$sp)
		{
		$send[$n]->uid=$sp->id;
		$send[$n]->title=iconv("WINDOWS-1251","UTF-8",$sp->fio);
		if($_GET['value'])
			{
			$sp->fio=preg_replace("/(".$input.")/i","<EM>\\1</EM>",$sp->fio);
			$sp->telefon=preg_replace("/(".$input.")/i","<EM>\\1</EM>",$sp->telefon);
			$sp->adres=preg_replace("/(".$input.")/i","<EM>\\1</EM>",$sp->adres);
			}
		$send[$n]->content=iconv("WINDOWS-1251","UTF-8",$sp->fio."<DIV class=pole2><SPAN>".$sp->telefon."</SPAN>".($sp->telefon?'<BR>':'').$sp->adres."</DIV>");
		}

if($_GET['set'])
	{
	$fio=$VK->QRow("select fio from client where id=".$_GET['set']);
	if(count($fio)==1) $send[0]->set=iconv("WINDOWS-1251","UTF-8",$fio);
	}

echo json_encode($send);
?>



