<?php if($zayav->skidka_razmer>0) $skidka="<SPAN class=skidka>Скидка <B>".$zayav->skidka_razmer."</B>% (".round($zayav->skidka_sum,2)." руб.)</SPAN>"; ?>
<TABLE cellpadding=0 cellspacing=0 class=rek>
	<TR><TD id=left>
		<DIV class=headName><?php echo $zayavCategory[$zayav->category]; ?> №<?php echo $zayav->id; ?></DIV>
		<TABLE cellpadding=0 cellspacing=6 class=tab>
		<TR><TD class=tdAbout>Клиент:<TD><A href='<?php echo $URL."&my_page=clientInfo&id=".$zayav->client_id; ?>'><?php echo $client; ?></A>
		<TR><TD class=tdAbout>Дата приёма:<TD><?php echo FullDataTime($zayav->dtime_add); ?>
		<TR><TD class=tdAbout>Размер:<TD><?php echo round($zayav->size_x,1)." x ".round($zayav->size_y,1)." = ".round($zayav->size_x*$zayav->size_y,2)." см&sup2;"; ?>
		<TR><TD class=tdAbout>Общая стоимость:<TD><B><?php echo round($zayav->summa,2); ?></B> руб.<?php echo $skidka; ?>
		<TR><TD class=tdAbout colspan=2>Номера выпуска:
		<TR><TD colspan=2><?php echo $nomer; ?>
		</TABLE>
		<?php echo $accrual; ?>
		<DIV id=comm></DIV>

<TD id=right><?php if($zayav->file) echo "<IMG src=/files/images/".$zayav->file."m.jpg onclick=fotoShow('".$zayav->file."')>"; ?>
</TABLE>
