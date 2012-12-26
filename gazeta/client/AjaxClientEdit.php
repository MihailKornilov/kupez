<?php
require_once('../../include/AjaxHeader.php');

if($_POST['person'])
	$VK->Query("update client set
person=".$_POST['person'].",
org_name='".iconv("UTF-8","WINDOWS-1251",$_POST['org_name'])."',
fio='".iconv("UTF-8","WINDOWS-1251",$_POST['fio'])."',
telefon='".iconv("UTF-8","WINDOWS-1251",$_POST['telefon'])."',
adres='".iconv("UTF-8","WINDOWS-1251",$_POST['adres'])."',
viewer_id_edit=".$_GET['viewer_id'].",
dtime_edit=current_timestamp
where id=".$_GET['id']);

$client=$VK->QueryObjectOne("select * from client where id=".$_GET['id']);
$personName=$VK->QRow("select name from setup_person where id=".$client->person);
$send->person_name=iconv("WINDOWS-1251","UTF-8",$personName);
$send->person_id=$client->person;
$send->org_name=iconv("WINDOWS-1251","UTF-8",$client->org_name);
$send->fio=iconv("WINDOWS-1251","UTF-8",$client->fio);
$send->telefon=iconv("WINDOWS-1251","UTF-8",$client->telefon);
$send->adres=iconv("WINDOWS-1251","UTF-8",$client->adres);
if($client->viewer_id_add>0)
	{
	$vkUs=$VK->QueryObjectOne("select first_name,last_name,sex from vk_user where viewer_id=".$client->viewer_id_add);
	$info="Информацию о клиенте вн".($vkUs->sex==1?'есла':'ёс')." <A href='http://vk.com/id".$client->viewer_id_add."' target=_vk>".$vkUs->first_name." ".$vkUs->last_name."</A> ".FullData($client->dtime_add,0,0,1);
	}
if($client->viewer_id_edit>0)
	{
	$vkUs=$VK->QueryObjectOne("select first_name,last_name,sex from vk_user where viewer_id=".$client->viewer_id_edit);
	$info.="<BR>Последний раз редактировал".($vkUs->sex==1?'а':'')." <A href='http://vk.com/id".$client->viewer_id_edit."' target=_vk>".$vkUs->first_name." ".$vkUs->last_name."</A> ".FullData($client->dtime_edit,0,0,1);
	}
$send->info=iconv("WINDOWS-1251","UTF-8",$info);
echo json_encode($send);
?>



