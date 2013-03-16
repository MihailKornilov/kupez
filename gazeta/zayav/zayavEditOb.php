<?php
$txtLen=$VK->QueryObjectOne("select txt_len_first,txt_len_next,txt_cena_first,txt_cena_next from setup_global limit 1");
?>
<SCRIPT type="text/javascript" src="/gazeta/zayav/zayavAEV.js?<?php echo $G->script_style; ?>"></SCRIPT>
<SCRIPT type="text/javascript" src="/include/clientAdd/clientAdd.js?<?php echo $G->script_style; ?>"></SCRIPT>
<SCRIPT type="text/javascript">
$(document).ready(function(){
	if($("#client_id").length>0) $("#client_id").clientSel();
	
	$('#rubrika').vkSel({
		width:120,
		spisok:<?php echo vkSelGetJson('select id,name from setup_rubrika order by sort'); ?>,
		func:function(){ podRubVkSel(0); }
		});

	podRubVkSel(<?php echo $zayav->podrubrika; ?>);

	$('#txt').autosize().keyup(calcSummaOb);

	$("#nomer").gnGet({
		zayav_id:<?php echo $zayav->id; ?>,
		func:calcSummaOb
		});
	
	$("#summa").keyup(function(){
		var reg=/^[0-9.]+$/;
		if(!reg.exec($(this).val()))
			$("#zMsg").alertShow({txt:"<DIV class=red>Не корректно введено сумма.<BR>Используйте цифры и точку для дроби.</DIV>",top:-110,left:180});
		else calcSummaOb();
		});

	$("#summa_manual").myCheck({func:manualCkeck});
	manualCkeck(0);
	});


function manualCkeck(FUN)
	{
	if($("#summa_manual").val()==1)
		$("#summa").css('background-color','#FF8').removeAttr('readonly').focus();
	else
		{
		$("#summa").css('background-color','#FFF').attr('readonly',true);
		if(FUN!=0) calcSummaOb();
		}
	}

function zayavEditGo()
	{
	var GO=1;
	if(!$("#txt").val()) { MSG="Введите текст объявления"; GO=0; }
	else
		if(!$("#telefon").val() && !$("#adres").val()) { MSG="Укажите контактный телефон или адрес клиента"; GO=0; }

	if(GO==0) $("#zMsg").alertShow({txt:"<DIV class=red>"+MSG+"</DIV>",top:-44,left:155});
	else
		{
		document.FormZayav.action='<?php $URL."&my_page=zayav_Edit&id=".$zayav->id; ?>';
		document.FormZayav.enctype='';
		document.FormZayav.target='';
		document.FormZayav.submit();
		}

	}
</SCRIPT>

<INPUT type=hidden id=txt_len_first value="<?php echo $txtLen->txt_len_first; ?>">
<INPUT type=hidden id=txt_cena_first value="<?php echo $txtLen->txt_cena_first; ?>">
<INPUT type=hidden id=txt_len_next value="<?php echo $txtLen->txt_len_next; ?>">
<INPUT type=hidden id=txt_cena_next value="<?php echo $txtLen->txt_cena_next; ?>">

<FORM method=post action="/gazeta/zayav/fileUpload.php?<?php echo $VALUES; ?>" name=FormZayav enctype=multipart/form-data target=uploadFrame>
	<DIV class=headName><?php echo $zayavCategory[$zayav->category]; ?> №<?php echo $zayav->id; ?> - редактирование</DIV>
	<TABLE cellpadding=0 cellspacing=8 class=tabTxtEdit>
	<TR><TD class=tdAbout>Клиент:<TD><?php echo $zayav->client_id>0?$client:"<INPUT type=hidden id=client_id name=client_id>"; ?>
	<TR><TD class=tdAbout>Рубрика:<TD><?php echo $rubrikaText; ?>
																	<TABLE cellpadding=0 cellspacing=0>
																		<TR><TD><INPUT type=hidden id=rubrika name=rubrika value=<?php echo $zayav->rubrika; ?>>
																		<TD style=padding-left:3px;><INPUT type=hidden id=podrubrika name=podrubrika>
																	</TABLE>
	<TR><TD class=tdAbout valign=top>Текст:<TD><TEXTAREA name=txt class=txarea id=txt><?php echo $zayav->txt; ?></TEXTAREA><DIV id=txtCount></DIV>
	<TR><TD class='tdAbout top5' valign=top>Загрузить изображение:<TD colspan=2 id=tdUpload>
	<TR><TD class=tdAbout>Контактный телефон:<TD><INPUT TYPE=text NAME=telefon id=telefon value="<?php echo $zayav->telefon; ?>" maxlength=200>
	<TR><TD class=tdAbout>Адрес:<TD><INPUT TYPE=text NAME=adres id=adres value="<?php echo $zayav->adres; ?>" maxlength=200>
	<TR><TD class=tdAbout colspan=2>Номера выпуска:
	<TR><TD id=nomer colspan=2>
	<TR><TD class=tdAbout>Указать стоимость вручную:<TD><INPUT TYPE=hidden id=summa_manual name=summa_manual value=<?php echo $zayav->summa_manual; ?>>
	<TR><TD class=tdAbout>Общая стоимость:<TD><?php echo $prevSumShow; ?><INPUT TYPE=text NAME=summa id=summa readonly value=<?php echo round($zayav->summa-$prevSum,2); ?>> руб.
	</TABLE>
	<INPUT TYPE=hidden NAME=file id=file value='<?php echo $zayav->file; ?>'>
	<INPUT type=hidden name=zayav_id_txt value=<?php echo $zayav->id; ?>>
</FORM>

<DIV id=zMsg></DIV>
<DIV class=vkButton><BUTTON onclick=zayavEditGo();>Сохранить</BUTTON></DIV><DIV class=vkCancel><BUTTON onclick="location.href='<?php echo $URL."&my_page=zayavView&id=".$zayav->id; ?>'">Отмена</BUTTON></DIV>
