<SCRIPT type="text/javascript">
G.gn.first_active = <?=GN_FIRST_ACTIVE?>;
G.gn.first_save = <?=GN_FIRST_ACTIVE?>;
G.gn.last_active = <?=GN_LAST_ACTIVE?>;
</SCRIPT>
<SCRIPT type="text/javascript" src="/js/gnGet.js?<?=JS_VERSION?>"></SCRIPT>
<SCRIPT type="text/javascript" src="/js/gazeta.js?<?=JS_VERSION?>"></SCRIPT>
<?php

// ���������� ����� ������
function zayavAdd() {
    $back = 'zayav';
    if (@$_GET['client_id']) $back = 'client&d1=info&id='.$_GET['client_id'];
?>
<DIV id=zayavAdd>
    <DIV class=headName>�������� ����� ������</DIV>

    <TABLE cellpadding=0 cellspacing=8>
        <TR><TD class=tdAbout>������:          <TD><INPUT TYPE=hidden id=client_id value=<?=@$_GET['client_id']?$_GET['client_id']:0?>>
        <TR><TD class=tdAbout><b>���������:</b><TD><INPUT TYPE=hidden id=category value=1>
    </TABLE>

    <TABLE cellpadding=0 cellspacing=8 id=for_ob>
        <TR><TD class=tdAbout>�������:            <TD><INPUT TYPE=hidden id=rubrika><INPUT TYPE=hidden id=podrubrika>
        <TR><TD class=tdAbout valign=top>�����:   <TD><TEXTAREA id=txt></TEXTAREA><DIV id=txtCount></DIV>
        <TR><TD class=tdAbout>���������� �������: <TD><INPUT TYPE=text id=telefon maxlength=200>
        <TR><TD class=tdAbout>�����:              <TD><INPUT TYPE=text id=adres maxlength=200>
    </TABLE>

    <TABLE cellpadding=0 cellspacing=8 id=for_rek>
        <TR><TD class=tdAbout>������ �����������:
            <TD><INPUT TYPE=text id=size_x maxlength=5>
                <B class=xb>x</B>
                <INPUT TYPE=text id=size_y maxlength=5>
                 = <INPUT TYPE=text id=kv_sm readonly> ��<SUP>2</SUP>
    </TABLE>

    <TABLE cellpadding=0 cellspacing=8>
        <TR><TD class=tdAbout>�����������:<TD id=foto>
    </TABLE>
    <input type=hidden id=foto_link>

    <TABLE cellpadding=0 cellspacing=8><TR><TD class=tdAbout>������ �������:<TD></TABLE>
    <DIV id=gn_spisok></DIV>

    <TABLE cellpadding=0 cellspacing=8 id=skidka_tab>
        <TR><TD class=tdAbout>������:<TD><INPUT TYPE=hidden id=skidka>
    </TABLE>

    <TABLE cellpadding=0 cellspacing=8 id=manual_tab>
        <TR><TD class=tdAbout>������� ��������� �������:<TD><INPUT TYPE=hidden id=summa_manual>
    </TABLE>

    <TABLE cellpadding=0 cellspacing=8>
        <TR><TD class=tdAbout>�������� ���������:<TD><INPUT TYPE=text id=summa readonly value=0> ���.
               <SPAN id=sumSkidka></SPAN><INPUT TYPE=hidden id=skidka_sum value=0>
        <TR><TD class=tdAbout valign=top>�������:<TD><TEXTAREA id=note></TEXTAREA>
    </TABLE>

    <DIV class=vkButton><BUTTON onclick="zayavAddGo(this,0);">������</BUTTON></DIV>
    <DIV class=vkCancel><BUTTON onclick="location.href='<?=URL?>&p=gazeta&d=<?=$back?>'">������</BUTTON></DIV>
</DIV>
<SCRIPT type="text/javascript" src="/view/gazeta/zayav/add/zayavAddEdit.js?<?=JS_VERSION?>"></SCRIPT>
<SCRIPT type="text/javascript">zayavAdd();</SCRIPT>
<?php
} // end of zayavAdd()

