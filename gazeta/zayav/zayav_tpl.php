<?php
include('incHeader.php');
$spisok=$VK->QueryRowArray("select distinct(substr(day_public,1,4)) from gazeta_nomer order by day_public");
foreach($spisok as $sp)
	$year.="{uid:".$sp[0].",title:'".$sp[0]."'},";

$gnMin=$VK->QRow("select min(general_nomer) from gazeta_nomer where day_print>='".strftime('%Y-%m-%d',time())."'");
?>
<SCRIPT LANGUAGE="JavaScript">
$(document).ready(function(){
	$("#category").vkSel({
		width:147,
		title0:'Категория не указана',
		spisok:zayavCategoryVk,
		func:function(ID){
			if(ID==1)
				{
				$("#type_gaz").myCheck({name:"Газетный вариант",func:zayavSpisokGet});
				$("#check_type_gaz").after("<A href='javascript:' class=word>Скачать в формате Word</A>");
				$(".word").click(function(){ location.href="/gazeta/zayav/PrintWordOb.php?<?php echo $VALUES; ?>&gn="+$("#gazeta_nomer").val(); });
				}
			else { $("#check_type_gaz").remove(); $(".word").remove(); }
			zayavSpisokGet();
			}
		});

	$("#vkSel_category").css('margin-bottom','5px');

	$("#year").vkSel({
		width:147,
		title0:'Год не указан',
		spisok:[<?php echo substr($year,0,strlen($year)-1); ?>],
		func:function(YEAR){
			if(YEAR>0) gazetaNomerGet(YEAR);
			else $("#vkSel_gazeta_nomer").remove();
			$("#gazeta_nomer").val(0);
			zayavSpisokGet();
			}
		});
	
	if($("#year").val()>0) gazetaNomerGet($("#year").val());

	$("#fastFind").topSearch({
		txt:'Быстрый поиск...',
		enter:1,
		func:function(INP)
			{
			if(INP)
				$("#nofast").hide();
			else $("#nofast").show();
			zayavSpisokGet();
			}
		});


	zayavSpisokGet();

	VK.callMethod('setLocation','zayav');
	});



function gazetaNomerGet(YEAR)
	{
	$("#gazeta_nomer").vkSel({
		width:147,
		title0:'Номер не указан',
		url:"/gazeta/zayav/AjaxGNGet.php?<?php echo $VALUES; ?>&year="+YEAR,
		func:zayavSpisokGet
		});
	$("#vkSel_gazeta_nomer").css('margin-top','4px');
	}



