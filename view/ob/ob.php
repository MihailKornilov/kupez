<?php
// ������ ����������
function obSpisok()
{
    global $VK, $vku;
    if (SA == VIEWER_ID) {
        $today = $VK->QRow("SELECT COUNT(`id`) FROM `vk_visit` WHERE `dtime_add`>='".strftime("%Y-%m-%d",time())." 00:00:00'");
        $visit = "<BR><BR><BR><DIV class=findName>����������</DIV>".
                 "<A href='".URL."&p=ob&o=visit'>�������: ".$today."</A>";
    }
    // ������� ���������� ���������� ������ �������
    $rubrikaCount = $VK->ptpJson('SELECT
                                    `rubrika` AS `id`,
                                    COUNT(`id`) AS `ob_count`
                                  FROM `vk_ob` WHERE
                                    `status`=1 AND
                                    `day_active`>=DATE_FORMAT(NOW(), "%Y-%m-%d") GROUP BY `rubrika`');
    // ������ �������, ��� ������� ���� ����������
    $cities = $VK->vkSelJson('SELECT DISTINCT(`city_id`),`city_name` FROM `vk_ob`
                              WHERE
                                `city_id`>0 AND
                                `city_name`!="" AND
                                `status`=1 AND
                                `day_active`>=DATE_FORMAT(NOW(), "%Y-%m-%d") ORDER BY `city_name`');
?>
<DIV id=obSpisok>
    <TABLE cellpadding=0 cellspacing=0 id=tabFind>
        <TR><TD><DIV id=vkFind></DIV>
            <TH><DIV class=vkButton><BUTTON onclick="location.href='<?=URL.'&p=ob&d=create'?>'";>���������� ����������</BUTTON></DIV>
    </TABLE>
    <DIV id=findResult>&nbsp;</DIV>
    <TABLE cellpadding=0 cellspacing=0 width=100%>
        <TR>
            <TD id=spisok>&nbsp;
            <TD id=right>
                <DIV class=findName>������</DIV><INPUT TYPE=hidden id=countries value=1><INPUT TYPE=hidden id=cities value=0><BR>

                <DIV class=findName>�������</DIV><DIV id=rubrika></DIV>
                <DIV id=podrubrika></DIV>

                <DIV class=findName>�������������</DIV>
                <INPUT TYPE=hidden id=foto_only value=0>
                <?=@$visit?>
    </TABLE>
</DIV>
<SCRIPT type="text/javascript">
var spisok = {
    enter:"<?=$vku['gazeta_worker'] == 1 ? "<A href='".URL."&p=gazeta' class=enter>����� � ���������</A>" : ''?>",
    rubCount:<?=$rubrikaCount?>,
    cities:<?=$cities?>
};
</SCRIPT>
<SCRIPT type="text/javascript" src="/view/ob/spisok/obSpisok.js?<?=JS_VERSION?>"></SCRIPT>
<?php
} // end of obSpisok()

// ���������� ����������
function obCreate()
{
    global $vku;
?>
<DIV id=vk-create>
  <DIV class=headName>�������� ������ ����������</DIV>
  <DIV class=info>
    <P>����������, ��������� ��� ����������� ����. ����� ���������� ���������� ����� ���������� �������� ��� ������ ������������� ���������.
    <P>���������� ���������� ������ ��������� �� ����� ����� �������� ��� ��������� � ������ ����������, ���� ��� �������� <A>�������</A>.
    <P>���������� ����� ��������� ������ �� 1 �����, � ���������� �� ������� �������� ���� ����.
  </DIV>

    <TABLE cellpadding=0 cellspacing=10 class=tab>
        <TR><TD class=tdAbout>�������:              <TD><INPUT TYPE=hidden id=rubrika><INPUT TYPE=hidden id=podrubrika>
        <TR><TD class=tdAbout valign=top>�����:     <TD><TEXTAREA id=txt></TEXTAREA>
        <TR><TD class=tdAbout>���������� ��������:  <TD><INPUT TYPE=text id=telefon maxlength=200>
        <TR><TD class=tdAbout valign=top>           <INPUT TYPE=hidden id=images><TD id=upload>
        <TR><TD class='tdAbout top5' valign=top>������:<TD><INPUT TYPE=hidden id=countries value=<?=$vku['country']?>>
                                                           <INPUT TYPE=hidden id=cities value=0>
        <TR><TD class=tdAbout>���������� ��� �� VK:    <TD><INPUT TYPE=hidden id=viewer_id_show value=0>
        <TR><TD class=tdAbout>������� �������:         <TD><INPUT TYPE=hidden id=pay_service value=0>
    </TABLE>

  <TABLE cellpadding=0 cellspacing=8  id=payContent>
    <TR><TD class=tdAbout><TD><INPUT TYPE=hidden id=dop value=0>
    <TR><TD class=tdAbout>������� ����������:<TD valign=bottom><INPUT TYPE=hidden id=top><SPAN id=top_week>�� <EM class=bok>&nbsp;</EM><EM class=a>-</EM><EM class=inp>1</EM><EM class=a>+</EM><EM class=bok>&nbsp;</EM> �����<EM class=end></EM></SPAN>
   </TABLE>

  <INPUT TYPE=hidden id=file>

  <DIV id=butts>
    <DIV class=vkButton><BUTTON>���������� ����������<SPAN></SPAN></BUTTON></DIV>
    <DIV class=vkCancel><BUTTON>������</BUTTON></DIV>
  </DIV>

<DIV id=callbacks></DIV>

  <DIV class=headName>���������� ����������</DIV>
  <DIV id=obSpisok></DIV>
  <DIV id=dialog_obCreate></DIV>
</DIV>

<SCRIPT type="text/javascript">
create = {
    back:"<?php echo isset($_GET['back']) ? $_GET['back'] : 'spisok'; ?>", // �� ����� �������� ������������ ��� ������
    order:{id:0,votes:0},
    top_week:1
};
</SCRIPT>
<SCRIPT type="text/javascript" src="/view/ob/create/create.js?<?=JS_VERSION?>"></SCRIPT>
<?php
} // end of obCreate()

function obMySpisok() {
?>
<div id=vk-myOb>
    <DIV class=path><A href="<?=URL?>">������</A> � ��� ����������</DIV>

    <DIV id=findResult>&nbsp;</DIV>

    <TABLE cellpadding=0 cellspacing=0 width=100%>
        <TR><TD id=spisok><DIV id=obSpisok></DIV>
            <TD id=right><DIV id=links></DIV>
    </TABLE>
    <div id=dialog_my></div>
</div>
<SCRIPT type="text/javascript" src="/view/ob/my/ob_edit.js?<?php echo $G->script_style; ?>"></SCRIPT>
<SCRIPT type="text/javascript" src="/view/ob/my/myOb.js?<?php echo $G->script_style; ?>"></SCRIPT>
<?php
} // end of obCreate()
?>