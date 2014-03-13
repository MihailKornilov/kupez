<?php


function reportGet($d1) {
    global $VK;
    $send = '';
    switch ($d1) {
        case 'log':
            $ids = $VK->ids("SELECT DISTINCT(`viewer_id_add`) FROM `gazeta_log`");
            $vkusers = $ids ? $VK->objectAssJson('SELECT
                            `viewer_id` AS `id`,
                        CONCAT(`first_name`," ",`last_name`) AS name,
                            `sex`
                        FROM `vk_user` WHERE `viewer_id` IN ('.$ids.')') : 'null';

            $send->content = '
<div id=log>
    <div id=spisok></div>
</div>
';
            $send->right = '
<div class=findHead>���������</div>
<input type=hidden id=log_type>
<div class=findHead>���������</div>
<input type=hidden id=log_worker>
<div class=findHead>������</div>
<EM class=period_em>��:</EM><INPUT type=hidden id=day_begin>
<EM class=period_em>��:</EM><INPUT type=hidden id=day_end>
';
            $send->js = '<SCRIPT type="text/javascript">G.vkusers = '.$vkusers.';</SCRIPT>'.
                        '<SCRIPT type="text/javascript" src="/view/gazeta/report/log/log.js?'.JS_VERSION.'"></SCRIPT>';
            break;
        case 'zayav':
            $send->content = '
<div id=repZayav>
    <div id=spisok><img src=/img/upload.gif></div>
</div>
';
            $send->right = '
<input type=hidden id=zayav_year />

<div class=findName>������ ������</div>
<input type=hidden id=format value="Month">
';
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
    <div id=summa>�����: <b id=itog></b> ���.<a onclick="moneyAdd({func:reportCalendarGet});">������ ������������ �����</a></div>
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
<div class=findHead>��� ��������</div>
<input type=hidden id=money_type>
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
<input type=hidden id=expense_id>
';
                    $send->js = '<SCRIPT type="text/javascript" src="/view/gazeta/report/money/rashod/rashod.js?'.JS_VERSION.'"></SCRIPT>';
                    break;
                case 'kassa':
                    $d2 = 'kassa';
                    $kassa = 'Sel';
                    if (KASSA_START == -1) {
                        $send->content = '
<DIV id=kassa_set>
      <DIV class=info>���������� ��������, ������ ������� ����� �����, ����������� ������ � ��������.
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
