<?php
// Список посетителей
function adminVisit() {
?>
<DIV id=dopMenu>
    <A class=linkSel><I></I><B></B><DIV>Посетители</DIV><B></B><I></I></A>
    <A HREF='<?=URL?>&p=admin&d=ob' class=link><I></I><B></B><DIV>Объявления</DIV><B></B><I></I></A>
    <A href="<?=URL?>&p=ob" class=fr>Назад</A>
</DIV>
<DIV id=findResult>&nbsp;</DIV>

<TABLE cellpadding=0 cellspacing=0 id=adminVisit class=MainSpisok>
    <TR>
        <TD class=left id=left>&nbsp;
        <TD class=right>
            <div id=search></div>
            <INPUT TYPE=hidden id=findRadio value=2>
</TABLE>
<SCRIPT type="text/javascript" src="/view/admin/visit/visit.js?<?=JS_VERSION?>"></SCRIPT>
<?php
} // end of adminVisit()

// Список объявлений
function adminObSpisok() {
    global $VK;
    $viewer_id = preg_match('/^\d+$/', @$_GET['viewer_id_add'])?$_GET['viewer_id_add']:0;
    if ($viewer_id > 0) {
        $viewer = $VK->QueryObjectOne('SELECT * FROM `vk_user` WHERE `viewer_id`='.$viewer_id);
        $name = $viewer->first_name.' '.$viewer->last_name.'<br />';
        $photo = '<a href="http://vk.com/id'.$viewer_id.'" target=_blank><img src='.$viewer->photo.' /></a>'.
                 '<a onclick=hideUser();>Скрыть</a>';
    }
?>
<div id=adminOb>
    <DIV id=dopMenu>
        <A HREF='<?=URL?>&p=admin&d=visit' class=link><I></I><B></B><DIV>Посетители</DIV><B></B><I></I></A>
        <A class=linkSel><I></I><B></B><DIV>Объявления</DIV><B></B><I></I></A>
        <A href="<?=URL?>&p=ob" class=fr>Назад</A>
    </DIV>

    <DIV id=findResult>&nbsp;</DIV>

    <TABLE cellpadding=0 cellspacing=0 class=MainSpisok>
        <TR><TD class=left id=obSpisok>&nbsp;
            <TD class=right>
                <div id=viewer><?=@$name.@$photo?></div>
                <div class=findHead>Категория</div>
                <input type=hidden id=menu value=0>
    </TABLE>
</div>
<SCRIPT type="text/javascript">
G.viewer = {
    id:<?=$viewer_id?>
}
</SCRIPT>
<SCRIPT type="text/javascript" src="/view/ob/my/ob_edit.js?<?=JS_VERSION?>"></SCRIPT>
<SCRIPT type="text/javascript" src="/view/admin/ob/adminOb.js?<?=JS_VERSION?>"></SCRIPT>
<?php
} // end of adminObSpisok()
?>