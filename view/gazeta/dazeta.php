<?php
// �������� �������������� ����
function main_links($g) {
    $name = array('�������', '������', '������', '���������');
    $page = array('client',  'zayav',  'report', 'setup');

    $g_page = 'client';
    for ($n = 0; $n < count($page); $n++) {
        if ($g == $page[$n])
            $g_page = $g;
    }
    $links = '<A href="'.URL.'&p=ob"style="float:right;margin:5px 5px 0 15px;" onclick="setCookie(\'enter\',0);">�����</A>';
    for ($n = 0; $n < count($page); $n++) {
        $links .=
            '<A HREF="'.URL.'&p=gazeta&d='.$page[$n].'" class="la'.($page[$n] == $g_page ? ' sel' : '').'">'.
                "<DIV class=l1></DIV>".
                "<DIV class=l2></DIV>".
                "<DIV class=l3>".$name[$n]."</DIV>".
            "</A>";
    }

    echo '<DIV id=main_links>'.$links.'</DIV>';
    return $g_page;
} // end of main_links()

/* ��������� ������� ������� */
function setClientBalans($client_id = 0) {
    if ($client_id > 0) {
        global $VK;
        $rashod = $VK->QRow("select sum(summa) from zayav where client_id=".$client_id);
        $prihod = $VK->QRow("select sum(summa) from oplata where status=1 and client_id=".$client_id);
        $balans = $prihod - $rashod;
        $VK->Query("update client set balans=".$balans." where id=".$client_id);
        return $balans;
    } else {
        return 0;
    }
}

// ������ ��������
function clientSpisok() {
    global $VK;
?>
<DIV id=clientFind></DIV>
<DIV id=findResult>&nbsp;</DIV>
<TABLE cellpadding="0" cellspacing="0" id=client>
<TR>
    <TD id=spisok>&nbsp;
    <TD id=cond>
        <DIV id=buttonCreate><A onclick="ca();">����� ������</A></DIV>
        <BR><BR>
        <INPUT TYPE=hidden id=cDolg value=0>
        <BR><BR>
        <INPUT TYPE=hidden id=personFind value=0>
</TABLE>
<SCRIPT type="text/javascript" src="/include/clientAdd/clientAdd.js?<?=JS_VERSION?>"></SCRIPT>
<SCRIPT type="text/javascript" src="/view/gazeta/client/spisok/clientSpisok.js?<?=JS_VERSION?>"></SCRIPT>
<?php
} // end of clientSpisok()

