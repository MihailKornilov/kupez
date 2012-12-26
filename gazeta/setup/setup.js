function setupSet(ID)
	{
	$(".razdel").find(".help").remove();
	switch(ID)
		{
		case '1': setupPerson(); break;
		case '2': setupRubrika(); break;
		case '7': setupPodRubrika(); break;
		case '3': setupGazNomer(); break;
		case '4': setupSmKvCost(); break;
		case '5': setupSkidka(); break;
		case '6': setupObDop(); break;
		case '8': setupAccess(); break;
		case '9': setupObLenght(); break;
		}
	}

// ���������
function setupPerson()
	{
	$(".razdel")
		.find(".help").remove()
		.end().append("<DIV class=help>���� ���������� ������������ ��� ���������� �������� �� ���������, ����� ��� '<B>������� ������</B>', '<B>�����������</B>' � ��.</DIV>");
	var HTML="<DIV id=person>";
	HTML+="<DIV class=headName>��������� ����� ����������</DIV>";
	HTML+="<A href='javascript:' onclick=personAdd();>�������� ����� ��� ���������</A>";
	HTML+="<DIV id=person_table></DIV>";
	HTML+="</DIV>";
	$("#edit").html(HTML);
	$.getJSON("/gazeta/setup/person/AjaxPersonGet.php?"+$("#VALUES").val(),function(res){
		if(res!=null)
			{
			$("#person_table").html("<IMG src=/img/upload.gif>");
			var HTML="<TABLE cellpadding=0 cellspacing=0 class=tabSpisok><TR><TH class=name>������������<TH class=colvo>���-��<BR>��������<TH class=set>���������</TABLE>";
			HTML+="<DL id=person_drag>";
			for(var n=0;n<res.length;n++)
				{
				HTML+="<DD id="+res[n].id+"><TABLE cellpadding=0 cellspacing=0 class=tabSpisok><TR>";
				HTML+="<TD class=name>"+res[n].name;
				HTML+="<TD class=colvo>"+(res[n].col>0?res[n].col:'');
				HTML+="<TD class=set><DIV class=img_edit onclick=personEdit("+res[n].id+");></DIV><DIV class=img_del onclick=personDel("+res[n].id+");></DIV></TABLE>";
				}
			HTML+="</DL>";
			$("#person_table").html(HTML);
			$("#person_drag").sortable({axis:'y',update:function(){
				var DD=$("#person_drag DD");
				var LEN=DD.length;
				var VAL=DD.eq(0).attr('id');
				if(LEN>1)
					{
					$("#person .headName").find("IMG").remove().end().append("<IMG src=/img/upload.gif>");
					for(var n=1;n<LEN;n++) VAL+=","+DD.eq(n).attr('id');
					$.getJSON("/gazeta/setup/person/AjaxPersonSort.php?"+$("#VALUES").val()+"&val="+VAL,function(){ $("#person .headName IMG").remove(); });
					}
				}});
			}
		else $("#person_table").html("������ ���������� ����.");
		frameBodyHeightSet();
		});
	}

function personAdd()
	{
	var HTML="<TABLE cellpadding=0 cellspacing=10>";
	HTML+="<TR><TD class=tdAbout>������������:<TD id=pn><INPUT type=text id=person_name style=width:200px;>";
	HTML+="</TABLE>";
	dialogShow({
		top:60,
		head:'�������� ������ ���������',
		content:HTML,
		focus:'#person_name',
		submit:function(){
			if(!$("#person_name").val()) $("#pn").alertShow({txt:'<SPAN class=red>�� ������� ������������.</SPAN>',top:-65,left:-3});
			else
				{
				$("#butDialog").butProcess();
				$.post("/gazeta/setup/person/AjaxPersonAdd.php?"+$("#VALUES").val(),{name:$("#person_name").val()},function(res){
					dialogHide();
					setupPerson();
					vkMsgOk("����� ������������ ��������� ���������!");
					},'json');
				}
			}
		});
	}

function personEdit(ID)
	{
	var HTML="<TABLE cellpadding=0 cellspacing=10>";
	HTML+="<TR><TD class=tdAbout>������������:<TD id=pn><INPUT type=text id=person_name style=width:200px; value='"+$("#"+ID+" .name").html()+"'>";
	HTML+="</TABLE>";
	dialogShow({
		top:100,
		head:'�������������� ������������ ���������',
		butSubmit:'���������',
		content:HTML,
		submit:function(){
			if(!$("#person_name").val()) $("#pn").alertShow({txt:'<SPAN class=red>�� ������� ������������.</SPAN>',top:-65,left:-3});
			else
				{
				$("#butDialog").butProcess();
				$.post("/gazeta/setup/person/AjaxPersonEdit.php?"+$("#VALUES").val(),{id:ID,name:$("#person_name").val()},function(res){
					dialogHide();
					setupPerson();
					vkMsgOk("������������ ��������� ��������!");
					},'json');
				}
			}
		});
	}

function personDel(ID)
	{
	var COLVO=$("#"+ID+" .colvo").html();
	var HTML="�������� ��������� ���������� <B>"+$("#"+ID+" .name").html()+"</B>.";
	if(COLVO)
		{
		HTML+="<TABLE cellspacing=0 cellpadding=0 style=margin-top:10px;>";
		HTML+="<TR><TD style=padding-right:5px; id=pn>����������� �������� �<TD><INPUT type=hidden id=person_ost value=0></TABLE>";
		}
	dialogShow({
		width:350,
		top:100,
		head:'��������',
		butSubmit:'�������',
		content:HTML,
		submit:function(){
			var OST=0;
			if(COLVO) OST=$("#person_ost").val();
			if(COLVO && OST==0) $("#pn").alertShow({txt:'<SPAN class=red>�������� ����� ��������� ���������.</SPAN>',top:-47,left:128});
			else
				{
				$("#butDialog").butProcess();
				$.post("/gazeta/setup/person/AjaxPersonDel.php?"+$("#VALUES").val(),{del:ID,ost:OST},function(){
					dialogHide();
					setupPerson();
					vkMsgOk("�������� ������� �����������!");
					});
				}
			}
		});
	if(COLVO)
		$("#person_ost").vkSel({
			width:180,
			msg:'�������� ���������',
			url:"/gazeta/setup/person/AjaxPersonOst.php?"+$("#VALUES").val()+"&del="+ID
			});
	}














