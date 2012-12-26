<?php
if($zayav->file) $zayav->txt="<IMG src=/files/images/".$zayav->file."s.jpg onclick=fotoShow('".$zayav->file."');>".$zayav->txt;
if($zayav->client_id>0) $clientTd="<TR><TD class=tdAbout>Клиент:<TD><A href='".$URL."&my_page=clientInfo&id=".$zayav->client_id."'>".$client."</A>";
if($zayav->telefon) $zayav->txt.="<B>Тел.: ".$zayav->telefon."</B>";
if($zayav->adres) $zayav->txt.="<B>Адрес: ".$zayav->adres."</B>";
if($zayav->summa_manual==1) $manual="<SPAN class=manual>(указана вручную)</SPAN>";
$rubrika=$VK->QRow("select name from setup_rubrika where id=".$zayav->rubrika);
if($zayav->podrubrika>0) $rubrika.="<SPAN class=ug>»</SPAN>".$VK->QRow("select name from setup_pod_rubrika where id=".$zayav->podrubrika);
?>
<DIV class=headName><?php echo $zayavCategory[$zayav->category]; ?> №<?php echo $zayav->id; ?></DIV>
<TABLE cellpadding=0 cellspacing=6 class=ob>
<?php echo $clientTd; ?>
<TR><TD class=tdAbout>Дата приёма:<TD><?php echo FullDataTime($zayav->dtime_add); ?>
<TR><TD class=tdAbout>Рубрика:<TD><?php echo $rubrika; ?>
<TR><TD class=tdAbout valign=top>Текст:<TD><DIV class=txt><?php echo $zayav->txt; ?><DIV style=clear:both;></DIV>
<TR><TD class=tdAbout>Общая стоимость:<TD><B><?php echo round($zayav->summa,2); ?></B> руб.<?php echo $manual; ?>
<TR><TD class=tdAbout colspan=2>Номера выпуска:
<TR><TD colspan=2><?php echo $nomer; ?>
</TABLE>

<?php echo $accrual; ?>
<DIV id=comm></DIV>

