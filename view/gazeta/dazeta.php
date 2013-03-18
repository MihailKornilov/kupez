<?php
// Основное горизонтальное меню
function main_links($g) {
    $name = array('Клиенты', 'Заявки', 'Отчёты', 'Настройки');
    $page = array('client',  'zayav',  'report', 'setup');

    $g_page = 'client';
    for ($n = 0; $n < count($page); $n++) {
        if ($g == $page[$n])
            $g_page = $g;
    }
    $links = '<A href="'.URL.'&p=ob"style="float:right;margin:5px 5px 0 15px;" onclick="setCookie(\'enter\',0);">Выход</A>';
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

/* установка баланса клиента */
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

// Список клиентов
function clientSpisok() {
    global $VK;
?>
<DIV id=clientFind></DIV>
<DIV id=findResult>&nbsp;</DIV>
<TABLE cellpadding=0 cellspacing=0 id=client>
<TR>
	<TD id=spisok>&nbsp;
	<TD id=cond>
		<DIV id=buttonCreate><A onclick="ca();">Новый клиент</A></DIV>
		<BR><BR>
		<INPUT TYPE=hidden id=cDolg value=0>
        <BR><BR>
		<INPUT TYPE=hidden id=personFind value=0>
</TABLE>
<SCRIPT type="text/javascript" src="/include/clientAdd/clientAdd.js?<?=JS_VERSION?>"></SCRIPT>
<SCRIPT type="text/javascript">
var Client = {
    spisok:<?= $VK->vkSelJson("select id,name from setup_person order by sort"); ?>
};
</SCRIPT>
<SCRIPT type="text/javascript" src="/view/gazeta/client/spisok/clientSpisok.js?<?=JS_VERSION?>"></SCRIPT>
<?php
} // end of clientSpisok()

// Список заявок
function zayavSpisok() {
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
           .',title:"'.$sp->week_nomer.' ('.$sp->general_nomer.') выход '.$pub.'"'
           .',content:"<B'.$grey.'>'.$sp->week_nomer.'</B><SPAN'.$grey.'>('.$sp->general_nomer.')</SPAN><TT>выход '.$pub.'</TT>"}'
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
            <DIV id=buttonCreate><A onclick="location.href='<?=URL?>&p=gazeta&d=zayavAadd';">Новая заявка</A></DIV>
            <DIV id=fastFind></DIV>
            <DIV id=nofast>
                <BR><BR>
                <DIV class=findName>Категория</DIV><INPUT TYPE=hidden id=category value=0>
                <INPUT TYPE=hidden id=type_gaz value=0>
                <BR>
                <DIV class=findName>Номер газеты</DIV><INPUT TYPE=hidden id=year value=<?=strftime("%Y",time())?>>
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

// Страница с настройками
function setupView($admin) {
    global $VK;
    // Получение начального и конечного года для настройки номеров газет
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
    <DIV class=razdel><INPUT type=hidden id=razdelSel value=<?=(@$_GET['id']?$_GET['id']:6)?>></DIV>
    <DIV id=edit></DIV>
    <DIV id=setup_dialog></DIV>
</DIV>
<SCRIPT type="text/javascript" src="/view/gazeta/setup/setup.js?<?=JS_VERSION?>"></SCRIPT>
<SCRIPT type="text/javascript">

$("#razdelSel").vkSel({
    width:300,
    spisok:[
        <?=($admin == 1 ? "{uid:8,title:'Доступ и права сотрудников'}," : '')?>
        {uid:1,title:'Заявители'},
        {uid:2,title:'Рубрики'},
        {uid:7,title:'Подрубрики'},
        {uid:9,title:'Стоимость длины объявления'},
        {uid:6,title:'Дополнительные параметры объявления'},
        {uid:4,title:'Стоимость см2 для каждой полосы',content:'Стоимость см&sup2; для каждой полосы'},
        {uid:3,title:'Номера выпусков газеты'},
        {uid:5,title:'Скидки'}],
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