<?php
require_once('../../../include/AjaxHeader.php');

$timeStart=strtotime($_POST['day_begin']);

// ���� �����������, �� ������ ������
if(date("w",$timeStart)==1)
	{
	// ���������� ������ ���� ���������� ����, ���� ���� �� ���� ������, �� ����������
	$timeEnd=strtotime(($_POST['year']+1)."-01-01");
	while($timeStart<$timeEnd)
		{
		$id=$VK->Query("insert into gazeta_nomer (
general_nomer,
week_nomer,
day_begin,
viewer_id_add
) values (
".$_POST['general_nomer'].",
".$_POST['week_nomer'].",
'".$_POST['day_begin']."',
".VIEWER_ID."
)");

		$monMon=date('n',$timeStart);
		$monSun=date('n',$timeStart+600000);
		$dayMon=date('j',$timeStart);
		$daySun=date('j',$timeStart+600000);
		if($monMon>$monSun) $_POST['year']++;
		if($monMon==$monSun)
			$data=$dayMon."-".$daySun." ".$MonthFull[$monMon]." ".$_POST['year'];
		else $data=$dayMon." ".$MonthFull[$monMon]." - ".$daySun." ".$MonthFull[$monSun]." ".$_POST['year'];

		$VK->Query("update gazeta_nomer set
day_end=date_add(day_begin,interval 6 day),
day_print=date_add(day_begin,interval ".($_POST['day_print']-1)." day),
day_public=date_add(day_begin,interval ".($_POST['day_public']-1)." day),
day_txt='".$data."'
where id=".$id);

		$_POST['general_nomer']++;
		$_POST['week_nomer']++;
		$timeStart+=604800;
		$_POST['day_begin']=strftime("%Y-%m-%d",$timeStart);
		}
	}
GvaluesCreate();

$send=1;

echo json_encode($send);
?>



