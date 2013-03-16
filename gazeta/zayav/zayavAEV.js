/* ИСПОЛЬЗУЕТСЯ ПРИ ДОБАВЛЕНИИ И РЕДАКТИРОВАНИИ ЗАЯВКИ */








var DOPCENA, SKIDKA;
$.fn.gnGet = function(OBJ){
	var OBJ = $.extend({
		gn_count:60,
		gn_show:4, // количество номеров, которые показываются изначально, а также отступ от уже выбранных
		gn_add:8,	// количество номеров, добавляющихся к показу
		category:1,
		zayav_id:0,
		func:''
		},OBJ);
	
	var PIX=21; // высота номера выпуска в пикселях
	var CHSHOW=OBJ.gn_show*PIX;
	var CHADD=OBJ.gn_add*PIX;
	var CHMAX=0;

	var HTML='<DIV id=gnGet>';
		HTML+="<TABLE cellpadding=0 cellspacing=0>";
		HTML+="<TR><TD><DIV id=dopMenu>";
		HTML+="<A HREF='javascript:' class=link val=4><I></I><B></B><DIV>Месяц</DIV><B></B><I></I></A>";
		HTML+="<A HREF='javascript:' class=link val=13><I></I><B></B><DIV>3 месяца</DIV><B></B><I></I></A>";
		HTML+="<A HREF='javascript:' class=link val=26><I></I><B></B><DIV>Полгода</DIV><B></B><I></I></A>";
		HTML+="<A HREF='javascript:' class=link val=52><I></I><B></B><DIV>Год</DIV><B></B><I></I></A>";
		HTML+="</DIV>";
		HTML+="<TD><DIV id=dopDef><IMG src=/img/upload.gif></DIV></TABLE>";

		HTML+="<TABLE cellpadding=0 cellspacing=0>";
		HTML+="<TR><TD class=left><DIV id=num></DIV>";
		HTML+="<TD><DIV class=content></DIV><DIV class=darr>&darr; &darr; &darr;</DIV>";
		HTML+="</TABLE>";
	HTML+="<INPUT type=hidden id=gn_input_prev>";
	HTML+="<INPUT type=hidden id=gn_input name=gn_input>";
	HTML+="</DIV>";
	$(this).html(HTML).find(".content").css('height',CHSHOW+'px');

	var URL="&gn_count="+OBJ.gn_count;
	URL+="&zayav_id="+OBJ.zayav_id;
	URL+="&category="+OBJ.category;
	$.getJSON("/include/gnGet/AjaxGNspisok.php?"+$("#VALUES").val()+URL,function(res){
		DOPCENA=res.dop_cena;
		SKIDKA=res.skidka;
		HTML='';
		// создание списка опубликованных номеров, если есть
		for(var n=0;n<res.pub[0].count;n++)
			{
			HTML+="<TABLE cellpadding=0 cellspacing=0><TR><TD>";

				HTML+="<TABLE cellpadding=0 cellspacing=0 class=viewed>";
				HTML+="<TR><TD class=td><B>"+res.pub[n].week_nomer+"</B><EM>("+res.pub[n].general_nomer+")</EM>";
				HTML+="<TD align=right class=td><EM>выход</EM> "+res.pub[n].day_public;
				HTML+="<TD align=right><INPUT type=text class=inpCena readonly value="+res.pub[n].summa+">";
				HTML+="</TABLE>";

				HTML+="<TD class=vdop>"+res.pub[n].dop_name+"</DIV>";

			HTML+="</TABLE>";
			}
		
		var S=0;
		// создание списка неопубликованных номеров
		for(var n=0;n<res.gaz.length;n++)
			{
			HTML+="<TABLE cellpadding=0 cellspacing=0><TR><TD>";

				HTML+="<TABLE cellpadding=0 cellspacing=0 class='tab"+(res.gaz[n].sel==1?' tabsel':'')+"'>";
				HTML+="<TR><TD class=td><B>"+res.gaz[n].week_nomer+"</B><EM>(<TT>"+res.gaz[n].general_nomer+"</TT>)</EM>";
				HTML+="<TD align=right class=td><EM>выход</EM> "+res.gaz[n].day_public;
				HTML+="<TD align=right><INPUT type=text class=inpCena readonly id=gn_sum_"+res.gaz[n].general_nomer+" maxlength=6>";
				HTML+="<INPUT type=hidden class=sel value="+res.gaz[n].sel+">";
				HTML+="</TABLE>";

				HTML+="<TD><DIV class=dop id=dop"+res.gaz[n].general_nomer+(res.gaz[n].sel==1?' style=display:block;':'')+"></DIV>";

			HTML+="</TABLE>";
			if(res.gaz[n].sel==1) S=n;
			}
		
		S+=res.pub[0].count;
		if(S>=OBJ.gn_show) CHSHOW=(S*1+5)*PIX;
		$("#gnGet .content").css('height',CHSHOW+'px').html(HTML);
		$("#gn_input_prev").val(res.gn_prev);
		$("#dopDef").html('').hide();
		CHMAX=(res.pub[0].count+res.gaz.length)*PIX;

		// расставляем дополнительные параметры
		if(res.dop[0].count>0)
			for(var n=0;n<res.gaz.length;n++)
				{
				$("#gnGet #dop"+res.gaz[n].general_nomer).linkMenu({
					grey0:1,
					selected:res.gaz[n].dop,
					spisok:res.dop,
					func:function(){ if(OBJ.func) OBJ.func(); }
					});
				}

		dopDef();
		selCalc();
		if(OBJ.func) OBJ.func();

		frameBodyHeightSet();

		// разворачиваем список номеров на количество OBJ.gn_add
		$("#gnGet .darr").click(function(){
			var H=$("#gnGet .content").css('height');
			var arr=H.split(/px/);
			H=arr[0]*1+CHADD*1;
			if(H>=CHMAX)
				{
				H=CHMAX;
				$(this).hide();
				}
			$("#gnGet .content").animate({'height':H+'px'},300,frameBodyHeightSet);
			});
	
		// нажатие на номер
		$("#gnGet .tab").click(function(){
			$("#gnGet .linkSel").attr('class','link');
			var INP=$(this).find('.sel');
			var VAL=INP.val();
			INP.val(VAL==0?1:0)
			if(VAL==0)
				$(this).addClass('tabsel').find('.inpCena').focus();
			else $(this).removeClass('tabsel').find('.inpCena').val('');
			var DOP=$(this).parent().next().find('.dop');
			if(VAL==1) DOP.hide(); else DOP.show();
			selCalc();
			if(OBJ.func) OBJ.func();
			});
		
		// выбор номеров на месяц, 3 месяца, полгода и год начиная сначала
		$("#gnGet .link").click(function(){
			var ATTR=$(this).attr('class');
			selClear();
			$("#gnGet .linkSel").attr('class','link');
			if(ATTR=='link')
				{
				$(this).attr('class','linkSel');
				var LEN=$(this).attr('val');
				var DOP=$("#gnGet .dop");
				var GN=$("#gnGet .tab");
				for(var n=0;n<LEN;n++)
					{
					GN.eq(n)
						.addClass('tabsel')
						.find(".sel").val(1);
					DOP.eq(n).show();
					}
				var H=(LEN*1+4+res.pub[0].count)*PIX;
				if(H>=CHMAX)
					{
					H=CHMAX;
					$("#gnGet .darr").hide();
					}
				else $("#gnGet .darr").show();
				$("#gnGet .content").animate({'height':H+'px'},300,frameBodyHeightSet);
				}
			else
				{
				$("#gnGet .content").animate({'height':CHSHOW+'px'},300,frameBodyHeightSet);
				$("#gnGet .darr").show();
				}
			selCalc();
			if(OBJ.func) OBJ.func();
			});
		

		// установка общей ссылки для дополнительных параметров
		function dopDef()
			{
			if(res.dop[0].count>0)
				$("#dopDef").linkMenu({
					name:'Установить всем...',
					grey0:1,
					spisok:res.dop,
					func:function(ID){
						dopDef();
						dopDefSet(ID);
						if(OBJ.func) OBJ.func();
						}
					});
			}

		// установка всем доплнительного параметра
		function dopDefSet(ID)
			{
			for(n=0;n<res.gaz.length;n++)
				$("#gnGet #dop"+res.gaz[n].general_nomer).linkMenuSet(ID);
			}

		// очистка всех выбранных номеров
		function selClear()
			{
			CHSHOW=(OBJ.gn_show+res.pub[0].count)*PIX;
			$("#gnGet .linkSel").attr('class','link');
			var GN=$("#gnGet .tab");
			for(var n=0;n<GN.length;n++)
				GN.eq(n)
					.removeClass('tabsel')
					.find('.inpCena').val('').end()
					.find(".sel").val(0);
			$("#gnGet .dop").hide();
			$("#dopDef").hide();
			if(res.dop[0].count>0) dopDefSet(0);
			}
		
		// подсчёт выбранных номеров и показ слева
		function selCalc()
			{
			var GNSEL=$("#gnGet .tabsel").length;
			if(GNSEL>0)
				{
				if(GNSEL>1) $("#dopDef").show(); else $("#dopDef").hide();
				var END='ов';
				if(Math.floor(GNSEL/10%10)!=1)
					switch(GNSEL%10)
						{
						case 1: END=''; break;
						case 2: END='а'; break;
						case 3: END='а'; break;
						case 4: END='а'; break;
						}
				$("#gnGet #num").html("Выбран"+(GNSEL%10!=1?'о':'')+" "+GNSEL+" номер"+END+"<A href='javascript:'>очистить</A>");
				$("#gnGet #num A").click(function(){
					selClear();
					$(this).parent().html('');
					$("#gnGet .content").animate({'height':CHSHOW+'px'},300,frameBodyHeightSet);
					if(OBJ.func) OBJ.func();
					});
				}
			else $("#gnGet #num").html('');
			}
		});
	}











