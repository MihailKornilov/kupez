$(document).ready(function(){
	$("#cDolg").myCheck({name:"��������",func:function(){
			$("#clientFind INPUT:first").val('');
			$("#clientFind H5 DIV:first").show();
			clientFindGo();
			}
		});

	$("#clientFind").topSearch({
		width:585,
		focus:1,
		txt:'������� ������� ������ �������',
		func:function(INP)
			{
			$("#cDolg").myCheckVal();
			clientFindGo({input:INP});
			}
		});

	$("#personFind").vkSel({
		width:177,
		spisok:<?php echo vkSelGetJson("select id,name from setup_person order by sort"); ?>,
		title0:'��������� �� ������',
		func:clientFindGo
		});


	clientFindGo();
	
	VK.callMethod('setLocation','client');
	});

function clientFindGo(OBJ)
	{
	$("#findResult").find('IMG').remove().end().append("<IMG src=/img/upload.gif>");

	var OBJ = $.extend({
		page:1,
		view:$("#spisok"),
		input:''
		},OBJ);

	var URL="&page="+OBJ.page;
	if(OBJ.input) URL+="&input="+encodeURIComponent(OBJ.input);
	URL+="&dolg="+$("#cDolg").val();
	URL+="&person="+$("#personFind").val();

	$.getJSON("/gazeta/client/AjaxClientSpisok.php?<?php echo $VALUES; ?>"+URL,"",function(data){
		var HTML='';
		if(data[0].count>0)
			{
			for(var n=1;n<data.length;n++)
				{
				HTML+="<DIV class=unit>";
				if(data[n].balans!=0) HTML+="<DIV class=balans>������: <B style=color:#"+(data[n].balans<0?'A00':'090')+">"+data[n].balans+"</B></DIV>";
				HTML+="<TABLE cellspacing=3 cellpadding=0>";
				if(data[n].org_name)
					HTML+="<TR><TD class=tdAbout>�����������:<TD><A HREF='<?php echo $URL; ?>&my_page=clientInfo&id="+data[n].id+"'>"+data[n].org_name+"</A>";
				else HTML+="<TR><TD class=tdAbout>���:<TD><A HREF='<?php echo $URL; ?>&my_page=clientInfo&id="+data[n].id+"'>"+data[n].fio+"</A>";
				if(data[n].telefon) HTML+="<TR><TD class=tdAbout>�������:<TD>"+data[n].telefon;
				if(data[n].adres) HTML+="<TR><TD class=tdAbout>�����:<TD>"+data[n].adres;
				if(data[n].zayav_count>0)HTML+="<TR><TD class=tdAbout>������:<TD>"+data[n].zayav_count;
				HTML+="</TABLE></DIV>";
				}
			if(data[0].page>0) HTML+="<DIV><DIV id=ajaxNext onclick=client20("+data[0].page+");>��������� 20 ��������</DIV></DIV>";
			$(OBJ.view).html(HTML);
			$("#findResult").html(data[0].result);
			}
		else
			{
			$(OBJ.view).html("<DIV class=findEmpty>������ �� ��� �����������.</DIV>");
			$("#findResult").html("������ �� ��� �����������.");
			}
		$(".path IMG").remove();
		frameBodyHeightSet();
		});
	}

function client20(P)
	{
	$("#ajaxNext").css("padding","10px 0px 9px 0px").html("<IMG SRC=/img/upload.gif>");
	clientFindGo({page:P,view:$("#ajaxNext").parent()});
	}

function ca()
	{
	clientAdd(	function(id){ location.href="<?php echo $URL; ?>&my_page=clientInfo&id="+id; });
	}




