<?php
if($SA[$_GET['viewer_id']]) {
  $SALink="<DIV id=sa></DIV>";
?>
<SCRIPT type="text/javascript">
$(document).ready(function(){
  $("#sa").linkMenu({
    name:'Admin',
    grey0:1,
    right:1,
    spisok:[
      {uid:1,title:'Обновление стилей и скриптов (<?php echo $G->script_style; ?>)',},
      {uid:2,title:'Подсказки'}
    ],
    func:function (ID) {
      switch (ID) {
      case '1': $.getJSON("/superadmin/AjaxScriptStyleUp.php?" + G.values, function () { location.reload(); }); break;
      case '2': location.href = "/index.php?" + G.values + "&my_page=adminHint"; break;
      }
    }
  });
});

function exit() {
  setCookie('enter','0');
  location.href="/index.php?" + G.values;
  }

</SCRIPT>
<?php } ?>

<DIV id=mainLinks>
<?php echo $SALink; ?>
  <A HREF='<?php echo $URL; ?>' style="float:right;margin:5px 5px 0px 15px;" onclick=exit();>Выход</A>
  <A HREF='<?php echo $URL; ?>&my_page=develop' style=float:right;margin-top:5px;>Разработка программы</A>
  <A HREF='<?php echo $URL; ?>&my_page=client' class=mLink<?php echo isset($mLink1) ? $mLink1 : ''; ?>><I></I><B></B><SPAN>Клиенты</SPAN></A>
  <A HREF='<?php echo $URL; ?>&my_page=zayav' class=mLink<?php echo isset($mLink2) ? $mLink2 : ''; ?>><I></I><B></B><SPAN>Заявки</SPAN></A>
  <A HREF='<?php echo $URL; ?>&my_page=report' class=mLink<?php echo isset($mLink3) ? $mLink3 : ''; ?>><I></I><B></B><SPAN>Отчёты</SPAN></A>
  <A HREF='<?php echo $URL; ?>&my_page=setup' class=mLink<?php echo isset($mLink7) ? $mLink7 : ''; ?>><I></I><B></B><SPAN>Настройки</SPAN></A>
  <DIV style=clear:both;></DIV>
</DIV>
