<?php
$prevSum=round($VK->QRow("select ifnull(sum(summa),0) from gazeta_nomer_pub where general_nomer<".$gnMin." and zayav_id=".$zayav->id),2);
$prevSumShow="<INPUT type=".($prevSum>0?'text':'hidden')." id=prev_sum value='".round($prevSum,2)."' readonly>".($prevSum>0?' + ':'')."";
?>
<SCRIPT type="text/javascript" src="/gazeta/zayav/zayavAEV.js?<?php echo $G->script_style; ?>"></SCRIPT>
<SCRIPT type="text/javascript">
$(document).ready(function(){
	$("#skidka").vkSel({
		width:90,
		title0:'Без скидки',
		spisok:<?php echo vkSelGetJson("select id,concat(razmer,'%') from setup_skidka order by razmer"); ?>,
		func:calcSummaRek
		});

	$("#size_x").keyup(calcSummaRek);
	$("#size_y").keyup(calcSummaRek);

	$("#nomer").gnGet({category:2,zayav_id:<?php echo $zayav->id; ?>,func:calcSummaRek});

	$("#summa").keyup(function(){
		var reg=/^[0-9.]+$/;
		if(!reg.exec($(this).val()))
			$("#zMsg").alertShow({txt:"<DIV class=red>Не корректно введено сумма.<BR>Используйте цифры и точку для дроби.</DIV>",top:-110,left:180});
		else calcSummaRek();
		});


	$("#summa_manual").myCheck({func:manualCkeck});
	manualCkeck(0);
	});


function manualCkeck(FUN)
	{
	if($("#summa_manual").val()==1)
		$("#summa").css('background-color','#FF8').removeAttr('readonly').focus();
	else
		$("#summa").css('background-color','#FFF').attr('readonly',true);
	if(FUN!=0) calcSummaRek();
	}







function zayavEditGo()
	{
	var GO=1;

	if(!$("#kv_sm").val()) { MSG="Не указан размер изображения"; GO=0; }
	else
		{
		var GNI=$("#gn_input").val();
		if(GNI)
			{
			var arr=GNI.split(/,/);
			for(var n=0;n<arr.length;n++)
				{
				var gnArr=arr[n].split(/:/);
				if(gnArr[1]==0) GO=0;
				}
			if(GO==0) MSG="Необходимо указать полосу у всех выбранных номеров";
			}
		else { MSG="Необходимо выбрать минимум один номер выпуска"; GO=0; }
		}

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
<FORM method=post action="/gazeta/zayav/fileUpload.php?<?php echo $VALUES; ?>" name=FormZayav enctype=multipart/form-data target=uploadFrame>
	<DIV class=headName><?php echo $zayavCategory[$zayav->category]; ?> №<?php echo $zayav->id; ?> - редактирование</DIV>
	<TABLE cellpadding=0 cellspacing=8 class=tabTxtEdit>
	<TR><TD class=tdAbout>Клиент:<TD><?php echo $client; ?>
	<TR><TD class='tdAbout top5' valign=top>Загрузить изображение:<TD colspan=2 id=tdUpload>
	<TR><TD class=tdAbout>Размер изображения:<TD id=pn>
					<INPUT TYPE=text NAME=size_x id=size_x maxlength=5 value='<?php echo round($zayav->size_x,1); ?>'>
					<B class=xb>x</B>
					<INPUT TYPE=text NAME=size_y id=size_y maxlength=5 value='<?php echo round($zayav->size_y,1); ?>'>
					 = <INPUT TYPE=text id=kv_sm readonly value='<?php echo round($zayav->size_x*$zayav->size_y,2); ?>'> см<SUP>2</SUP>
	<TR><TD class=tdAbout colspan=2>Номера выпуска:
	<TR><TD id=nomer colspan=2>
	<TR><TD class=tdAbout>Скидка:<TD><INPUT TYPE=hidden NAME=skidka id=skidka value=<?php echo $zayav->skidka_id; ?>>
	<TR><TD class=tdAbout>Указать стоимость вручную:<TD><INPUT TYPE=hidden id=summa_manual name=summa_manual value=<?php echo $zayav->summa_manual; ?>>
	<TR><TD class=tdAbout>Общая стоимость:<TD><?php echo $prevSumShow; ?><INPUT TYPE=text NAME=summa id=summa readonly> руб.
																					<SPAN id=sumSkidka>Сумма скидки: <B></B> руб.</SPAN><INPUT TYPE=hidden NAME=skidka_sum id=skidka_sum>
	</TABLE>
	<INPUT TYPE=hidden NAME=file id=file value='<?php echo $zayav->file; ?>'>
	<INPUT type=hidden name=zayav_id_rek value=<?php echo $zayav->id; ?>>
</FORM>

<DIV id=zMsg></DIV>
<DIV class=vkButton><BUTTON onclick=zayavEditGo();>Сохранить</BUTTON></DIV><DIV class=vkCancel><BUTTON onclick="location.href='<?php echo $URL."&my_page=zayavView&id=".$zayav->id; ?>'">Отмена</BUTTON></DIV>