// �������� ������
function zayavView() {
    global $VK, $zayavCategory;
    $zayav = $VK->QueryObjectOne("SELECT * FROM `gazeta_zayav` WHERE `id`=".(preg_match("|^[\d]+$|", @$_GET['id']) ? $_GET['id'] : 0));
    if (!@$zayav->id) { nopage($_GET['p'], $_GET['d']); return; };

    if ($zayav->client_id > 0) {
        $client = $VK->QueryObjectOne("SELECT `fio`,`org_name` FROM `gazeta_client` WHERE `id`=".$zayav->client_id);
        $client = "<TR><TD class=tdAbout>������:".
                      "<TD><A href='".URL."&p=gazeta&d=client&d1=info&id=".$zayav->client_id."'>".($client->org_name ? $client->org_name : $client->fio)."</A>";
    }

    switch ($zayav->category) {
        case 1:
            if ($zayav->file)
                $img = '<td><img src='.$zayav->file.'s.jpg onclick=G.fotoView("'.$zayav->file.'");>';
            if ($zayav->summa_manual == 1) $manual = "<SPAN class=manual>(������� �������)</SPAN>";
            $dop = '<TH>�������������';
            $dopArr = $VK->QueryPtPArray('SELECT `id`,`name` FROM `setup_ob_dop`');
            $dopTd = '<td class=dop>';
            break;
        case 2:
            if ($zayav->summa_manual == 1) $manual = "<SPAN class=manual>(������� �������)</SPAN>";
            if ($zayav->skidka > 0)
                $skidka = "<SPAN class=skidka>������ <B>".$zayav->skidka."</B>% (".round($zayav->skidka_sum, 2)." ���.)</SPAN>";
            $dop = '<TH>������';
            $dopArr = $VK->QueryPtPArray('SELECT `id`,`name` FROM `setup_polosa_cost`');
            $dopTd = '<td class=dop>';
            break;
    }
    $dopArr[0] = '';


    if ($zayav->file and $zayav->category != 1) {
        $image = '<td id=image><img src='.$zayav->file.'b.jpg width=200 onclick=G.fotoView("'.$zayav->file.'");>';
    }

    $zayav_del = 1; // ���������� ������ ����� �������

    // ������ �������
    $spisok = $VK->QueryObjectArray("SELECT * FROM `gazeta_nomer_pub` WHERE `zayav_id`=".$zayav->id.' ORDER BY `general_nomer`');
    if (count($spisok) > 0) {
        $gn = $VK->ObjectAss("SELECT `general_nomer` AS `id`,`week_nomer`,`day_public` FROM `gazeta_nomer`");
        $pub['active'] = array();
        $pub['lost'] = array();
        foreach ($spisok as $sp) {
            $class = ($sp->general_nomer >= GN_FIRST_ACTIVE ? 'active' : 'lost');
            if ($class == 'lost') $zayav_del = ''; // ���� ���� ��������� ������, �������� ������ ����������
            array_push($pub[$class], '<TR class='.$class.'>'.
                        '<td align=right><b>'.$gn[$sp->general_nomer]->week_nomer.'</b><em>('.$sp->general_nomer.')</em>'.
                        '<td class=public>'.FullData($gn[$sp->general_nomer]->day_public, 1, 1).
                        '<td align=right>'.round($sp->summa, 2).
                        @$dopTd.$dopArr[$sp->dop]);
        }
        $public = '<TABLE cellpadding=0 cellspacing=0 class=tabSpisok><TR><TH>�����<TH>�����<TH>����'.@$dop;
        if (count($pub['lost']) > 0) {
            $public .= '<tr class=lost_spisok><td colspan=4><a onclick=lostView();>�������� ��������� ������ ('.count($pub['lost']).')</a>';
            $public .= implode($pub['lost']);
        }
        $public .= implode($pub['active']).'</TABLE>';
    }

    // ������ ��������
    $moneySumma = 0; // ����� ����� ��������
    $spisok = $VK->QueryObjectArray("SELECT * FROM `gazeta_money` WHERE `zayav_id`=".$zayav->id.' ORDER BY `id`');
    if (count($spisok) > 0) {
        if ($zayav->client_id == 0 and $zayav_del == 1) $zayav_del = '<span id=del>������� ������</span>';
        $type = $VK->QueryPtPArray("SELECT `id`,`name` FROM `setup_money_type`");
        $money = '<div id=money>'.
                '<DIV class=headBlue>������ ��������</div>'.
                '<TABLE cellpadding=0 cellspacing=0 class=tabSpisok><TR><TH>�����<TH>��������<TH>����<TH class=del>';
        foreach ($spisok as $sp) {
            $money .= '<tr><td class=sum>'.round($sp->sum, 2).
                          '<td class=about><b>'.$type[$sp->type].($sp->prim ? ':' : '').'</b> '.$sp->prim.
                          '<td class=dtime>'.FullDataTime($sp->dtime_add).
                          '<td class=del><div class=img_del onclick=moneyDel('.$sp->id.');></div>';
            $moneySumma += $sp->sum;
        }
        $money .= '</table></div>';
    }

    // ���� ��� �������, ��������, ������� �������� �� ������
    if ($zayav->client_id == 0) {
        $paided = "<TR><TD class=tdAbout>��������:<TD>".round($moneySumma, 2)." ���.";
    }


    if ($zayav_del == 1) {
        $zayav_del = '<a id=delete>������� ������</a>';
    }
?>
<div id=zayavView>
    <TABLE cellpadding=0 cellspacing=0 width=100%>
    <tr><td valign=top width=100%>
        <TABLE cellpadding=0 cellspacing=6 width=100%>
            <?=@$txt?>
            <?=@$size?>
            <TR><TD class=tdAbout>����� ���������:<TD><B><?=round($zayav->summa, 2)?></B> ���.<?=@$manual.@$skidka?>
            <?=@$paided?>
            <TR><TD class=tdAbout>������ �������:<td>
        </TABLE>
        <?=@$public?>
        <?=@$image?>
    </TABLE>
    <?=@$money?>
    <DIV id=comm></DIV>
    <DIV id=dialog_zayav></DIV>
</div>
<SCRIPT type="text/javascript">
G.zayav = {
    id:<?=$zayav->id?>,
    client_id:<?=$zayav->client_id?>,
    category:<?=$zayav->category?>,
    image:"<?=$zayav->file?>"
};
</SCRIPT>
<SCRIPT type="text/javascript" src="/view/gazeta/zayav/view/zayavView.js?<?=JS_VERSION?>"></SCRIPT>
<?php
} // end of zayavView()

