<?php
require_once('../../../include/AjaxHeader.php');

if(!$VK->QRow("select viewer_id from vk_user where viewer_id=".$_POST['uid']))
	{
	require_once('../../../include/vkapi.class.php');
	$VKAPI = new vkapi(2881875,'h9IjOkxIMwoW8agQkW3M'); 
	$res=$VKAPI->api('users.get',array('uids'=>$_POST['uid'],'fields'=>'photo,sex'));
	$VK->Query("insert into vk_user (
viewer_id,
first_name,
last_name,
sex,
photo
) values (
".$_POST['uid'].",
'".iconv('UTF8','CP1251',$res['response'][0]['first_name'])."',
'".iconv('UTF8','CP1251',$res['response'][0]['last_name'])."',
'".$res['response'][0]['sex']."',
'".$res['response'][0]['photo']."')");
	}

if(!$VK->QRow("select viewer_id from worker where viewer_id=".$_POST['uid']))
	$VK->Query("insert into worker (viewer_id) values (".$_POST['uid'].")");

$send=1;
echo json_encode($send);
?>



