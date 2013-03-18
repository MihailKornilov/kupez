<?php
require_once('../../include/AjaxHeader.php');

if($_POST['person'])
	$VK->Query("update client set
person=".$_POST['person'].",
org_name='".win1251($_POST['org_name'])."',
fio='".win1251($_POST['fio'])."',
telefon='".win1251($_POST['telefon'])."',
adres='".win1251($_POST['adres'])."',
viewer_id_edit=".VIEWER_ID.",
dtime_edit=current_timestamp
where id=".$_GET['id']);

$client=$VK->QueryObjectOne("select * from client where id=".$_GET['id']);
$personName=$VK->QRow("select name from setup_person where id=".$client->person);
$send->person_name=utf8($personName);
$send->person_id=$client->person;
$send->org_name=utf8($client->org_name);
$send->fio=utf8($client->fio);
$send->telefon=utf8($client->telefon);
$send->adres=utf8($client->adres);
if($client->viewer_id_add>0)
	{
	$vkUs=$VK->QueryObjectOne("select first_name,last_name,sex from vk_user where viewer_id=".$client->viewer_id_add);
	$info="���������� � ������� ��".($vkUs->sex==1?'����':'��')." <A href='http://vk.com/id".$client->viewer_id_add."' target=_vk>".$vkUs->first_name." ".$vkUs->last_name."</A> ".FullData($client->dtime_add,0,0,1);
	}
if($client->viewer_id_edit>0)
	{
	$vkUs=$VK->QueryObjectOne("select first_name,last_name,sex from vk_user where viewer_id=".$client->viewer_id_edit);
	$info.="<BR>��������� ��� ������������".($vkUs->sex==1?'�':'')." <A href='http://vk.com/id".$client->viewer_id_edit."' target=_vk>".$vkUs->first_name." ".$vkUs->last_name."</A> ".FullData($client->dtime_edit,0,0,1);
	}
$send->info=utf8($info);
echo json_encode($send);
?>



