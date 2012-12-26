<DIV id=dopMenu>
	<A HREF='<?php echo $URL; ?>&my_page=zayavView&id=<?php echo $zayav->id; ?>' class=link<?php echo $dLink1; ?>><I></I><B></B><DIV>Просмотр</DIV><B></B><I></I></A>
	<A HREF='<?php echo $URL; ?>&my_page=zayavEdit&id=<?php echo $zayav->id; ?>' class=link<?php echo $dLink2; ?>><I></I><B></B><DIV>Редактирование</DIV><B></B><I></I></A>
	<?php if($_GET['my_page']=='zayavView') echo "<A HREF='javascript:' class=link onclick=accrualAdd();><I></I><B></B><DIV>Начисление</DIV><B></B><I></I></A>"; ?>
	<?php echo $zayavDel; ?>
	<DIV style=clear:both;></DIV>
</DIV>