function zayavSpisokGet(OBJ)
	{
	$("#findResult IMG").remove();
	$("#findResult").append("<IMG src=/img/upload.gif>");

	if($("#type_gaz").val()==1) obSpisokGet();
	else
		{
		var OBJ = $.extend({
			page:1,
			view:$("#spisok"),
			},OBJ);

		var URL="&page="+OBJ.page;
		URL+="&category="+$("#category").val();
		URL+="&gazeta_nomer="+$("#gazeta_nomer").val();
		var FAST=$("#fastFind_input").val(); if(FAST) URL+="&fast="+encodeURIComponent(FAST);

		$.getJSON("/gazeta/zayav/AjaxZayavSpisok.php?<?php echo $VALUES; ?>"+URL,function(data){
			if(data[0].count>0)
				{
				var HTML='';
				for(var n=1;n<data.length;n++)
					{
					HTML+="<DIV class=zayavUnit>";
						HTML+="<H1><EM>"+data[n].dtime+"</EM><A href='<?php echo $URL; ?>&my_page=zayavView&id="+data[n].id+"'>"+data[n].cat_name+" №"+data[n].id+"</A></H1>";
		
						HTML+="<TABLE cellpadding=0 cellspacing=0><TR><TD valign=top>";
		
							HTML+="<TABLE cellpadding=0 cellspacing=4>";
							if(data[n].client_id>0) HTML+="<TR><TD class=tdAbout>Клиент:<TD><A HREF='<?php echo $URL; ?>&my_page=clientInfo&id="+data[n].client_id+"'>"+data[n].fio+"</A>";
							if(data[n].cat_id==1) HTML+="<TR><TD class=tdAbout>Рубрика:<TD>"+data[n].rub+(data[n].podrub?"<SPAN class=ug>»</SPAN>"+data[n].podrub:'');
							if(data[n].cat_id==1) HTML+="<TR><TD class=tdAbout valign=top>Текст:<TD><DIV class=txt>"+data[n].txt+"</DIV>";
							if(data[n].ob_dop) HTML+="<TR><TD class=tdAbout>Доп. параметр:<TD>"+data[n].ob_dop;
							if(data[n].cat_id==2) HTML+="<TR><TD class=tdAbout>Размер:<TD>"+data[n].size_x+" x "+data[n].size_y+" = "+data[n].kv_sm;
							if(data[n].telefon) HTML+="<TR><TD class=tdAbout>Телефон:<TD>"+data[n].telefon;
							if(data[n].adres) HTML+="<TR><TD class=tdAbout>Адрес:<TD>"+data[n].adres;
							HTML+="<TR><TD class=tdAbout>Стоимость:<TD><B>"+data[n].summa+"</B> руб."+(data[n].summa_manual==1?'<SPAN class=manual>(указана вручную)</SPAN>':'');
							HTML+="</TABLE>";

						if(data[n].file) HTML+="<TD class=image><IMG src=/files/images/"+data[n].file+"s.jpg onclick=fotoShow('"+data[n].file+"');>";

						HTML+="</TABLE>";
					HTML+="</DIV>";
					}
				if(data[0].page>0) HTML+="<DIV><DIV id=ajaxNext onclick=zayavNext("+data[0].page+");>Следующие 20 заявок</DIV></DIV>";
				$("#findResult").html(data[0].result);
				OBJ.view.html(HTML);
				}
			else
				{
				$("#findResult").html("Запрос не дал результатов.");
				OBJ.view.html("<DIV class=findEmpty>Запрос не дал результатов.</DIV>");
				}

			frameBodyHeightSet();
			});	
		}
	}


function zayavNext(P)
	{
	$("#ajaxNext").css("padding","10px 0px 9px 0px").html("<IMG SRC=/img/upload.gif>");
	zayavSpisokGet({page:P,view:$("#ajaxNext").parent()});
	}

function obSpisokGet()
	{
	var URL="&gn="+$("#gazeta_nomer").val();
	$.ajax({
		url:"/gazeta/zayav/AjaxObSpisok.php?<?php echo $VALUES; ?>"+URL,
		dataType:'json',
		success:function(data){
			$("#findResult").html(data.result);
			$("#spisok").html("<DIV id=obSpisok>"+data.html+"</DIV>");
			frameBodyHeightSet();
			}
		});

	}
</SCRIPT>

<?php $mLink2='Sel'; include 'gazeta/mainLinks.php'; ?>

<DIV id=findResult>&nbsp;</DIV>

<TABLE cellpadding=0 cellspacing=0 id=zayav>
<TR>
	<TD id=spisok>&nbsp;
	<TD id=cond>
		<DIV id=buttonCreate><A HREF='javascript:' onclick="location.href='<?php echo $URL; ?>&my_page=zayavAdd';">Новая заявка</A></DIV>
		<DIV id=fastFind></DIV>
		
		<DIV id=nofast>
			<BR><BR>
			<DIV class=findName>Категория</DIV><INPUT TYPE=hidden id=category value=0>
			<INPUT TYPE=hidden id=type_gaz value=0>
			<BR>
			<DIV class=findName>Номер газеты</DIV><INPUT TYPE=hidden id=year value=<?php echo strftime("%Y",time()); ?>>
			<INPUT TYPE=hidden id=gazeta_nomer value=<?php echo $gnMin; ?>><BR>
		</DIV>


</TABLE>
<?php include('incFooter.php'); ?>