// �������
function setupRubrika()
	{
	var HTML="<DIV id=rubrika>";
	HTML+="<DIV class=headName>��������� ������</DIV>";
	HTML+="<A href='javascript:' onclick=rubrikaAdd();>�������� ����� �������</A>";
	HTML+="<DIV id=rubrika_table></DIV>";
	HTML+="</DIV>";
	$("#edit").html(HTML);
	$.getJSON("/gazeta/setup/rubrika/AjaxRubrikaGet.php?"+$("#VALUES").val(),function(res){
		if(res!=null)
			{
			$("#rubrika_table").html("<IMG src=/img/upload.gif>");
			var HTML="<TABLE cellpadding=0 cellspacing=0 class=tabSpisok><TR><TH class=name>������������<TH class=set>���������</TABLE>";
			HTML+="<DL id=rubrika_drag>";
			for(var n=0;n<res.length;n++)
				{
				HTML+="<DD id="+res[n].id+"><TABLE cellpadding=0 cellspacing=0 class=tabSpisok><TR>";
				HTML+="<TD class=name>"+res[n].name;
				HTML+="<TD class=set><DIV class=img_edit onclick=rubrikaEdit("+res[n].id+");></DIV><DIV class=img_del onclick=rubrikaDel("+res[n].id+");></DIV></TABLE>";
				}
			HTML+="</DL>";
			$("#rubrika_table").html(HTML);
			$("#rubrika_drag").sortable({axis:'y',update:function(){
				var DD=$("#rubrika_drag DD");
				var LEN=DD.length;
				var VAL=DD.eq(0).attr('id');
				if(LEN>1)
					{
					$("#rubrika .headName").find("IMG").remove().end().append("<IMG src=/img/upload.gif>");
					for(var n=1;n<LEN;n++) VAL+=","+DD.eq(n).attr('id');
					$.getJSON("/gazeta/setup/rubrika/AjaxRubrikaSort.php?"+$("#VALUES").val()+"&val="+VAL,function(){ $("#rubrika .headName IMG").remove(); });
					}
				}});
			}
		else $("#rubrika_table").html("������� �� �������.");
		frameBodyHeightSet();
		});
	}

function rubrikaAdd()
	{
	HTML="<TABLE cellpadding=0 cellspacing=10>";
	HTML+="<TR><TD class=tdAbout>������������:<TD id=pn><INPUT type=text id=rubrika_name style=width:200px;>";
	HTML+="</TABLE>";
	dialogShow({
		top:60,
		head:'�������� ����� �������',
		content:HTML,
		focus:'#rubrika_name',
		submit:function(){
			if(!$("#rubrika_name").val()) $("#pn").alertShow({txt:'<SPAN class=red>�� ������� ������������.</SPAN>',top:-65,left:-3});
			else
				{
				$("#butDialog").butProcess();
				$.post("/gazeta/setup/rubrika/AjaxRubrikaAdd.php?"+$("#VALUES").val(),{name:$("#rubrika_name").val()},function(res){
					dialogHide();
					setupRubrika();
					vkMsgOk("����� ������� ���������!");
					},'json');
				}
			}
		});
	}

function rubrikaEdit(ID)
	{
	HTML="<TABLE cellpadding=0 cellspacing=10>";
	HTML+="<TR><TD class=tdAbout>������������:<TD id=pn><INPUT type=text id=rubrika_name style=width:200px; value='"+$("#"+ID+" .name").html()+"'>";
	HTML+="</TABLE>";
	dialogShow({
		top:100,
		head:'�������������� �������',
		butSubmit:'���������',
		content:HTML,
		submit:function(){
			if(!$("#rubrika_name").val()) $("#pn").alertShow({txt:'<SPAN class=red>�� ������� ������������.</SPAN>',top:-65,left:-3});
			else
				{
				$("#butDialog").butProcess();
				$.post("/gazeta/setup/rubrika/AjaxRubrikaEdit.php?"+$("#VALUES").val(),{id:ID,name:$("#rubrika_name").val()},function(res){
					dialogHide();
					setupRubrika();
					vkMsgOk("������������ ������� ��������!");
					},'json');
				}
			}
		});
	}

function rubrikaDel(ID)
	{
	dialogShow({
		width:300,
		top:100,
		head:'��������',
		butSubmit:'�������',
		content:"<CENTER>����������� �������� ������� '<B>"+$("#"+ID+" .name").html()+"</B>'.</CENTER>",
		submit:function(){
			$("#butDialog").butProcess();
			$.post("/gazeta/setup/rubrika/AjaxRubrikaDel.php?"+$("#VALUES").val(),{id:ID},function(res){
				dialogHide();
				setupRubrika();
				vkMsgOk("�������� ������� �����������!");
				},'json');
			}
		});
	}





















// ����������
function setupPodRubrika()
	{
	var HTML="<DIV id=podrubrika>";
	HTML+="<DIV class=headName>��������� ���������</DIV>";
	HTML+="<TABLE cellpadding=0 cellspacing=10><TR><TD><INPUT type=hidden id=rubrika_id><TD id=podRubLinkAdd></TABLE>";
	HTML+="<DIV id=podRub_table></DIV>";
	HTML+="</DIV>";
	$("#edit").html(HTML);
	$("#rubrika_id").vkSel({
		width:200,
		msg:'�������� �������',
		url:'/gazeta/setup/podrubrika/AjaxRubrikaGet.php?'+$("#VALUES").val(),
		func:podRubrikaGet
		});
	frameBodyHeightSet();
	}

function podRubrikaGet(ID)
	{
	var RUBNAME=$("#vkSel_rubrika_id INPUT:first").val();
	$("#podRubLinkAdd").html("<A href='javascript:' onclick=podRubrikaAdd();>�������� ����� ���������� ��� ������� <B>"+RUBNAME+"</B></A>");
	$.getJSON("/gazeta/setup/podrubrika/AjaxPodRubrikaGet.php?"+$("#VALUES").val()+"&rubrika_id="+ID,function(res){
		if(res!=null)
			{
			$("#podRub_table").html("<IMG src=/img/upload.gif>");
			var HTML="<TABLE cellpadding=0 cellspacing=0 class=tabSpisok><TR><TH class=name>������������<TH class=set>���������</TABLE>";
			HTML+="<DL id=podRub_drag>";
			for(var n=0;n<res.length;n++)
				{
				HTML+="<DD id="+res[n].id+"><TABLE cellpadding=0 cellspacing=0 class=tabSpisok><TR>";
				HTML+="<TD class=name>"+res[n].name;
				HTML+="<TD class=set><DIV class=img_edit onclick=podRubrikaEdit("+res[n].id+");></DIV><DIV class=img_del onclick=podRubrikaDel("+res[n].id+");></DIV></TABLE>";
				}
			HTML+="</DL>";
			$("#podRub_table").html(HTML);
			$("#podRub_drag").sortable({axis:'y',update:function(){
				var DD=$("#podRub_drag DD");
				var LEN=DD.length;
				var VAL=DD.eq(0).attr('id');
				if(LEN>1)
					{
					$("#podrubrika .headName").find("IMG").remove().end().append("<IMG src=/img/upload.gif>");
					for(var n=1;n<LEN;n++) VAL+=","+DD.eq(n).attr('id');
					$.getJSON("/gazeta/setup/podrubrika/AjaxPodRubrikaSort.php?"+$("#VALUES").val()+"&val="+VAL,function(){ $("#podrubrika .headName IMG").remove(); });
					}
				}});
			}
		else $("#podRub_table").html("��������� ��� <B>"+RUBNAME+"</B> ���.");
		frameBodyHeightSet();
		});
	}

