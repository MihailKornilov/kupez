<SCRIPT type="text/javascript" src="/gazeta/zayav/zayavAEV.js?0"></SCRIPT>
<SCRIPT type="text/javascript">
$(document).ready(function(){
	$("#nomer").gnGet({category:<?php echo $zayav->category; ?>,zayav_id:<?php echo $zayav->id; ?>,func:calcSummaPozSt});
	$("#summa").keyup(function(){
		var reg=/^[0-9.]+$/;
		if(!reg.exec($(this).val()))
			$("#zMsg").alertShow({txt:"<DIV class=red>Не корректно введено сумма.<BR>Используйте цифры и точку для дроби.</DIV>",top:-110,left:180});
		else calcSummaPozSt();
		});
	});

function zayavEditGo()
	{
	if(!$("#gn_input").val()) $("#zMsg").alertShow({txt:"<DIV class=red>Необходимо выбрать минимум один номер выпуска</DIV>",top:-44,left:155});
	else
		{
		document.FormZayav.action='<?php $URL."&my_page=zayav_Edit&id=".$zayav->id; ?>';
		document.FormZayav.enctype='';
		document.FormZayav.target='';
		document.FormZayav.submit();
		}
	}
</SCRIPT>
<FORM method=post action="/gazeta/zayav/fileUpload.php?<?php echo $VALUES; ?>" name=FormZayav enctype=multipart/form-data target=uploadFrame>
	<DIV class=headName><?php echo $zayavCategory[$zayav->category]; ?> №<?php echo $zayav->id; ?> - редактирование</DIV>
	<TABLE cellpadding=0 cellspacing=8 class=tabTxtEdit>
	<TR><TD class=tdAbout>Клиент:<TD><?php echo $client; ?>
	<TR><TD class='tdAbout top5' valign=top>Загрузить изображение:<TD colspan=2 id=tdUpload>
	<TR><TD class=tdAbout colspan=2>Номера выпуска:
	<TR><TD id=nomer colspan=2>
	<TR><TD class=tdAbout>Общая стоимость:<TD><INPUT TYPE=text NAME=summa id=summa value=<?php echo round($zayav->summa,2); ?>> руб.
	</TABLE>
	<INPUT TYPE=hidden NAME=file id=file value='<?php echo $zayav->file; ?>'>
	<INPUT type=hidden name=zayav_id_poz_st value=<?php echo $zayav->id; ?>>
</FORM>

<DIV id=zMsg></DIV>
<DIV class=vkButton><BUTTON onclick=zayavEditGo();>Сохранить</BUTTON></DIV><DIV class=vkCancel><BUTTON onclick="location.href='<?php echo $URL."&my_page=zayavView&id=".$zayav->id; ?>'">Отмена</BUTTON></DIV>
