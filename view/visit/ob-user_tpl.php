<?php
if(isset($WR[$vkUser->viewer_id])) Header("Location:".URL); // ��������� �� ������� ��������, ���� ������������ ������ �������� ���
include('incHeader.php');

$dLink2='Sel'; include 'vk/visit/dopLinks.php';

if(!$_GET['id']) $_GET['id']=0;
?>

<SCRIPT LANGUAGE="JavaScript">
$(document).ready(function(){
	obSpisok();
	});

function obSpisok(OBJ)
	{
	var OBJ = $.extend({
		page:1,
		view:$("#spisok"),
		user:<?php echo $_GET['id']; ?>
		},OBJ);

	$("#findResult").find('IMG').remove().end().append("<IMG src=/img/upload.gif>");

	if(OBJ.page==1) VK.callMethod('scrollWindow',0);
	VK.callMethod('setLocation','vk-ob-user_'+OBJ.user);

	var URL="&page="+OBJ.page;
	URL+="&user="+OBJ.user;

	$.getJSON("/vk/visit/AjaxObUserSpisok.php?<?php echo $VALUES; ?>"+URL,function(data){
		if(data[0].count>0)
			{
			var HTML='';
			if(OBJ.user>0)
				{
				HTML+="<DIV class=user>";
				HTML+="<TABLE cellspacing=0 cellpadding=0>";
				HTML+="<TR><TD class=img><A href='http://vk.com/id"+OBJ.user+"' target=_vk><IMG src="+data[0].photo+"></A>";
				HTML+="<TD valign=top><DIV class=img_del onclick=\"obSpisok({user:0})\"></DIV><A href='http://vk.com/id"+OBJ.user+"' target=_vk><B>"+data[0].vk_name+"</B></A>";
				HTML+="<DIV class=ob>"+data[0].ob_count+"</A></DIV>";
				HTML+="</TABLE>";
				HTML+="</DIV>";
				}
			for(var n=0;n<data.length;n++)
				{
				HTML+="<DIV class=unit>";
					HTML+="<DIV class='"+data[n].dop+"'>";
					HTML+="<TABLE cellpadding=0 cellspacing=0 width=100%>";
					HTML+="<TR><TD class=txt><EM>"+data[n].rub+"</EM> � ";
					if(data[n].podrub) HTML+="<EM>"+data[n].podrub+"</EM> � ";
					HTML+=data[n].txt;
					if(data[n].telefon) HTML+=" <B>���.: "+data[n].telefon+"</B>";
					if(data[n].adres) HTML+=" <B>�����: "+data[n].adres+"</B>";
					if(data[n].viewer_id_show>0) HTML+="<A href='http://vk.com/id"+data[n].viewer_id+"' target=_vk class=vk_name>"+data[n].vk_name+"</A>";
					if(data[n].file) HTML+="<TD width=80 align=center valign=top><IMG src=/files/images/"+data[n].file+"s.jpg onclick=fotoShow('"+data[n].file+"');>";
					HTML+="</TABLE>";
					HTML+="<DIV class=data>"+data[n].dtime;
					if(OBJ.user==0) HTML+="<A href='javascript:' onclick=\"obSpisok({user:"+data[n].viewer_id+"})\">"+data[n].vk_name+"</A>";
					HTML+="</DIV>";
					HTML+="</DIV>";
				HTML+="</DIV>";
				}
			if(data[0].page>0) HTML+="<DIV><DIV id=ajaxNext onclick=obNext("+data[0].page+");>�������� �� ����������</DIV></DIV>";
			$("#findResult").html(data[0].result);
			OBJ.view.html(HTML);
			$(".unit").hover(function(){ $(this).find(".edit").show(); },function(){ $(this).find(".edit").hide(); });
			}
		else
			{
			$("#findResult").html("������ �� ��� �����������.");
			OBJ.view.html("<DIV class=findEmpty>������ �� ��� �����������.</DIV>");
			}
		frameBodyHeightSet();
		});
	}

function obNext(P)
	{
	$("#ajaxNext").html("<IMG SRC=/img/upload.gif>");
	obSpisok({page:P,view:$("#ajaxNext").parent()});
	}

</SCRIPT>

<DIV id=findResult>&nbsp;</DIV>

<TABLE cellpadding=0 cellspacing=0 id=vk-ob-user>
<TR>
	<TD id=spisok>&nbsp;
	<TD id=cond>


</TABLE>
<?php include('incFooter.php'); ?>