// �������������� ������
function zayavEdit() {
    global $VK, $zayavCategory;
    $zayav = $VK->QueryObjectOne("SELECT * FROM `gazeta_zayav` WHERE `id`=".(preg_match("|^[\d]+$|", @$_GET['id']) ? $_GET['id'] : 0));
    if (!@$zayav->id) { nopage($_GET['p'], $_GET['d']); return; };

    if ($zayav->client_id > 0) {
        $client = $VK->QueryObjectOne("SELECT `fio`,`org_name` FROM `gazeta_client` WHERE `id`=".$zayav->client_id);
        $client = '<a href="'.URL.'&p=gazeta&d=client&d1=info&id='.$zayav->client_id.'">'.($client->org_name ? $client->org_name : $client->fio).'</a>'.
                  '<INPUT TYPE=hidden id=client_id value='.$zayav->client_id.'>';
    } else {
        $client = '<INPUT TYPE=hidden id=client_id>';
    }
    switch ($zayav->category) {
        case 1:
            $for_ob = '<TR><TD class=tdAbout>�������:'.
                          '<TD><INPUT TYPE=hidden id=rubrika value='.$zayav->rubrika.'>'.
                              '<INPUT TYPE=hidden id=podrubrika value='.$zayav->podrubrika.'>'.
                      '<TR><TD class=tdAbout valign=top>�����:'.
                          '<TD><TEXTAREA id=txt>'.textUnFormat($zayav->txt).'</TEXTAREA><DIV id=txtCount></DIV>'.
                      '<TR><TD class=tdAbout>���������� �������:'.
                          '<TD><INPUT TYPE=text id=telefon maxlength=200 value="'.textUnFormat($zayav->telefon).'">'.
                      '<TR><TD class=tdAbout>�����:'.
                          '<TD><INPUT TYPE=text id=adres maxlength=200 value="'.textUnFormat($zayav->adres).'">';
            break;
        case 2:
            $for_rek = '<TR><TD class=tdAbout>������ �����������:'.
                           '<TD><INPUT TYPE=text id=size_x maxlength=5 value="'.round($zayav->size_x, 1).'">'.
                               '<B class=xb>x</B>'.
                               '<INPUT TYPE=text id=size_y maxlength=5 value="'.round($zayav->size_y, 1).'"> = '.
                               '<INPUT TYPE=text id=kv_sm readonly value="'.round($zayav->size_x * $zayav->size_y).'"> ��<SUP>2</SUP>';
            $skidka = '<TABLE cellpadding=0 cellspacing=8 id=skidka_tab>'.
                        '<TR><TD class=tdAbout>������:<TD><INPUT TYPE=hidden id=skidka value='.$zayav->skidka.'>'.
                      '</TABLE>';
            break;
    }
    $gn = $VK->ObjectAss("SELECT `general_nomer` AS `id`,`dop`,`summa` FROM `gazeta_nomer_pub` WHERE `zayav_id`=".$zayav->id." AND `general_nomer`>=".GN_FIRST_ACTIVE);

    // ���������� ���� ��������� �������
    foreach ($gn as $sp) {
        $sp->summa = round($sp->summa, 2);
    }

    $catEdit = array(
        1 => '����������',
        2 => '�������',
        3 => '������������',
        4 => '������'
    );
?>
<DIV id=dopMenu>
    <A HREF='<?=URL?>&p=gazeta&d=zayav&d1=view&id=<?=$zayav->id?>' class=link><I></I><B></B><DIV>��������</DIV><B></B><I></I></A>
    <A class=linkSel><I></I><B></B><DIV>��������������</DIV><B></B><I></I></A>
</DIV>
<DIV id=zayavAdd class=edit>
    <DIV class=headName>�������������� <?=$catEdit[$zayav->category]?> �<?=$zayav->id?></DIV>

    <input type=hidden id=category value="<?=$zayav->category?>">

    <TABLE cellpadding=0 cellspacing=8>
        <TR><TD class=tdAbout>������:<TD><?=$client?>
        <?=@$for_ob?>
        <?=@$for_rek?>
    </TABLE>

    <TABLE cellpadding=0 cellspacing=8>
        <TR><TD class=tdAbout>�����������:<TD id=foto>
    </TABLE>
    <input type=hidden id=foto_link value="<?=$zayav->file?>">

    <TABLE cellpadding=0 cellspacing=8><TR><TD class=tdAbout>������ �������:<TD></TABLE>
    <DIV id=gn_spisok></DIV>

    <?=@$skidka?>

    <TABLE cellpadding=0 cellspacing=8 id=manual_tab>
        <TR><TD class=tdAbout>������� ��������� �������:<TD><INPUT TYPE=hidden id=summa_manual value=<?=$zayav->summa_manual?>>
    </TABLE>

    <TABLE cellpadding=0 cellspacing=8>
        <TR><TD class=tdAbout>�������� ���������:<TD><INPUT TYPE=text id=summa readonly value=0> ���.
                <SPAN id=sumSkidka></SPAN><INPUT TYPE=hidden id=skidka_sum value=0>
    </TABLE>

    <DIV class=vkButton><BUTTON onclick="zayavAddGo(this,<?=$zayav->id?>);">���������</BUTTON></DIV>
    <DIV class=vkCancel><BUTTON onclick="location.href='<?=URL?>&p=gazeta&d=zayav&d1=view&id=<?=$zayav->id?>'">������</BUTTON></DIV>
</DIV>
<SCRIPT type="text/javascript" src="/view/gazeta/zayav/add/zayavAddEdit.js?<?=JS_VERSION?>"></SCRIPT>
<SCRIPT type="text/javascript">zayavEdit(<?=$zayav->category.','.$zayav->client_id.','.json_encode($gn)?>);</SCRIPT>
<?php
}  // end of zayavEdit()

function reportView() {
    global $VK;
    switch(@$_GET['d1']) {
        case 'money':
        default: $d1 = 'money'; $money = 'sel'; break;
        case 'zayav': $d1 = 'zayav'; $zayav = 'sel'; break;
        case 'log': $d1 = 'log'; $log = 'sel'; break;
    }
    $report = reportGet($d1);
?>
<TABLE cellpadding=0 cellspacing=0 id=report>
    <TR><TD id=cont><?=$report->content?>&nbsp;
        <TD id=right>
            <DIV class=infoLink>
                <a href="<?=URL?>&p=gazeta&d=report&d1=money" class="<?=@$money?>">������</a>
                <a href="<?=URL?>&p=gazeta&d=report&d1=zayav" class="<?=@$zayav?>">������</a>
                <a href="<?=URL?>&p=gazeta&d=report&d1=log" class="<?=@$log?>">������� ��������</a>
            </DIV>
        <?=$report->right?>
</TABLE>
<div id=report_dialog></div>
<?php
echo $report->js;
} // end of reportView()

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