function podRubrikaAdd()
	{
	var RUBNAME=$("#vkSel_rubrika_id INPUT:first").val();
	var ID=$("#rubrika_id").val();
	HTML="<TABLE cellpadding=0 cellspacing=10>";
	HTML+="<TR><TD class=tdAbout>�������:<TD>"+RUBNAME;
	HTML+="<TR><TD class=tdAbout>������������:<TD id=pn><INPUT type=text id=podrubrika_name style=width:200px;>";
	HTML+="</TABLE>";
	dialogShow({
		top:60,
		head:'�������� ����� ���������� ��� '+RUBNAME,
		content:HTML,
		focus:'#podrubrika_name',
		submit:function(){
			if(!$("#podrubrika_name").val()) $("#pn").alertShow({txt:'<SPAN class=red>�� ������� ������������.</SPAN>',top:-43,left:-3});
			else
				{
				$("#butDialog").butProcess();
				$.post("/gazeta/setup/podrubrika/AjaxPodRubrikaAdd.php?"+$("#VALUES").val(),{rubrika_id:ID,name:$("#podrubrika_name").val()},function(res){
					dialogHide();
					podRubrikaGet(ID);
					vkMsgOk("����� ���������� ���������!");
					},'json');
				}
			}
		});
	}

function podRubrikaEdit(ID)
	{
	var RUBNAME=$("#vkSel_rubrika_id INPUT:first").val();
	HTML="<TABLE cellpadding=0 cellspacing=10>";
	HTML+="<TR><TD class=tdAbout>�������:<TD>"+RUBNAME;
	HTML+="<TR><TD class=tdAbout>������������:<TD id=pn><INPUT type=text id=podrubrika_name style=width:200px; value='"+$("#"+ID+" .name").html()+"'>";
	HTML+="</TABLE>";
	dialogShow({
		top:100,
		head:'�������������� ����������',
		butSubmit:'���������',
		content:HTML,
		submit:function(){
			if(!$("#podrubrika_name").val()) $("#pn").alertShow({txt:'<SPAN class=red>�� ������� ������������.</SPAN>',top:-43,left:-3});
			else
				{
				$("#butDialog").butProcess();
				$.post("/gazeta/setup/podrubrika/AjaxPodRubrikaEdit.php?"+$("#VALUES").val(),{id:ID,name:$("#podrubrika_name").val()},function(res){
					dialogHide();
					podRubrikaGet($("#rubrika_id").val());
					vkMsgOk("������������ ���������� ��������!");
					},'json');
				}
			}
		});
	}

function podRubrikaDel(ID)
	{
	dialogShow({
		width:300,
		top:100,
		head:'��������',
		butSubmit:'�������',
		content:"<CENTER>����������� �������� ���������� '<B>"+$("#"+ID+" .name").html()+"</B>'.</CENTER>",
		submit:function(){
			$("#butDialog").butProcess();
			$.post("/gazeta/setup/podrubrika/AjaxPodRubrikaDel.php?"+$("#VALUES").val(),{id:ID},function(res){
				dialogHide();
				podRubrikaGet($("#rubrika_id").val());
				vkMsgOk("�������� ������� �����������!");
				},'json');
			}
		});
	}

















// ������ �������� ������
function setupGazNomer()
	{
	$("#edit").html("<IMG src=/img/upload.gif>");
	$.getJSON("/gazeta/setup/gazeta_nomer/AjaxYearGet.php?"+$("#VALUES").val(),function(res){
		var HTML="<DIV id=gazNomer>";
		HTML+="<DIV class=headName>���������� �������� �������� ������</DIV>";
		HTML+="<DIV id=dopMenu>";
		var DAT=new Date();
		var FY=DAT.getFullYear();
		for(var year=res.begin;year<=res.end;year++)
			HTML+="<A HREF='javascript:' class=link"+(year==FY?'Sel':'')+" onclick=gazNomerGet("+year+");><I></I><B></B><DIV>"+year+"</DIV><B></B><I></I></A>";
		HTML+="<DIV style=clear:both;></DIV></DIV>";

		HTML+="<DIV id=spisok></DIV>";
		HTML+="</DIV>";
		$("#edit").html(HTML);
		gazNomerGet(FY);
		});
	}


function gazNomerGet(YEAR,ID)
	{
	var A=$("#dopMenu A");
	A.attr('class','link');
	var LEN=A.length;
	for(var n=0;n<LEN;n++)
		if(A.eq(n).find("DIV:first").html()==YEAR)
			A.eq(n).attr('class','linkSel');
	$.getJSON("/gazeta/setup/gazeta_nomer/AjaxSpisokGet.php?"+$("#VALUES").val()+"&year="+YEAR+"&id="+ID,function(res){
		if(res[0].count==0) $("#spisok").html(res[0].txt);
		else
			{
			var HTML="<TABLE cellpadding=0 cellspacing=0 class=tabSpisok><TR>";
			HTML+="<TH>�����<BR>�������";
			HTML+="<TH>��� ������";
			HTML+="<TH>����<BR>��������<BR>� ������";
			HTML+="<TH>���� ������";
			HTML+="<TH>������";
			HTML+="<TH>���������";
			for(var n=0;n<res.length;n++)
				{
				HTML+="<TR id=gn"+res[n].id+" class='"+res[n].grey+(ID==res[n].id?' yellow':'')+"'><TD align=center><B id=edit_week_nomer>"+res[n].week_nomer+"</B> (<SPAN id=edit_general_nomer>"+res[n].general_nomer+"</SPAN>)";
				HTML+="<TD align=right>"+res[n].day_txt;
				HTML+="<TD align=right id=edit_day_print val='"+res[n].day_print_val+"'>"+res[n].day_print;
				HTML+="<TD align=right id=edit_day_public val='"+res[n].day_public_val+"'>"+res[n].day_public;
				HTML+="<TD align=center>"+(res[n].zayav_count>0?res[n].zayav_count:'');
				HTML+="<TD class=set><DIV class=img_edit onclick=gazNomerEdit("+res[n].id+");></DIV>";
				HTML+="<INPUT type=hidden id=edit_day_begin value='"+res[n].day_begin+"'><INPUT type=hidden id=edit_day_end value='"+res[n].day_end+"'>";
				HTML+="<INPUT type=hidden id=edit_day_begin_val value='"+res[n].day_begin_val+"'><INPUT type=hidden id=edit_day_end_val value='"+res[n].day_end_val+"'>";
				if(res[n].zayav_count==0) HTML+="<DIV class=img_del onclick=gazNomerDel("+res[n].id+");></DIV>";
				}
			HTML+="</TABLE><INPUT type=hidden id=gnYear value="+YEAR+">";
			$("#spisok").html(HTML);
			$("#spisok .yellow").mouseover(function(){ $(this).removeClass('yellow'); });
			}
		frameBodyHeightSet();
		});
	}