function setZayav() {
	$("#summa_manual").myCheckVal(0);
	$("#summa").val(0).css('background-color','#FFF').attr('readonly',true);
	$("#sumSkidka").hide();
	$("#skidka_sum").val(0);
	var CAT=$("#category").val();
	var HTML='';
	$("#skidkaContent").html('');
	switch(CAT)
		{
		case '1':
			$("#manual_tab").show();
			HTML="<TABLE cellpadding=0 cellspacing=8>";
			HTML+="<TR><TD class=tdAbout>Рубрика:							<TD width=120><INPUT TYPE=hidden NAME=rubrika id=rubrika value="+$("#dub_rubrika").val()+"><TD><INPUT TYPE=hidden NAME=podrubrika id=podrubrika value=0>";
			HTML+="<TR><TD class=tdAbout valign=top>Текст:				<TD colspan=2><TEXTAREA name=txt id=txt>"+$("#dub_txt").val()+"</TEXTAREA><DIV id=txtCount></DIV>";
			HTML+="<TR><TD class='tdAbout top5' valign=top>Загрузить изображение:";
					HTML+="<TD colspan=2><INPUT TYPE=file NAME=file_name id=file_name onchange=fileSelected();>";
					HTML+="<IFRAME src='' name=uploadFrame scrolling=yes frameborder=1 style=display:none;></IFRAME>";
					HTML+="<INPUT TYPE=hidden NAME=file id=file>";
			HTML+="<TR><TD class=tdAbout>Контактный телефон:		<TD colspan=2><INPUT TYPE=text NAME=telefon id=telefon value='"+$("#dub_telefon").val()+"' maxlength=200>";
			HTML+="<TR><TD class=tdAbout>Адрес:								<TD colspan=2><INPUT TYPE=text NAME=adres id=adres value='"+$("#dub_adres").val()+"' maxlength=200>";
			HTML+="</TABLE>";
			$("#content").html(HTML);
			$("#rubrika").vkSel({
				width:120,
				title0:'Не указана',
				url:"/gazeta/zayav/AjaxRubrikaGet.php?"+$("#VALUES").val(),
				func:function(ID){
					if(ID>0) podRubVkSel(0);
					else
						{
						$("#podrubrika").val(0);
						$("#vkSel_podrubrika").remove();
						}
					}
				});
			$("#txt").autosize({callback:frameBodyHeightSet}).keyup(calcSummaOb);
			$("#nomer").gnGet({category:CAT,func:calcSummaOb});
			break;

		case '2':
			$("#manual_tab").show();
			HTML="<TABLE cellpadding=0 cellspacing=8>";
			HTML+="<TR><TD class=tdAbout>Размер изображения:";
			HTML+="<TD id=pn><INPUT TYPE=text NAME=size_x id=size_x maxlength=5 value='"+$("#dub_size_x").val()+"'>";
			HTML+="<B class=xb>x</B>";
			HTML+="<INPUT TYPE=text NAME=size_y id=size_y maxlength=5 value='"+$("#dub_size_y").val()+"'>";
			HTML+=" = <INPUT TYPE=text id=kv_sm readonly value='"+$("#dub_kv_sm").val()+"'> см<SUP>2</SUP>";
			HTML+="<TR><TD class='tdAbout top5' valign=top>Загрузить изображение:";
					HTML+="<TD><INPUT TYPE=file NAME=file_name id=file_name onchange=fileSelected();>";
					HTML+="<IFRAME src='' name=uploadFrame scrolling=no frameborder=1 style=display:none;></IFRAME>";
					HTML+="<INPUT TYPE=hidden NAME=file id=file>";
			HTML+="</TABLE>";
			$("#content").html(HTML);
			$("#skidkaContent").html("<TABLE cellpadding=0 cellspacing=8><TR><TD class=tdAbout>Скидка:<TD><INPUT TYPE=hidden NAME=skidka id=skidka value=0></TABLE>");
			$("#skidka").vkSel({width:90,title0:'без скидки',spisok:skidkaSpisok,func:calcSummaRek});
			$("#size_x").keyup(calcSummaRek);
			$("#size_y").keyup(calcSummaRek);
			$("#nomer").gnGet({category:CAT,func:calcSummaRek});
			break;

		default:
			$("#manual_tab").hide();
			$("#summa").css('background-color','#FF8').removeAttr('readonly');
			HTML="<TABLE cellpadding=0 cellspacing=8>";
			HTML+="<TR><TD class='tdAbout top5' valign=top>Загрузить изображение:";
					HTML+="<TD><INPUT TYPE=file NAME=file_name id=file_name onchange=fileSelected();>";
					HTML+="<IFRAME src='' name=uploadFrame scrolling=no frameborder=1 style=display:none;></IFRAME>";
					HTML+="<INPUT TYPE=hidden NAME=file id=file>";
			HTML+="</TABLE>";
			$("#content").html(HTML);
			$("#nomer").gnGet({category:CAT,func:calcSummaPozSt});
			break;
		}
	$("#dub_rubrika").val(0);
	$("#dub_podrubrika").val(0);
	$("#dub_txt").val('')
	$("#dub_telefon").val('')
	$("#dub_adres").val('')
	$("#dub_size_x").val('');
	$("#dub_size_y").val('');
	$("#dub_kv_sm").val('');
	}




