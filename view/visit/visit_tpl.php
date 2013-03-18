<?php
if(isset($WR[$vkUser->viewer_id])) Header("Location:".URL); // ��������� �� ������� ��������, ���� ������������ ������ �������� ���
include('incHeader.php');

/*
// ��������� ������� ���������� ����� ���� �����������
$spisok=$VK->QueryObjectArray("select * from vk_user order by viewer_id");
foreach($spisok as $sp)
	{
	$last=$VK->QRow("select dtime_add from visit where viewer_id=".$sp->viewer_id." order by id desc");
	if(!$last) $last=$sp->dtime_add;
	$VK->Query("update vk_user set enter_last='".$last."' where viewer_id=".$sp->viewer_id);
	}



// ��������� ���������� �����٨���� ���������� ���� �����������
$spisok=$VK->QueryObjectArray("select * from vk_user order by viewer_id");
foreach($spisok as $sp)
	{
	$ob_count=$VK->QRow("select count(id) from zayav where category=1 and vk_srok>0 and viewer_id_add=".$sp->viewer_id);
	$VK->Query("update vk_user set ob_count='".$ob_count."' where viewer_id=".$sp->viewer_id);
	}



// ��������� ���� - ��������� �� ������������ ����������
//$VK->Query("update vk_user set app_setup=0");
require_once('include/vkapi.class.php');
$VKAPI = new vkapi(2881875,'h9IjOkxIMwoW8agQkW3M');
$spisok=$VK->QueryObjectArray("select * from vk_user order by viewer_id desc limit 500,100");
foreach($spisok as $sp)
	{
	$app_setup=$VKAPI->api('isAppUser',array('uid'=>$sp->viewer_id));
	$VK->Query("update vk_user set app_setup=".$app_setup['response']." where viewer_id=".$sp->viewer_id);
	}



// ��������� ���� - ��������� �� ������������ ������ � ����� ����
//$VK->Query("update vk_user set app_setup=0");
require_once('include/vkapi.class.php');
$VKAPI = new vkapi(2881875,'h9IjOkxIMwoW8agQkW3M');
$spisok=$VK->QueryObjectArray("select * from vk_user order by viewer_id desc limit 500,100");
foreach($spisok as $sp)
	{
	$mls=$VKAPI->api('getUserSettings',array('uid'=>$sp->viewer_id));
	$menu_left_set=$mls['response']&256;
	$VK->Query("update vk_user set menu_left_set=".($menu_left_set>0?1:0)." where viewer_id=".$sp->viewer_id);
	}
*/

$dLink1='Sel'; include 'vk/visit/dopLinks.php';
?>

<SCRIPT LANGUAGE="JavaScript">
$(document).ready(function(){
	$("#findRadio").myRadio({
		spisok:[
			{uid:1,title:'��� ����������'},
			{uid:2,title:'�������� �������'},
			{uid:3,title:'� ���� ������'},
			{uid:4,title:'��������� ����������'},
			{uid:5,title:'���������� ����������'},
			{uid:6,title:'�������� � ����� ����'}],
		bottom:7,
		func:visitSpisok
		});

	$("#with_ob").myCheck({name:"� ������������",func:visitSpisok});

	visitSpisok();

//VK.api('setCounter',{counter:7});
/*
VK.api('getUserSettings',{},function(data){
		$(".path").append(" flag = "+(data.response&1));
	});
*/

	VK.callMethod('setLocation','vk-visit');
	});

function visitSpisok(OBJ)
	{
	var OBJ = $.extend({
		page:1,
		view:$("#spisok")
		},OBJ);

	$("#findResult").find('IMG').remove().end().append("<IMG src=/img/upload.gif>");

	var URL="&page="+OBJ.page;
	URL+="&radio="+$("#findRadio").val();
	$.getJSON("/vk/visit/AjaxVisitSpisok.php?<?php echo $VALUES; ?>"+URL,function(data){
		if(data[0].count>0)
			{
			var HTML='';
			var ENTER='';
			for(var n=0;n<data.length;n++)
				{
				HTML+="<DIV class=unit>";
				HTML+="<TABLE cellspacing=0 cellpadding=0>";
				HTML+="<TR><TD class=img><A href='http://vk.com/id"+data[n].viewer_id+"' target=_vk><IMG src="+data[n].photo+"></A>";
				ENTER=''; if(data[n].count_day>1) ENTER+="<SPAN>"+data[n].count_day+"x</SPAN>";
				HTML+="<TD valign=top><DIV class=time>"+ENTER+data[n].time+"</DIV><A href='http://vk.com/id"+data[n].viewer_id+"' target=_vk><B>"+data[n].last_name+" "+data[n].first_name+"</B></A>";
				if(data[n].ob_count>0) HTML+="<DIV class=ob><A href='<?php echo URL; ?>&my_page=vk-ob-user&id="+data[n].viewer_id+"'>����������: "+data[n].ob_count+"</A></DIV>";
				HTML+="</TABLE>";
				HTML+="</DIV>";
				}
			if(data[0].page>0) HTML+="<DIV><DIV id=ajaxNext onclick=visitNext("+data[0].page+");>�������� �� �����������</DIV></DIV>";
			$("#findResult").html(data[0].result);
			OBJ.view.html(HTML);
			}
		else
			{
			$("#findResult").html("������ �� ��� �����������.");
			OBJ.view.html("<DIV class=findEmpty>������ �� ��� �����������.</DIV>");
			}
		frameBodyHeightSet();
		});
	}

function visitNext(P)
	{
	$("#ajaxNext").html("<IMG SRC=/img/upload.gif>");
	visitSpisok({page:P,view:$("#ajaxNext").parent()});
	}

</SCRIPT>

<DIV id=findResult>&nbsp;</DIV>

<TABLE cellpadding=0 cellspacing=0 id=vk-visit>
<TR>
	<TD id=spisok>&nbsp;
	<TD id=cond><INPUT TYPE=hidden id=findRadio value=2>


</TABLE>
<?php include('incFooter.php'); ?>