function gazNomerSpisokCreate(YEAR,OBJ)
	{
	$("#gazNomer").css('padding-bottom','300px');
	frameBodyHeightSet();

	var STYLE=" readonly style=width:70px;text-align:right;";
	var Y=YEAR+"-01-01";
	HTML="<DIV class=gnInfo>��� �������� ������ ������� ����� <B>"+YEAR+"</B> ���� ������� ������ <B>������� ������</B>, ������� ����� �������� � ���� ����. ��� ���� ����������� ��� ����������.</DIV>";
	HTML+="<TABLE cellpadding=0 cellspacing=10>";
	HTML+="<TR><TD class=tdAbout>����� �������:<TD id=enum><INPUT type=text id=week_nomer style=width:15px;text-align:right; maxlength=2>";
	HTML+="&nbsp;<INPUT type=text id=general_nomer style=width:20px;text-align:right; maxlength=3>";

	HTML+="<TR><TD class=tdAbout>�����������:<TD>";
	HTML+="<INPUT type=text		id=dayBegin		onclick=Calendar('"+Y+"',event,'dayBeginFunc');"+STYLE+"><INPUT type=hidden	id=dayBeginVal>";

	HTML+="<TR><TD class=tdAbout>���� �������� � ������:<TD><INPUT type=hidden	id=dayPrint>";
	HTML+="<TR><TD class=tdAbout id=butAll>���� ������:<TD><INPUT type=hidden	id=dayPublic>";

	HTML+="</TABLE>";
	dialogShow({
		top:100,
		width:380,
		head:'�������� ������ ������� ������',
		butSubmit:'���������',
		content:HTML,
		submit:function(){
			var WN=$("#week_nomer").val();
			var reg=/^[0-9]+$/;
			if(!reg.exec(WN)) { $("#enum").alertShow({txt:"<SPAN class=red>�� ��������� ������� ��������.<BR>����������� �����.</SPAN>",top:-55,left:-13}); $("#week_nomer").focus(); }
			else
				{
				var GN=$("#general_nomer").val();
				var reg=/^[0-9]+$/;
				if(!reg.exec(GN)) { $("#enum").alertShow({txt:"<SPAN class=red>�� ��������� ������� ��������.<BR>����������� �����.</SPAN>",top:-55,left:17}); $("#general_nomer").focus(); }
				else
					{
					var BEGIN=$("#dayBeginVal").val();
					var PRINT=$("#dayPrint").val();
					var PUBLIC=$("#dayPublic").val();
					if(!BEGIN || !PRINT || !PUBLIC) $("#butAll").alertShow({txt:"<SPAN class=red>���������� ������� ��� ��������.</SPAN>",top:10,left:90});
					else
						{
						$("#butDialog").butProcess();
						$.post("/gazeta/setup/gazeta_nomer/AjaxSpisokCreate.php?"+$("#VALUES").val(),{
							year:YEAR,
							week_nomer:WN,
							general_nomer:GN,
							day_begin:BEGIN,
							day_print:PRINT,
							day_public:PUBLIC
							},function(res){ $(OBJ).after("<IMG src=/img/upload.gif>").remove(); dialogHide(); gazNomerGet(YEAR); },'json');
						}
					}
				}
			}
		});
	$("#dayPrint").vkSel({
		width:100,
		title0:'�� �������',
		spisok:[{uid:1,title:'�����������'},{uid:2,title:'�������'},{uid:3,title:'�����'},{uid:4,title:'�������'},{uid:5,title:'�������'},{uid:6,title:'�������'},{uid:7,title:'�����������'},]
		});
	$("#dayPublic").vkSel({
		width:100,
		title0:'�� �������',
		spisok:[{uid:1,title:'�����������'},{uid:2,title:'�������'},{uid:3,title:'�����'},{uid:4,title:'�������'},{uid:5,title:'�������'},{uid:6,title:'�������'},{uid:7,title:'�����������'},]
		});
	}


function gazNomerEdit(ID)
	{
	var DAY_BEGIN=$("#gn"+ID+" #edit_day_begin").val();
	var DAY_BEGIN_VAL=$("#gn"+ID+" #edit_day_begin_val").val();
	var DAY_END=$("#gn"+ID+" #edit_day_end").val();
	var DAY_END_VAL=$("#gn"+ID+" #edit_day_end_val").val();
	var STYLE=" readonly style=width:70px;text-align:right;";
	HTML="<TABLE cellpadding=0 cellspacing=10>";
	HTML+="<TR><TD class=tdAbout>����� �������:<TD id=enum><INPUT type=text id=week_nomer style=width:15px;text-align:right; maxlength=2 value='"+$("#gn"+ID+" #edit_week_nomer").html()+"'>";
	HTML+="&nbsp;<INPUT type=text id=general_nomer style=width:20px;text-align:right; maxlength=3 value='"+$("#gn"+ID+" #edit_general_nomer").html()+"'>";

	HTML+="<TR><TD class=tdAbout>��� ������:<TD>";
	HTML+="<INPUT type=text		id=dayBegin			value='"+DAY_BEGIN+"' onclick=Calendar('"+DAY_BEGIN_VAL+"',event,'dayBeginFunc');"+STYLE+"> - ";
	HTML+="<INPUT type=hidden	id=dayBeginVal	value='"+DAY_BEGIN_VAL+"'>";
	HTML+="<INPUT type=text		id=dayEnd			value='"+DAY_END+"' onclick=Calendar('"+DAY_END_VAL+"',event,'dayEndFunc');"+STYLE+">";
	HTML+="<INPUT type=hidden	id=dayEndVal		value='"+DAY_END_VAL+"'>";

	HTML+="<TR><TD class=tdAbout>���� �������� � ������:<TD>";
	HTML+="<INPUT type=text		id=dayPrint		value='"+$("#gn"+ID+" #edit_day_print").html()+"' onclick=Calendar('"+$("#gn"+ID+" #edit_day_print").attr('val')+"',event,'dayPrintFunc');"+STYLE+">";
	HTML+="<INPUT type=hidden	id=dayPrintVal	value='"+$("#gn"+ID+" #edit_day_print").attr('val')+"'>";

	HTML+="<TR><TD class=tdAbout>���� ������:<TD>";
	HTML+="<INPUT type=text		id=dayPublic		value='"+$("#gn"+ID+" #edit_day_public").html()+"' onclick=Calendar('"+$("#gn"+ID+" #edit_day_public").attr('val')+"',event,'dayPublicFunc');"+STYLE+">";
	HTML+="<INPUT type=hidden	id=dayPublicVal	value='"+$("#gn"+ID+" #edit_day_public").attr('val')+"'>";

	HTML+="</TABLE>";
	dialogShow({
		top:100,
		width:380,
		head:'�������������� ������ ������ ������',
		butSubmit:'���������',
		content:HTML,
		submit:function(){
			var WN=$("#week_nomer").val();
			var reg=/^[0-9]+$/;
			if(!reg.exec(WN)) { $("#enum").alertShow({txt:"<SPAN class=red>�� ��������� ������� ��������.<BR>����������� �����.</SPAN>",top:-55,left:-13}); $("#week_nomer").focus(); }
			else
				{
				var GN=$("#general_nomer").val();
				var reg=/^[0-9]+$/;
				if(!reg.exec(GN)) { $("#enum").alertShow({txt:"<SPAN class=red>�� ��������� ������� ��������.<BR>����������� �����.</SPAN>",top:-55,left:17}); $("#general_nomer").focus(); }
				else
					{
					$("#butDialog").butProcess();
					$.post("/gazeta/setup/gazeta_nomer/AjaxGNEdit.php?"+$("#VALUES").val(),{
						id:ID,
						week_nomer:WN,
						general_nomer:GN,
						day_begin:$("#dayBeginVal").val(),
						day_end:$("#dayEndVal").val(),
						day_print:$("#dayPrintVal").val(),
						day_public:$("#dayPublicVal").val()
						},function(res){
							dialogHide();
							gazNomerGet($("#gnYear").val(),ID);
							vkMsgOk("������ ��������!");
							},'json');
					}
				}
			}
		});
	}