// ВЫВОД СПИСКА ПОДРУБРИК
function podRubVkSel(ID)
	{
	$("#podrubrika").val(ID).vkSel({
		width:200,
		title0:'Подрубрика не указана',
		msg:'Подрубрика не указана',
		url:"/gazeta/zayav/AjaxPodRubrikaGet.php?"+$("#VALUES").val()+"&rubrika_id="+$("#rubrika").val(),
		funcAdd:podRubrikaAdd
		});
	}



// ДОБАВЛЕНИЕ НОВОЙ ПОДРУБРИКИ ПРИ НАЖАТИИ НА ПЛЮСИК
function podRubrikaAdd()
	{
	var RUBNAME=$("#vkSel_rubrika INPUT:first").val();
	var ID=$("#rubrika").val();
	HTML="<TABLE cellpadding=0 cellspacing=10>";
	HTML+="<TR><TD class=tdAbout>Рубрика:<TD>"+RUBNAME;
	HTML+="<TR><TD class=tdAbout>Наименование:<TD id=pn><INPUT type=text id=podrubrika_name style=width:200px;>";
	HTML+="</TABLE>";
	dialogShow({
		top:100,
		head:'Внесение новой подрубрики для '+RUBNAME,
		content:HTML,
		focus:'#podrubrika_name',
		submit:function(){
			if(!$("#podrubrika_name").val()) $("#pn").alertShow({txt:'<SPAN class=red>Не указано наименование.</SPAN>',top:-43,left:-3});
			else
				{
				$("#butDialog").butProcess();
				$.post("/gazeta/setup/podrubrika/AjaxPodRubrikaAdd.php?"+$("#VALUES").val(),{rubrika_id:ID,name:$("#podrubrika_name").val()},function(res){
					dialogHide();
					vkMsgOk("Новая подрубрика добавлена!");
					podRubVkSel(res.id);
					},'json');
				}
			}
		});
	}








