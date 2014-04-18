<?php
// Размещение объявления
function obCreate()
{
    global $vku;
?>
<DIV id=vk-create>
  <TABLE cellpadding=0 cellspacing=8  id=payContent>
    <TR><TD class=tdAbout><TD><INPUT TYPE=hidden id=dop value=0>
    <TR><TD class=tdAbout>Поднять объявление:<TD valign=bottom><INPUT TYPE=hidden id=top><SPAN id=top_week>на <EM class=bok>&nbsp;</EM><EM class=a>-</EM><EM class=inp>1</EM><EM class=a>+</EM><EM class=bok>&nbsp;</EM> недел<EM class=end></EM></SPAN>
   </TABLE>


</DIV>

<SCRIPT type="text/javascript">
create = {
    back:"<?php echo isset($_GET['back']) ? $_GET['back'] : 'spisok'; ?>", // на какую страницу возвращаться при отмене
    order:{id:0,votes:0},
    top_week:1
};
</SCRIPT>
<?php
} // end of obCreate()

function obMySpisok() {
?>
<div id=myOb>
    <DIV class=path><A href="<?=URL.'&p=ob'?>">КупецЪ</A> » Мои объявления</DIV>

    <DIV id=findResult>&nbsp;</DIV>

    <TABLE cellpadding=0 cellspacing=0 class=MainSpisok>
        <TR><TD class=left id=obSpisok>&nbsp;
            <TD class=right><DIV id=links></DIV>
    </TABLE>
</div>
<SCRIPT type="text/javascript" src="/view/ob/my/ob_edit.js?<?=JS_VERSION?>"></SCRIPT>
<SCRIPT type="text/javascript" src="/view/ob/my/myOb.js?<?=JS_VERSION?>"></SCRIPT>
<?php
} // end of obCreate()
?>