function dayBeginFunc(DAY)
	{
	var arr=DAY.split(/-/);
	$("#dayBegin").val(arr[2]+" "+getMonRus4[arr[1]]+" "+arr[0]);
	$("#dayBeginVal").val(DAY);
	$("#calendar").remove();
	}

function dayEndFunc(DAY)
	{
	var arr=DAY.split(/-/);
	$("#dayEnd").val(arr[2]+" "+getMonRus4[arr[1]]+" "+arr[0]);
	$("#dayEndVal").val(DAY);
	$("#calendar").remove();
	}

function dayPrintFunc(DAY)
	{
	var arr=DAY.split(/-/);
	$("#dayPrint").val(arr[2]+" "+getMonRus4[arr[1]]+" "+arr[0]);
	$("#dayPrintVal").val(DAY);
	$("#calendar").remove();
	}

function dayPublicFunc(DAY)
	{
	var arr=DAY.split(/-/);
	$("#dayPublic").val(arr[2]+" "+getMonRus4[arr[1]]+" "+arr[0]);
	$("#dayPublicVal").val(DAY);
	$("#calendar").remove();
	}



function gazNomerDel(ID)
	{
	dialogShow({
		width:250,
		top:100,
		head:'��������',
		butSubmit:'�������',
		content:"<CENTER>����������� ��������<BR>������ ������ <B>"+$("#gn"+ID+" #edit_week_nomer").html()+"</B> ("+$("#gn"+ID+" #edit_general_nomer").html()+").</CENTER>",
		submit:function(){
			$("#butDialog").butProcess();
			$.post("/gazeta/setup/gazeta_nomer/AjaxGNDel.php?"+$("#VALUES").val(),{id:ID},function(res){
				dialogHide();
				$("#gn"+ID).remove();
				vkMsgOk("�������� ������� �����������!");
				frameBodyHeightSet();
				},'json');
			}
		});
	}










// ��������� ��2 ��� ������ ������
function setupSmKvCost()
	{
	var HTML="<DIV id=smKvCost>";
	HTML+="<DIV class=headName>��������� ��������� ��&sup2; ������� ��� ������ ������</DIV>";
	HTML+="<A href='javascript:' id=polosaAdd>�������� ����� �������� ������</A>";
	HTML+="<DIV id=spisok></DIV>";
	HTML+="</DIV>";
	$("#edit").html(HTML);

	$("#polosaAdd").click(function(){
		HTML="<TABLE cellpadding=0 cellspacing=7>";
		HTML+="<TR><TD class=tdAbout>��������:<TD id=pn><INPUT type=text id=name style=width:200px; maxlength=50>";
		HTML+="<TR><TD class=tdAbout>���� �� ��&sup2;:<TD id=sup><INPUT type=text id=cena style=width:40px; maxlength=6> ���.";
		HTML+="</TABLE>";
		dialogShow({
			top:100,
			head:'�������� ������ �������� ������',
			content:HTML,
			submit:function(){
				if(!$("#name").val()) { $("#pn").alertShow({txt:'<SPAN class=red>�� ������� ��������.</SPAN>',top:-43,left:-3}); $("#name").focus(); }
				else
					{
					var CENA=$("#cena").val();
					var reg=/^[0-9.]+$/;
					if(!reg.exec(CENA)) { $("#sup").alertShow({txt:"<SPAN class=red>�� ��������� ������� ��������.<BR>����������� ����� � ����� ��� �����.</SPAN>",top:-55,left:-8}); $("#cena").focus(); }
					else
						{
						$("#butDialog").butProcess();
						$.post("/gazeta/setup/polosa_cost/AjaxPolosaAdd.php?"+$("#VALUES").val(),{name:$("#name").val(),cena:CENA},function(){
							dialogHide();
							setupSmKvCost();
							vkMsgOk("�������� ������� �����������!");
							},'json');
						}
					}
				},
			focus:'#name'
			});
		});

	$.getJSON("/gazeta/setup/polosa_cost/AjaxPolosaGet.php?"+$("#VALUES").val(),function(res){
		var HTML="<TABLE cellpadding=0 cellspacing=0 class=tabSpisok><TR><TH class=name>������<TH class=cena>���� �� ��&sup2;<TH class=set>���������</TABLE>";
		if(res!=null)
			{
			HTML+="<DL id=polosa_drag>";
			for(var n=0;n<res.length;n++)
				{
				HTML+="<DD id="+res[n].id+"><TABLE cellpadding=0 cellspacing=0 class=tabSpisok><TR>";
				HTML+="<TD class=name>"+res[n].name;
				HTML+="<TD class=cena>"+res[n].cena;
				HTML+="<TD class=set><DIV class=img_edit onclick=polosaEdit("+res[n].id+");></DIV><DIV class=img_del onclick=polosaDel("+res[n].id+");></DIV>";
				HTML+="</TABLE>";
				}
			HTML+="</DL>";
			}
		$("#spisok").html(HTML);
		frameBodyHeightSet();
		$("#polosa_drag").sortable({axis:'y',update:function(){
			var DD=$("#polosa_drag DD");
			var LEN=DD.length;
			var VAL=DD.eq(0).attr('id');
			if(LEN>1)
				{
				$("#smKvCost .headName").find("IMG").remove().end().append("<IMG src=/img/upload.gif>");
				for(var n=1;n<LEN;n++) VAL+=","+DD.eq(n).attr('id');
				$.getJSON("/gazeta/setup/polosa_cost/AjaxPolosaSort.php?"+$("#VALUES").val()+"&val="+VAL,function(){ $("#smKvCost .headName IMG").remove(); });
				}
			}});
		});
	}