function calcSummaOb()
	{
	var MANUAL=$("#summa_manual").val();
	if(MANUAL==0) $("#summa").val(0);
	var SUMMA=0;
	var TXT_SUM=0;
	var STR=document.FormZayav.txt.value.replace(/\./g,'');
	STR=STR.replace(/,/g,'');
	STR=STR.replace(/\//g,'');
	STR=STR.replace(/\"/g,'');
	STR=STR.replace(/( +)/g,' ');
	STR=STR.replace( /^\s+/g, '');
	STR=STR.replace( /\s+$/g, '');
	var LEN=STR.length;
	if(LEN>0)
		{
		var TXT_LEN_FIRST=$("#txt_len_first").val();
		var TXT_CENA_FIRST=$("#txt_cena_first").val();
		var TXT_LEN_NEXT=$("#txt_len_next").val();
		var TXT_CENA_NEXT=$("#txt_cena_next").val();
		TXT_SUM+=TXT_CENA_FIRST*1;
		var COUNT_PODR=''; // подробное расписывание длины объявления
		if(LEN>TXT_LEN_FIRST)
			{
			COUNT_PODR=' = ';
			var CEIL=Math.ceil((LEN-TXT_LEN_FIRST)/TXT_LEN_NEXT);
			COUNT_PODR+=TXT_LEN_FIRST;
			var LAST=LEN-TXT_LEN_FIRST-(CEIL-1)*TXT_LEN_NEXT;
			TXT_SUM+=CEIL*TXT_CENA_NEXT;
			if(TXT_LEN_NEXT==LAST) CEIL++;
			if(CEIL>1) COUNT_PODR+=" + "+TXT_LEN_NEXT;
			if(CEIL>2) COUNT_PODR+="x"+(CEIL-1);
			if(TXT_LEN_NEXT>LAST) COUNT_PODR+=" + "+LAST;
			}
		$("#txtCount").html("Длина: <B>"+LEN+"</B>"+COUNT_PODR+"<BR>Цена: <B>"+TXT_SUM+"</B> руб.<SPAN>(без учёта доп. параметров)</SPAN>");

		var PREV=$("#gn_input_prev").val();
		var FOUR=1; // счётчик: каждое четвёртое - бесплатно
		if(PREV)
			{
			var prevArr=PREV.split(/,/);
			for(var n=0;n<prevArr.length;n++)
				{
				var arr=prevArr[n].split(/:/);
			//	SUMMA+=arr[2]*1;
				if(FOUR==4) FOUR=0;
				FOUR++;
				}
			}

		var MAN_SUM=0;
		if(MANUAL==1)
			{
			var GNSEL=$("#gnGet .tabsel").length;
			GNSEL=Math.floor(GNSEL/4)*3+(GNSEL%4);
			MAN_SUM=Math.round(document.FormZayav.summa.value/GNSEL*100)/100;
			}

		var SP='';
		var GN=$("#gnGet .tab");
		for(var n=0;n<GN.length;n++)
			{
			var INP=GN.eq(n).find(".sel");
			if(INP.val()==1)
				{
				var NUM=GN.eq(n).find("TT:first").html();
				var DOP=$("#linkMenu_dop"+NUM).val();
				var SD=0;
				if(FOUR!=4)
					if(MANUAL==0)
						{
						SUMMA+=TXT_SUM;
						var CENA=DOPCENA[DOP];
						SUMMA+=CENA*1;
						SD=TXT_SUM+CENA*1;
						}
					else SD=MAN_SUM;
				else FOUR=0;
				$("#gn_sum_"+NUM).val(SD);
				SP+=","+NUM+":"+DOP+":"+SD;	
				FOUR++;
				}
			}
		if(PREV && SP) PREV+=",";
		$("#gn_input").val(PREV+SP.substring(1));

		if(MANUAL==0) document.FormZayav.summa.value=Math.round(SUMMA);
		}
	else
		{
		$("#txtCount").html('');
		$("#gnGet .inpCena").val(0);
		}
	}






function calcSummaRek()
	{
	var MANUAL=$("#summa_manual").val();
	if(MANUAL==0) $("#summa").val(0);
	$("#kv_sm").val('');
	$("#sumSkidka").hide();
	$("#skidka_sum").val(0);
	var X=document.FormZayav.size_x.value;
	if(X)
		{
		var ALERT="<SPAN class=red>Не корректно введено значение.<BR>Используйте цифры и точку для дроби.<BR>Значение указывается в сантиметрах.</SPAN>";
		var reg=/^[0-9.]+$/;
		if(!reg.exec(X)) $("#pn").alertShow({txt:ALERT,top:-69,left:-13});
		else
			{
			var Y=document.FormZayav.size_y.value;
			if(Y)
				{
				if(!reg.exec(Y)) $("#pn").alertShow({txt:ALERT,top:-69,left:45});
				else
					{
					var XY=Math.round(X*Y*100)/100;
					$("#kv_sm").val(XY);
					var SUMMA=0;

					var PREV=$("#gn_input_prev").val();
					if(PREV)
						{
						var prevArr=PREV.split(/,/);
						for(var n=0;n<prevArr.length;n++)
							{
							var arr=prevArr[n].split(/:/);
							SUMMA+=arr[2]*1;
							}
						}

					var MAN_SUM=0;
					if(MANUAL==1)
						{
						var GNSEL=$("#gnGet .tabsel").length;
						MAN_SUM=Math.round(document.FormZayav.summa.value/GNSEL*100)/100;
						}

					var SKID=$("#skidka").val();	// id скидки из INPUT
					var SK=SKIDKA[SKID];			// размер скидки в процентах
					var RSK=0;

					var SP='';
					var GN=$("#gnGet .tab");
					for(var n=0;n<GN.length;n++)
						{
						var INP=GN.eq(n).find(".sel");
						if(INP.val()==1)
							{
							var NUM=GN.eq(n).find("TT:first").html();
							var DOP=$("#linkMenu_dop"+NUM).val();
							var SD=0;
							if(MANUAL==0)
								{
								var CENA=Math.round(XY*DOPCENA[DOP]*100)/100;
								SUMMA+=CENA;
								if(SK>0) CENA=Math.round((CENA-CENA*SK/100)*100)/100;
								SD=CENA;
								}
							else SD=MAN_SUM;
							$("#gn_sum_"+NUM).val(SD);
							SP+=","+NUM+":"+DOP+":"+SD;	
							}
						}
					$("#gn_input").val(SP.substring(1));

					if($("#prev_sum").length>0) SUMMA-=$("#prev_sum").val();
					RSK=SUMMA*SK/100;		// размер скидки в рублях
					if(MANUAL==0) document.FormZayav.summa.value=Math.round((SUMMA-RSK)*100)/100;
					if(RSK>0)
						{
						RSK=Math.round(RSK*100)/100
						$("#sumSkidka").show().find("B").html(RSK);
						$("#skidka_sum").val(RSK);
						}
					}
				}
			}
		}
	}





function calcSummaPozSt()
	{
	var SUMMA=0;

	var PREV=$("#gn_input_prev").val();
	if(PREV)
		{
		var prevArr=PREV.split(/,/);
		for(var n=0;n<prevArr.length;n++)
			{
			var arr=prevArr[n].split(/:/);
			SUMMA+=arr[2]*1;
			}
		}

	var GNSEL=$("#gnGet .tabsel").length;
	var MAN_SUM=Math.round(document.FormZayav.summa.value/GNSEL*100)/100;

	var SP='';
	var GN=$("#gnGet .tab");
	for(var n=0;n<GN.length;n++)
		{
		var INP=GN.eq(n).find(".sel");
		if(INP.val()==1)
			{
			var NUM=GN.eq(n).find("TT:first").html();
			var DOP=$("#linkMenu_dop"+NUM).val();
			var SD=MAN_SUM;
			$("#gn_sum_"+NUM).val(MAN_SUM);
			SP+=","+NUM+":"+DOP+":"+MAN_SUM;	
			}
		}
	if(PREV && SP) PREV+=",";
	$("#gn_input").val(PREV+SP.substring(1));
	}






// ЗАГРУЗКА ФАЙЛА
function fileSelected()
	{
	$("#file_name").hide().after("<IMG src=/img/upload.gif>");
	setCookie('upload','process');
	timer=setInterval("fileUploadStart();",500);
	document.FormZayav.action='/gazeta/zayav/fileUpload.php?'+$("#VALUES").val();
	document.FormZayav.enctype='multipart/form-data';
	document.FormZayav.target='uploadFrame';
	document.FormZayav.submit();
	}

function fileUploadStart()
	{
	var COOKIE=getCookie("upload");
	if(COOKIE!='process')
		if(COOKIE!='error')
			{
			clearInterval(timer);
			$("#file_name")
				.next().remove('IMG')
				.end().after("<TABLE cellpadding=0 cellspacing=0 id=fileTab><TR><TD><IMG src=/files/images/"+COOKIE+"s.jpg onload=frameBodyHeightSet(); onclick=fotoShow('"+COOKIE+"');><TD valign=top><A href='javascript:' class=img_del onclick=fileDel();></A></TABLE>");
			$("#file").val(COOKIE);
			delCookie("upload");
			}
	}

function fileDel()
	{
	$("#file_name").after("<INPUT TYPE=file NAME=file_name id=file_name onchange=fileSelected();>").remove();
	$("#fileTab").remove();
	$("#file").val('');
	frameBodyHeightSet();
	}

