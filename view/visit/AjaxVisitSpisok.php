<?php
function visitEnd($count)
	{
	$ost=$count%10;
	$ost10=$count/10%10;

	if($ost10==1) return '��';
	else
		switch($ost)
			{
			case '1': return '�';
			case '2': return '�';
			case '3': return '�';
			case '4': return '�';
			default: return '��';
			}
	}

require_once('../../include/AjaxHeader.php');

$find="where viewer_id";
switch($_GET['radio'])
	{
	case 2: $fCount="�� ������� "; $find.=" and enter_last>='".strftime("%Y-%m-%d 00:00:00",time())."'"; break;
	case 3: $fCount="� ���� ������ "; $find.=" and enter_last>='".strftime("%Y-%m-01 00:00:00",time())."'"; break;
	case 4: $fCount="��������� ���������� "; $find.=" and ob_count>0"; break;
	case 5: $fCount="���������� ���������� "; $find.=" and app_setup=1"; break;
	case 6: $fCount="�������� � ����� ���� "; $find.=" and menu_left_set=1";break;
	default: $fCount="����� ";
	}

$send[0]->count=$VK->QRow("select count(viewer_id) from vk_user ".$find);
$send[0]->result=utf8($fCount.$send[0]->count." ���������".visitEnd($send[0]->count));
$send[0]->page=0;

$CP=50;
$spisok=$VK->QueryObjectArray("select * from vk_user ".$find." order by enter_last desc limit ".(($_GET['page']-1)*$CP).",".$CP);
if(count($spisok)>0)
	{
	$today=strftime("%Y-%m-%d");;
	foreach($spisok as $n=>$sp)
		{
		$send[$n]->viewer_id=$sp->viewer_id;
		$send[$n]->first_name=utf8($sp->first_name);
		$send[$n]->last_name=utf8($sp->last_name);
		$send[$n]->photo=$sp->photo;
		$send[$n]->count_day=0;
		if($today==substr($sp->enter_last,0,10))
			{
			$send[$n]->count_day=$VK->QRow("select count_day from visit where viewer_id=".$sp->viewer_id." and dtime_add LIKE '".$today."%'");
			$send[$n]->time=round(substr($sp->enter_last,10,3)).substr($sp->enter_last,13,3);
			}
		else $send[$n]->time=utf8(FullDataTime($sp->enter_last,1));
		$send[$n]->ob_count=$sp->ob_count;
		}
	if(count($spisok)==$CP)
		{
		$count=$VK->QNumRows("select viewer_id from vk_user ".$find." limit ".($_GET['page']*$CP).",".$CP);
		$_GET['page']++;
		if($count>0) $send[0]->page=$_GET['page'];
		}
	}

echo json_encode($send);
?>