function polosaEdit(ID)
	{
	HTML="<TABLE cellpadding=0 cellspacing=10>";
	HTML+="<TR><TD class=tdAbout>��������:<TD id=pn><INPUT type=text id=name style=width:200px; maxlength=50 value='"+$("#"+ID+" .name").html()+"'>";
	HTML+="<TR><TD class=tdAbout>���� �� ��&sup2;:<TD id=sup><INPUT type=text id=cena style=width:40px; maxlength=6 value='"+$("#"+ID+" .cena").html()+"'> ���.";
	HTML+="</TABLE>";
	dialogShow({
		top:100,
		head:'�������������� ������ ������',
		butSubmit:'���������',
		content:HTML,
		submit:function(){
			if(!$("#name").val()) { $("#pn").alertShow({txt:'<SPAN class=red>�� ������� ��������.</SPAN>',top:-43,left:-3}); $("#name").focus(); }
			else
				{
				var CENA=$("#cena").val();
				var reg=/^[0-9.]+$/;
				if(!reg.exec(CENA)) { $("#sup").alertShow({txt:"<SPAN class=red>�� ��������� ������� ��������.<BR>����������� ����� � ����� ��� �����.</SPAN>",top:-55,left:-8}); $("#cena").focus(); }
				else
					{
					$("#butDialog").butProcess();
					$.post("/gazeta/setup/polosa_cost/AjaxPolosaEdit.php?"+$("#VALUES").val(),{id:ID,name:$("#name").val(),cena:CENA},function(res){
						dialogHide();
						setupSmKvCost();
						vkMsgOk("������ ��������!");
						},'json');
					}
				}
			}
		});
	}

function polosaDel(ID)
	{
	dialogShow({
		width:270,
		top:100,
		head:'��������',
		butSubmit:'�������',
		content:"<CENTER>����������� �������� ������ ������ '<B>"+$("#"+ID+" .name").html()+"</B>'.</CENTER>",
		submit:function(){
			$("#butDialog").butProcess();
			$.post("/gazeta/setup/polosa_cost/AjaxPolosaDel.php?"+$("#VALUES").val(),{id:ID},function(res){
				dialogHide();
				setupSmKvCost();
				vkMsgOk("�������� ������� �����������!");
				},'json');
			}
		});
	}







// ������
function setupSkidka()
	{
	var HTML="<DIV id=skidka>";
	HTML+="<DIV class=headName>���������� ��������</DIV>";
	HTML+="<A href='javascript:' onclick=skidkaAdd();>�������� ����� ������</A>";
	HTML+="<DIV id=spisok></DIV>";
	HTML+="</DIV>";
	$("#edit").html(HTML);
	$.getJSON("/gazeta/setup/skidka/AjaxSkidkaGet.php?"+$("#VALUES").val(),function(res){
		var HTML="<TABLE cellpadding=0 cellspacing=0 class=tabSpisok><TR><TH>������ ������<TH>��������<TH>���������";
		if(res!=null)
			for(var n=0;n<res.length;n++)
				{
				HTML+="<TR>";
				HTML+="<TD align=center><B id=raz"+res[n].id+">"+res[n].razmer+"</B>%";
				HTML+="<TD id=ab"+res[n].id+">"+res[n].about;
				HTML+="<TD align=center><DIV class=img_edit onclick=skidkaEdit("+res[n].id+");></DIV><DIV class=img_del onclick=skidkaDel("+res[n].id+");>";
				}
		HTML+="</TABLE>";
		$("#spisok").html(HTML);
		frameBodyHeightSet();
		});
	}

function skidkaAdd()
	{
	HTML="<TABLE cellpadding=0 cellspacing=7>";
	HTML+="<TR><TD class=tdAbout>������ ������:<TD id=rz><INPUT type=text id=razmer style=width:30px; maxlength=3> %";
	HTML+="<TR><TD class=tdAbout>��������:<TD><INPUT type=text id=about style=width:200px; maxlength=200>";
	HTML+="</TABLE>";
	dialogShow({
		top:100,
		head:'�������� ����� ������',
		content:HTML,
		submit:function(){
			var RAZ=$("#razmer").val();
			var ALERT="<SPAN class=red>�� ��������� ����� ������ ������.<BR>����������� �������� �� 1 �� 100.</SPAN>";
			var reg=/^[0-9]+$/;
			if(!reg.exec(RAZ)) { $("#rz").alertShow({txt:ALERT,top:-55,left:-8}); $("#razmer").focus(); }
			else
				if(RAZ<1 || RAZ>100) { $("#rz").alertShow({txt:ALERT,top:-55,left:-8}); $("#razmer").focus(); }
				else
					{
					$("#butDialog").butProcess();
					$.post("/gazeta/setup/skidka/AjaxSkidkaAdd.php?"+$("#VALUES").val(),{about:$("#about").val(),razmer:RAZ},function(){
						dialogHide();
						setupSkidka();
						vkMsgOk("�������� ������� �����������!");
						},'json');
					}
			},
		focus:'#razmer'
		});
	}

function skidkaEdit(ID)
	{
	HTML="<TABLE cellpadding=0 cellspacing=7>";
	HTML+="<TR><TD class=tdAbout>������ ������:<TD id=rz><INPUT type=text id=razmer style=width:30px; maxlength=3 value='"+$("#raz"+ID).html()+"'> %";
	HTML+="<TR><TD class=tdAbout>��������:<TD><INPUT type=text id=about style=width:200px; maxlength=200 value='"+$("#ab"+ID).html()+"'>";
	HTML+="</TABLE>";
	dialogShow({
		top:100,
		head:'�������������� ������ ������',
		butSubmit:'���������',
		content:HTML,
		submit:function(){
			var RAZ=$("#razmer").val();
			var ALERT="<SPAN class=red>�� ��������� ����� ������ ������.<BR>����������� �������� �� 1 �� 100.</SPAN>";
			var reg=/^[0-9]+$/;
			if(!reg.exec(RAZ)) { $("#rz").alertShow({txt:ALERT,top:-55,left:-8}); $("#razmer").focus(); }
			else
				if(RAZ<1 || RAZ>100) { $("#rz").alertShow({txt:ALERT,top:-55,left:-8}); $("#razmer").focus(); }
				else
					{
					$("#butDialog").butProcess();
					$.post("/gazeta/setup/skidka/AjaxSkidkaEdit.php?"+$("#VALUES").val(),{id:ID,about:$("#about").val(),razmer:RAZ},function(){
						dialogHide();
						setupSkidka();
						vkMsgOk("��������� ������� �����������!");
						},'json');
					}
			}
		});
	}

