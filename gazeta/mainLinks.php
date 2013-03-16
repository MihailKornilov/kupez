<DIV id=mainLinks>
  <A HREF='<?=$URL?>' style="float:right;margin:5px 5px 0px 15px;" onclick="setCookie('enter','0');">Выход</A>
  <!-- <A HREF='<?php echo $URL; ?>&my_page=develop' style=float:right;margin-top:5px;>Разработка программы</A> -->
  <A HREF='<?=$URL?>&my_page=client' class=mLink<?=isset($mLink1) ? $mLink1 : ''?>><I></I><B></B><SPAN>Клиенты</SPAN></A>
  <A HREF='<?=$URL?>&my_page=zayav'  class=mLink<?=isset($mLink2) ? $mLink2 : ''?>><I></I><B></B><SPAN>Заявки</SPAN></A>
  <A HREF='<?=$URL?>&my_page=report' class=mLink<?=isset($mLink3) ? $mLink3 : ''?>><I></I><B></B><SPAN>Отчёты</SPAN></A>
  <A HREF='<?=$URL?>&my_page=setup'  class=mLink<?=isset($mLink7) ? $mLink7 : ''?>><I></I><B></B><SPAN>Настройки</SPAN></A>
  <DIV style=clear:both;></DIV>
</DIV>
