function clientAdd(FUNC)
	{
	var HTML="<TABLE cellpadding=0 cellspacing=6 class=clientAdd>";
	HTML+="<TR><TD class=tdAbout>���������:<TD><INPUT TYPE=hidden id=person value=1>";
	HTML+="<TR><TD class=tdAbout>�������� �����������:<TD><INPUT TYPE=text id=org_name class=input>";
	HTML+="<TR><TD class=tdAbout>���:<TD><INPUT TYPE=text id=fio class=input>";
	HTML+="<TR><TD class=tdAbout>��������:<TD><INPUT TYPE=text id=telefon class=input>";
	HTML+="<TR><TD class=tdAbout>�����:<TD id=ms><INPUT TYPE=text id=adres class=input>";
	HTML+="</TABLE>";
	dialogShow({
		width:440,
		head:"���������� �o���� �������",
		content:HTML,
		submit:function(){
			if(!$("#fio").val() && !$("#org_name").val())
				{
				$("#ms").alertShow({txt:"<DIV class=red>���������� ������� ��� �������<BR>���� �������� �����������.</DIV>",top:-3,left:-5});
				$("#org_name").focus();
				}
			else
				{
				$("#butDialog").butProcess();
				$.post("/include/clientAdd/AjaxClientAdd.php?"+$("#VALUES").val(),{
					person:$("#person").val(),
					org_name:$("#org_name").val(),
					fio:$("#fio").val(),
					telefon:$("#telefon").val(),
					adres:$("#adres").val()
					},function(res){ FUNC(res.id); },'json');
				}
			},
		focus:'#org_name'
		});
	$("#person").vkSel({
		width:180,
		url:"/include/clientAdd/AjaxPersonGet.php?"+$("#VALUES").val()
		});
	}







// ����� �������� �� ������ � ���������� ������. ����������� �� �������� ����� ������, � ������� ��������
$.fn.clientSel = function(OBJ){
	var OBJ = $.extend({
		width:240,
		func:''
		},OBJ);

	$("#client_id").vkSel({
		width:OBJ.width,
		msg:'������� ������� ������ �������...',
		url:"/include/clientAdd/AjaxClientSpisok.php?"+$("#VALUES").val()+"&limit=50",
		ro:0,
		nofind:'�������� �� �������',
		func:OBJ.func,
		funcAdd:function(){
			clientAdd(	function(id){
				$("#client_id")
					.val(id)
					.next()
					.find("INPUT:first")
					.val($("#fio").val())
					.removeClass('pusto');
				dialogHide();
				});
			}
		});
	}