function skidkaDel(ID)
	{
	dialogShow({
		width:260,
		top:100,
		head:'��������',
		butSubmit:'�������',
		content:"<CENTER>����������� �������� ������ <B>"+$("#raz"+ID).html()+"</B>%.</CENTER>",
		submit:function(){
			$("#butDialog").butProcess();
			$.post("/gazeta/setup/skidka/AjaxSkidkaDel.php?"+$("#VALUES").val(),{id:ID},function(res){
				dialogHide();
				setupSkidka();
				vkMsgOk("�������� ������� �����������!");
				},'json');
			}
		});
	}











// ��������� ����� ����������
function setupObLenght()
	{
	var HTML="<DIV id=obLen>";
	HTML+="<DIV class=headName>��������� ��������� ����� ����������</DIV>";
	HTML+="<DIV id=table></DIV></DIV>";
	$("#edit").html(HTML);
	$.getJSON("/gazeta/setup/ob_len/AjaxObLenGet.php?"+$("#VALUES").val(),function(res){
		HTML="<TABLE cellpadding=0 cellspacing=8>";
		HTML+="<TR><TD align=right>������ <INPUT type=text maxlength=3 value='"+res.txt_len_first+"' id=txt_len_first>&nbsp;&nbsp;��������:";
		HTML+="<TD><INPUT type=text maxlength=3 value='"+res.txt_cena_first+"' id=txt_cena_first> ���.";
		HTML+="<TR><TD>����������� <INPUT type=text maxlength=3 value='"+res.txt_len_next+"' id=txt_len_next>&nbsp;&nbsp;��������:";
		HTML+="<TD><INPUT type=text maxlength=3 value='"+res.txt_cena_next+"' id=txt_cena_next> ���.";
		HTML+="<TR><TD colspan=2 align=center id=info>";
		HTML+="<INPUT type=hidden value='"+res.txt_len_first+"'		id=txt_len_first_prev>";
		HTML+="<INPUT type=hidden value='"+res.txt_cena_first+"'	id=txt_cena_first_prev>";
		HTML+="<INPUT type=hidden value='"+res.txt_len_next+"'		id=txt_len_next_prev>";
		HTML+="<INPUT type=hidden value='"+res.txt_cena_next+"'	id=txt_cena_next_prev>";
		HTML+="</TABLE>";
		$("#table").html(HTML);
		$("#obLen INPUT").keyup(function(){
			$("#obLen #info").html('');
			var VAL=$(this).val();
			var reg=/^[0-9]+$/;
			if(!reg.exec(VAL) && VAL)
				$("#obLen #info").html("<SPAN class=red>������������ ���� ��������.<BR>����������� �����.</SPAN>");
			});
		$("#obLen INPUT").blur(function(){
			var INF=$("#obLen #info");
			var ATTR=$(this).attr('id');
			var VAL=$(this).val();
			var reg=/^[0-9]+$/;
			if(!reg.exec(VAL))
				INF.html("<SPAN class=red>������������ ���� ��������.<BR>���������� ����������.</SPAN>");
			else
				if($("#"+ATTR+"_prev").val()!=VAL)
					{
					INF.html("<SPAN style=color:#AA0>����������...<IMG src=/img/upload.gif></SPAN>");
					$.getJSON("/gazeta/setup/ob_len/AjaxObLenSave.php?"+$("#VALUES").val()+"&name="+ATTR+"&value="+VAL,function(){
						INF.html("<B style=color:#090>���������!</B>");
						INF.find("B").delay(2000).fadeOut(500);
						});
					}

			});
		frameBodyHeightSet();
		});
	}











// �������������� ��������� ����������
function setupObDop()
	{
	var HTML="<DIV id=obDop>";
	HTML+="<DIV class=headName>��������� �������������� ���������� ����������</DIV>";
	HTML+="<A href='javascript:' onclick=paramAdd();>�������� ����� ��������</A>";
	HTML+="<DIV id=spisok></DIV>";
	HTML+="</DIV>";
	$("#edit").html(HTML);
	$.getJSON("/gazeta/setup/ob_dop/AjaxObDopGet.php?"+$("#VALUES").val(),function(res){
		var HTML="<TABLE cellpadding=0 cellspacing=0 class=tabSpisok><TR><TH>������������<TH>���������<TH>���������";
		if(res!=null)
			for(var n=0;n<res.length;n++)
				{
				HTML+="<TR>";
				HTML+="<TD id=name"+res[n].id+">"+res[n].name;
				HTML+="<TD align=center id=cena"+res[n].id+">"+res[n].cena;
				HTML+="<TD align=center><DIV class=img_edit onclick=paramEdit("+res[n].id+");></DIV><DIV class=img_del onclick=paramDel("+res[n].id+");>";
				}
		HTML+="</TABLE>";
		$("#spisok").html(HTML);
		frameBodyHeightSet();
		});

	}

function paramAdd()
	{
	HTML="<TABLE cellpadding=0 cellspacing=7>";
	HTML+="<TR><TD class=tdAbout>������������:<TD id=par><INPUT type=text id=name style=width:200px; maxlength=50>";
	HTML+="<TR><TD class=tdAbout>���������:<TD id=cn><INPUT type=text id=cena style=width:30px; maxlength=3> ���.";
	HTML+="</TABLE>";
	dialogShow({
		top:100,
		head:'�������� ������ ���������',
		content:HTML,
		submit:function(){
			if(!$("#name").val()) { $("#par").alertShow({txt:"<SPAN class=red>�� ������� ������������.</SPAN>",top:-42,left:-8}); $("#name").focus(); }
			else
				{
				var CENA=$("#cena").val();
				var reg=/^[0-9]+$/;
				if(!reg.exec(CENA)) { $("#cn").alertShow({txt:"<SPAN class=red>�� ��������� ������� ���������.</SPAN>",top:-43,left:-15}); $("#cena").focus(); }
				else
					{
					$("#butDialog").butProcess();
					$.post("/gazeta/setup/ob_dop/AjaxObDopAdd.php?"+$("#VALUES").val(),{name:$("#name").val(),cena:CENA},function(){
						dialogHide();
						setupObDop();
						vkMsgOk("�������� ������� �����������!");
						},'json');
					}
				}
			},
		focus:'#name'
		});
	}

function paramEdit(ID)
	{
	HTML="<TABLE cellpadding=0 cellspacing=7>";
	HTML+="<TR><TD class=tdAbout>������������:<TD id=par><INPUT type=text id=name style=width:200px; maxlength=50 value='"+$("#name"+ID).html()+"'>";
	HTML+="<TR><TD class=tdAbout>���������:<TD id=cn><INPUT type=text id=cena style=width:30px; maxlength=3 value='"+$("#cena"+ID).html()+"'> ���.";
	HTML+="</TABLE>";
	dialogShow({
		top:100,
		head:'�������������� ���������',
		butSubmit:'���������',
		content:HTML,
		submit:function(){
			if(!$("#name").val()) { $("#par").alertShow({txt:"<SPAN class=red>�� ������� ������������.</SPAN>",top:-42,left:-8}); $("#name").focus(); }
			else
				{
				var CENA=$("#cena").val();
				var reg=/^[0-9]+$/;
				if(!reg.exec(CENA)) { $("#cn").alertShow({txt:"<SPAN class=red>�� ��������� ������� ���������.</SPAN>",top:-43,left:-15}); $("#cena").focus(); }
				else
					{
					$("#butDialog").butProcess();
					$.post("/gazeta/setup/ob_dop/AjaxObDopEdit.php?"+$("#VALUES").val(),{id:ID,name:$("#name").val(),cena:CENA},function(){
						dialogHide();
						setupObDop();
						vkMsgOk("�������������� ������� �����������!");
						},'json');
					}
				}
			}
		});
	}