// ������ ������
function zayavSpisok() {
    if (@$_GET['d1'] == 'add') { zayavAdd(); return; }
    global $VK, $MonthCut;
    $spisok = $VK->QueryObjectArray("SELECT
                                        `general_nomer`,
                                        SUBSTR(`day_public`,1,4) AS `year`,
                                        SUBSTR(`day_public`,6,2) AS `month`,
                                        SUBSTR(`day_public`,9,2) AS `day`,
                                        `week_nomer`,
                                        `day_print`
                                         FROM `gazeta_nomer` ORDER BY general_nomer");
    $nomer = array();
    foreach($spisok as $sp) {
        $grey = (time() > strtotime($sp->day_print) + 86400 ? ' class=grey' : '');
        $pub = abs($sp->day).' '.$MonthCut[$sp->month];
        if (!isset($nomer[$sp->year])) { $nomer[$sp->year] = array(); }
        array_push($nomer[$sp->year],
            '{uid:'.$sp->general_nomer
           .',title:"'.$sp->week_nomer.' ('.$sp->general_nomer.') ����� '.$pub.'"'
           .',content:"<B'.$grey.'>'.$sp->week_nomer.'</B><SPAN'.$grey.'>('.$sp->general_nomer.')</SPAN><TT>����� '.$pub.'</TT>"}'
        );
    }
    $y_nomer = array();
    foreach ($nomer as $n => $sp) { array_push($y_nomer, $n.":[".implode(',',$sp)."]"); }
    $gnMin = $VK->QRow('SELECT MIN(`general_nomer`) FROM `gazeta_nomer` WHERE `day_print`>=DATE_FORMAT(NOW(),"%Y-%m-%d")');
?>
<DIV id=findResult>&nbsp;</DIV>
<TABLE cellpadding=0 cellspacing=0 id=zayav>
    <TR>
        <TD id=spisok>&nbsp;
        <TD id=cond>
            <DIV id=buttonCreate><A onclick="location.href='<?=URL?>&p=gazeta&d=zayav&d1=add';">����� ������</A></DIV>
            <DIV id=fastFind></DIV>
            <DIV id=nofast>
                <BR><BR>
                <DIV class=findName>���������</DIV><INPUT TYPE=hidden id=category value=0>
                <INPUT TYPE=hidden id=type_gaz value=0>
                <BR>
                <DIV class=findName>����� ������</DIV><INPUT TYPE=hidden id=year value=<?=strftime("%Y",time())?>>
                <INPUT TYPE=hidden id=gazeta_nomer value=<?=$gnMin?>><BR>
            </DIV>
</TABLE>
<SCRIPT type="text/javascript">
G.gazeta_nomer_spisok = <?='{'.implode(',', $y_nomer).'}'?>;
var Zayav = {
    gazeta_nomer:<?=$gnMin?>,
    year:<?=$VK->vkSelJson("SELECT
                                DISTINCT(SUBSTR(`day_public`,1,4)),
                                SUBSTR(`day_public`,1,4) FROM `gazeta_nomer` ORDER BY `day_public`");?>
};
</SCRIPT>
<SCRIPT type="text/javascript" src="/view/gazeta/zayav/spisok/zayavSpisok.js?<?=JS_VERSION?>"></SCRIPT>
<?php
} // end of zayavSpisok()

// ���������� ����� ������
function zayavAdd() {
?>
<DIV id=zayavAdd>
    <DIV class=headName>�������� ����� ������</DIV>

    <TABLE cellpadding=0 cellspacing=8>
        <TR><TD class=tdAbout>������:    <TD><INPUT TYPE=hidden id=client_id name=client_id value="">
        <TR><TD class=tdAbout>���������: <TD><INPUT TYPE=hidden NAME=category id=category value=1>
    </TABLE>

    <DIV id=content></DIV>

    <TABLE cellpadding=0 cellspacing=8><TR><TD class=tdAbout>������ �������:<TD></TABLE>
    <DIV id=nomer></DIV>

    <DIV id=skidkaContent></DIV>

    <TABLE cellpadding=0 cellspacing=8 id=manual_tab>
        <TR><TD class=tdAbout>������� ��������� �������:<TD><INPUT TYPE=hidden id=summa_manual name=summa_manual value=0>
    </TABLE>

    <TABLE cellpadding=0 cellspacing=8>
        <TR><TD class=tdAbout>�������� ���������:<TD><INPUT TYPE=text NAME=summa id=summa readonly value=0> ���.
               <SPAN id=sumSkidka>����� ������: <B></B> ���.</SPAN><INPUT TYPE=hidden NAME=skidka_sum id=skidka_sum value=0>
                <TR><TD class=tdAbout>������ ��������?:            <TD><INPUT TYPE=hidden name=oplata id=oplata>
        <TR><TD class=tdAbout valign=top>�������:<TD><TEXTAREA name=note id=note></TEXTAREA>
    </TABLE>

    <DIV class=vkButton><BUTTON onclick=zayavAddGo();>������</BUTTON></DIV>
    <DIV class=vkCancel><BUTTON onclick="location.href='<?=URL?>&p=gazeta&d=zayav'">������</BUTTON></DIV>
</DIV>
<SCRIPT type="text/javascript" src="/view/gazeta/zayav/add/zayavAdd.js?<?=JS_VERSION?>"></SCRIPT>
<?php
} // end of zayavAdd()


function reportView() {
    global $VK;
    switch(@$_GET['d1']) {
        case 'log':
        default: $d1 = 'log'; $log = 'sel'; break;
        case 'zayav': $d1 = 'zayav'; $zayav = 'sel'; break;
        case 'money': $d1 = 'money'; $money = 'sel'; break;
    }
//    $ids = $VK->ids("select distinct(viewer_id_add) from history where ws_id=".$vku->ws_id);
    $ids = '0';
    $ids_money = $VK->ids("SELECT DISTINCT(`viewer_id_add`) FROM `gazeta_money`");
    $ids .= ($ids_money ? ',' : '').$ids_money;
    $report = reportGet($d1);
?>
<SCRIPT type="text/javascript">
G.vkusers = <?=$ids ? $VK->ptpJson("select viewer_id,concat(first_name,' ',last_name) from vk_user where viewer_id in (".$ids.")") : 'null'?>;
</SCRIPT>
<TABLE cellpadding=0 cellspacing=0 id=report>
    <TR><TD id=cont><?=$report->content?>&nbsp;
        <TD id=right>
            <DIV class=infoLink>
                <a href="<?=URL?>&p=gazeta&d=report&d1=log" class="<?=@$log?>">������� ��������</a>
                <a href="<?=URL?>&p=gazeta&d=report&d1=zayav" class="<?=@$zayav?>">������</a>
                <a href="<?=URL?>&p=gazeta&d=report&d1=money" class="<?=@$money?>">������</a>
            </DIV>
        <?=$report->right?>
</TABLE>
<div id=report_dialog></div>
<?php
echo $report->js;
} // end of reportView()

function reportGet($d1) {
    switch ($d1) {
        case 'log':
            $send->content = '';
            $send->js = '<SCRIPT type="text/javascript" src="/view/gazeta/report/log/log.js?'.JS_VERSION.'"></SCRIPT>';
            break;
        case 'zayav':
            $send->content = '';
            $send->js = '<SCRIPT type="text/javascript" src="/view/gazeta/report/zayav/zayav.js?'.JS_VERSION.'"></SCRIPT>';
            break;

            break;
        case 'money':
            switch(@$_GET['d2']) {
                case 'prihod':
                default:
                    $d2 = 'prihod';
                    $prihod = 'Sel';
                    $send->content = '
<div id=prihod>
    <div id=summa>�����: <b id=itog></b> ���.<a onclick=prihodAdd();>������ ������������ �����</a></div>
    <div id=spisokHead></div>
    <div id=spisok></div>
</div>
';
                    $send->right = '
<TABLE cellpadding=0 cellspacing=0 id=periodHead>
    <tr><td class=active  val="calendar">������
        <td class=passive val="month" align=right>�� �������
</table>
<div id=periodCalendar>
    <EM class=period_em>��:</EM><INPUT type=hidden id=day_begin>
    <EM class=period_em>��:</EM><INPUT type=hidden id=day_end>
</div>
<div id=periodMonth><input type=hidden id=period_year /></div>
';
                    $send->js = '<SCRIPT type="text/javascript" src="/view/gazeta/report/money/prihod/prihod.js?'.JS_VERSION.'"></SCRIPT>';
                    break;
                case 'rashod':
                    $d2 = 'rashod';
                    $rashod = 'Sel';
                    $send->content = '
<div id=rashod>
    <div class=headName>������ �������� ������<a onclick=rashodAdd();>������ ����� ������</a></div>
    <div id=summa>�����: <b id=itog></b> ���.</div>
    <div id=spisokHead></div>
    <div id=spisok></div>
</div>
';
                    $send->right = '
<TABLE cellpadding=0 cellspacing=0 id=periodHead>
    <tr><td class=active  val="calendar">������
        <td class=passive val="month" align=right>�� �������
</table>
<div id=periodCalendar>
    <EM class=period_em>��:</EM><INPUT type=hidden id=day_begin>
    <EM class=period_em>��:</EM><INPUT type=hidden id=day_end>
</div>
<div id=periodMonth><input type=hidden id=period_year /></div>
<div class=findHead>���������</div>
<input type=hidden id=rashod_category>
';
                    $send->js = '<SCRIPT type="text/javascript" src="/view/gazeta/report/money/rashod/rashod.js?'.JS_VERSION.'"></SCRIPT>';
                    break;
                case 'kassa':
                    $d2 = 'kassa';
                    $kassa = 'Sel';
                    if (KASSA_START == -1) {
                        $send->content = '
<DIV id=kassa_set>
      <DIV class=info>���������� ��������, ������ ������� ����� �����, ����������� ������ � ����������.
      �� ����� �������� ����� ������� ���������� ���� �������, �����������, ���� ������������ �� �����.<BR>
      <B>��������!</B> ������ �������� ����� ���������� ������ ���� ���.</DIV>
      <TABLE cellpadding=0 cellspacing=8 id=kassa_set_tab><TR>
          <TD>�����: <INPUT type=text id=kassa_set_sum maxlength=8> ���.
          <TD><DIV class=vkButton><BUTTON onclick=kassaSet();>����������</BUTTON></DIV>
      </TABLE>
</DIV>
';
                        $send->right = '';
                    } else {
                        $send->content = '
<div id=kassa>
    <div id=summa>
        � �����: <b id=itog></b> ���.
        <div class=a>
            <a onclick=kassaGet();>����� �� �����</a> ::
            <a onclick=kassaPut();>�������� � �����</a>
        </div>
    </div>
    <TABLE cellpadding=0 cellspacing=0 class=tabSpisok width=100%>
        <TR><TH class=sum>�����
        <TH class=about>��������
        <TH class=data>����
    </TABLE>
    <div id=spisok></div>
</div>
';
                        $send->right = '
<div class=findHead>������</div>
<EM class=period_em>��:</EM><INPUT type=hidden id=day_begin>
<EM class=period_em>��:</EM><INPUT type=hidden id=day_end>
';
                    }
                    $send->js = '<SCRIPT type="text/javascript" src="/view/gazeta/report/money/kassa/kassa.js?'.JS_VERSION.'"></SCRIPT>';
                    break;
            }
            $send->content = '<div id=dopMenu>
                <a href="'.URL.'&p=gazeta&d=report&d1=money&d2=prihod" class="link'.@$prihod.'"><i></i><b></b><div>�����������</div><b></b><i></i></a>
                <a href="'.URL.'&p=gazeta&d=report&d1=money&d2=rashod" class="link'.@$rashod.'"><i></i><b></b><div>�������</div><b></b><i></i></a>
                <a href="'.URL.'&p=gazeta&d=report&d1=money&d2=kassa" class="link'.@$kassa.'"><i></i><b></b><div>�����</div><b></b><i></i></a>
                </div>'.$send->content;
            break;
    }
    return $send;
}

// �������� � �����������
function setupView($admin) {
    global $VK;
    // ��������� ���������� � ��������� ���� ��� ��������� ������� �����
    $gn = $VK->QueryObjectOne("SELECT
            SUBSTR(MIN(`day_public`),1,4) AS `begin`,
            SUBSTR(MAX(`day_public`),1,4) AS `end`,
            MAX(`general_nomer`) AS `max`
            FROM `gazeta_nomer`");
    if(!$gn->begin)    {
        $gn->begin = strftime("%Y",time());
        $gn->end = $gn->begin;
    }
?>
<DIV id=setup>
    <DIV class=razdel><INPUT type=hidden id=razdelSel value=<?=(@$_GET['id']?$_GET['id']:1)?>></DIV>
    <DIV id=edit></DIV>
    <DIV id=setup_dialog></DIV>
</DIV>
<SCRIPT type="text/javascript" src="/view/gazeta/setup/setup.js?<?=JS_VERSION?>"></SCRIPT>
<SCRIPT type="text/javascript">

$("#razdelSel").vkSel({
    width:300,
    spisok:[
        <?=($admin == 1 ? "{uid:8,title:'������ � ����� �����������'}," : '')?>
        {uid:1,title:'���������'},
        {uid:2,title:'�������'},
        {uid:7,title:'����������'},
        {uid:9,title:'��������� ����� ����������'},
        {uid:6,title:'�������������� ��������� ����������'},
        {uid:4,title:'��������� ��2 ������ ������ ��� �������',content:'��������� ��&sup2; ������ ������ ��� �������'},
        {uid:3,title:'������ �������� ������'},
        {uid:5,title:'������'},
        {uid:10,title:'��������� ��������'}],
    func:setupSet
});
G.setup = {
    year:{
        begin:<?=$gn->begin?>,
        end:<?=$gn->end?>
    },
    gn_max:<?=$gn->max?>
};
setupSet($("#razdelSel").val());
</SCRIPT>
<?php
} // end of reportView()
?>