function paramDel(ID)
	{
	dialogShow({
		width:330,
		top:100,
		head:'��������',
		butSubmit:'�������',
		content:"<CENTER>����������� �������� ��������� <B>"+$("#name"+ID).html()+"</B>.</CENTER>",
		submit:function(){
			$("#butDialog").butProcess();
			$.post("/gazeta/setup/ob_dop/AjaxObDopDel.php?"+$("#VALUES").val(),{id:ID},function(res){
				dialogHide();
				setupObDop();
				vkMsgOk("�������� ������� �����������!");
				},'json');
			}
		});
	}















// ���������� ������� �����������
function setupAccess()
	{
	var HTML="<DIV id=access>";
	HTML+="<DIV class=headName>���������� ������ ����������</DIV>";
	HTML+="<TABLE cellpadding=0 cellspacing=7><TR><TD class=tdAbout>������ �� ��������<BR>������������ ��������� ��� ��� ID:";
	HTML+="<TD id=fi><INPUT type=text id=find_input>";
	HTML+="<TD id=but><DIV class=vkButton><BUTTON onclick=accessUserGet(); id=acBut>�����</BUTTON></DIV></TABLE>";
	HTML+="<DIV id=userFind></DIV>";

	HTML+="<DIV class='headName top30'>������ ����������� ������</DIV>";
	HTML+="<DIV id=spisok></DIV>";
	HTML+="</DIV>";
	$("#edit").html(HTML);
	$("#find_input").focus();
	accessUserSpisok();
	}


function vkUserTest(VAL)
	{
	if(VAL)
		{
		var reg = /^[0-9]*$/i;
		if(reg.exec(VAL)!=null && VAL>0) return VAL;		// ���� ������� ����� � ��� ������ 0, �� �� ��
		else
			{
			var arr=("_"+VAL).split(/id/);
			if(reg.exec(arr[1])!=null && arr[1]>0) return arr[1];
			else
				{
				reg = /\//;
				if(reg.exec(VAL)!=null)
					{
					arr=VAL.split(/\//);
					if(typeof(arr[3])=='undefined')
						if(typeof(arr[2])=='undefined') return '';
						else return arr[2];
					else return arr[3];
					}
				else
					{
					reg=/^[0-9a-zA-Z_]*$/i;
					if(reg.exec(VAL)!=null) return VAL;
					else return'';
					}
				}
			}
		}
	else return '';
	}

function accessUserGet()
	{
	var UID=vkUserTest($("#find_input").val());
	if(UID)
		{
		$("#acBut").butProcess();
		VK.api('users.get',{uids:UID,name_case:'acc',fields:'photo'},function(data){
			accessUserShow(data.response[0]);
			$("#but").html("<DIV class=vkButton><BUTTON onclick=accessUserGet(); id=acBut>�����</BUTTON></DIV>");
			});
		}
	else
		{
		$("#fi").alertShow({txt:"<SPAN class=red>�� ��������� ������� ������ �� ��������.</SPAN>",top:-43,left:-2});
		$("#find_input").focus();
		}
	}




function accessUserShow(res)
	{
	var HTML="<DIV class=userShow><TABLE cellpadding=0 cellspacing=8>";
	HTML+="<TR><TD><A href='http://vk.com/id"+res.uid+"' target=_blank><IMG src="+res.photo+"></A>";
	HTML+="<TD>�� ������������� �������� � ���������� <A href='http://vk.com/id"+res.uid+"' target=_blank>"+res.first_name+" "+res.last_name+"</A> ?";
	HTML+="<DIV class=buttons><DIV class=vkButton><BUTTON onclick=accessUserAdd(this);>��������</BUTTON></DIV>";
	HTML+="<DIV class=vkCancel><BUTTON onclick=\"$('#userFind .userShow').slideUp(200,frameBodyHeightSet);\">������</BUTTON></DIV></DIV>";
	HTML+="</TABLE>";
	HTML+="<INPUT type=hidden id=uid value="+res.uid+">";
	HTML+="</DIV>";
	$("#userFind").html(HTML);
	frameBodyHeightSet();
	}

function accessUserAdd(OBJ)
	{
	$(OBJ).butProcess();
	$.post("/gazeta/setup/access/AjaxWorkerAdd.php?"+$("#VALUES").val(),{uid:$("#uid").val()},function(){
		$('#userFind').html('');
		$("#find_input").val('');
		accessUserSpisok();
		});
	}



function accessUserSpisok()
	{
	$.getJSON("/gazeta/setup/access/AjaxWorkerSpisok.php?"+$("#VALUES").val(),function(res){
		var HTML='';
		if(res[0].count>0)
			for(var n=0;n<res[0].count;n++)
				{
				HTML+="<DIV class=userShow id=user"+res[n].viewer_id+"><TABLE cellpadding=0 cellspacing=8>";
				HTML+="<TR><TD><A href='http://vk.com/id"+res[n].viewer_id+"' target=_blank><IMG src="+res[n].photo+"></A>";
				HTML+="<TD width=330><A href='http://vk.com/id"+res[n].viewer_id+"' target=_blank id=name"+res[n].viewer_id+">"+res[n].first_name+" "+res[n].last_name+"</A>";
				if(res[n].admin==1) HTML+="<DIV class=admin>�������������</DIV>";
				HTML+="<DIV class=dtime>"+res[n].dtime_add+"</DIV>";
				if(res[n].admin==0) HTML+="<TD><A href='javascript:' onclick=accessUserDel("+res[n].viewer_id+");>�������</A>";
				HTML+="</TABLE>";
				HTML+="</DIV>";
				}
		$("#spisok").html(HTML);
		frameBodyHeightSet();
		});
	}

function accessUserDel(ID)
	{
	dialogShow({
		width:240,
		top:100,
		head:'��������',
		butSubmit:'�������',
		content:"<CENTER>����������� �������� ���������� <B>"+$("#name"+ID).html()+"</B>.</CENTER>",
		submit:function(){
			$("#butDialog").butProcess();
			$.post("/gazeta/setup/access/AjaxWorkerDel.php?"+$("#VALUES").val(),{viewer_id:ID},function(res){
				dialogHide();
				vkMsgOk("�������� ������� �����������!");
				$("#user"+ID).remove();
				frameBodyHeightSet();
				},'json');
			}
		});
	}

