<?php
// Формирование окончаний
function ends($count, $e1, $e2, $e5) {
    if (!$e5) { $e5 = $e2; }
    if($count/10%10 == 1) { return $e5; }
    else {
        switch($count%10) {
            case '1': return $e1;
            case '2': return $e2;
            case '3': return $e2;
            case '4': return $e2;
            default: return $e5;
        }
    }
} // end of ends()

function win1251($txt) { return iconv("UTF-8", "WINDOWS-1251", $txt); }
function utf8($txt) { return iconv("WINDOWS-1251", "UTF-8", $txt); }
function curTime() { return strftime("%Y-%m-%d %H:%M:%S", time()); }

/* форматирование текста для внесения в базу */
function textFormat($txt) {
    $txt = str_replace("'","&#039;", $txt);
    $txt = str_replace("<","&lt;", $txt);
    $txt = str_replace(">","&gt;", $txt);
    return str_replace("\n","<BR>", $txt);
}
function textUnFormat($txt) {
    $txt=str_replace("&#039;","'",$txt);
    $txt=str_replace("&lt;","<",$txt);
    $txt=str_replace("&gt;",">",$txt);
    return str_replace("<BR>","\n",$txt);
}

$MonthFull = array(
    1=>'января',
    2=>'февраля',
    3=>'марта',
    4=>'апреля',
    5=>'мая',
    6=>'июня',
    7=>'июля',
    8=>'августа',
    9=>'сентября',
    10=>'октября',
    11=>'ноября',
    12=>'декабря',
    '01'=>'января',
    '02'=>'февраля',
    '03'=>'марта',
    '04'=>'апреля',
    '05'=>'мая',
    '06'=>'июня',
    '07'=>'июля',
    '08'=>'августа',
    '09'=>'сентября'
);

$MonthCut = array(
    1=>'янв',
    2=>'фев',
    3=>'мар',
    4=>'апр',
    5=>'мая',
    6=>'июн',
    7=>'июл',
    8=>'авг',
    9=>'сент',
    10=>'окт',
    11=>'ноя',
    12=>'дек',
    '01'=>'янв',
    '02'=>'фев',
    '03'=>'мар',
    '04'=>'апр',
    '05'=>'мая',
    '06'=>'июн',
    '07'=>'июл',
    '08'=>'авг',
    '09'=>'сен'
);

$WeekName = array(
    1=>'пн',
    2=>'вт',
    3=>'ср',
    4=>'чт',
    5=>'пт',
    6=>'сб',
    0=>'вс'
);

function FullData($value, $cut = 0, $week = 0, $yYear = 0) {
    // 14 апреля 2010
    global $MonthFull,$MonthCut,$WeekName;
    $d=explode("-",$value);
    if($yYear) if($d[0]==strftime("%Y",time())) $d[0]=''; // если eYear!=0, а также год совпадает с текущим, то не отображаем его
    return ($week!=0?$WeekName[date('w',strtotime($value))].". ":'').abs($d[2])." ".($cut==0?$MonthFull[$d[1]]:$MonthCut[$d[1]])." ".$d[0];
}

function FullDataTime($value, $cut = 0) {
    // 14 апреля 2010 в 12:45
    global $MonthFull,$MonthCut;
    $arr=explode(" ",$value);
    $d=explode("-",$arr[0]);
    $t=explode(":",$arr[1]);
    return abs($d[2])." ".($cut==0?$MonthFull[$d[1]]:$MonthCut[$d[1]]).(date('Y')==$d[0]?'':' '.$d[0])." в ".$t[0].":".$t[1];
}

// Составление Ajax-списка
function AjaxSpisokCreate($sql) {
    global $VK;
    if (!@$_GET['order']) $_GET['order'] = 'id';
    if (!@$_GET['sort']) $_GET['sort'] = 'desc';
    $send->all = $VK->QNumRows($sql);
    $send->next = 0;
    $send->spisok = $VK->QueryObjectArray($sql." ORDER BY ".$_GET['order']." ".$_GET['sort']." LIMIT ".$_GET['start'].",".$_GET['limit']);
    if (count($send->spisok) > 0)
        if($VK->QNumRows($sql." LIMIT ".($_GET['start'] + $_GET['limit']).",".$_GET['limit']) > 0)
            $send->next = 1;
    $send->time = round(microtime(true) - TIME, 3);
    return $send;
} // end of AjaxSpisokCreate()

// составление файла G_values.js
function GvaluesCreate() {
    global $VK;
    $save = 'function SpisokToAss(s){var a=[];for(var n=0;n<s.length;a[s[n].uid]=s[n].title,n++);return a;}';

    $save .= 'G.category_spisok = [{uid:1,title:"Объявление"},{uid:2,title:"Реклама"},{uid:3,title:"Поздравление"},{uid:4,title:"Статья"}];G.category_ass = SpisokToAss(G.category_spisok);';
    $save .= "G.rubrika_spisok = ".$VK->vkSelJson('SELECT `id`,`name` FROM `setup_rubrika` ORDER BY `sort`').";G.rubrika_ass = SpisokToAss(G.rubrika_spisok);";
    $save .= "G.person_spisok = ".$VK->vkSelJson('SELECT `id`,`name` FROM `setup_person` ORDER BY `sort`').";G.person_ass = SpisokToAss(G.person_spisok);";
    $save .= "G.polosa_spisok = ".$VK->vkSelJson('SELECT `id`,`name` FROM `setup_polosa_cost` ORDER BY `sort`').";G.polosa_ass = SpisokToAss(G.polosa_spisok);";
    $save .= "G.skidka_spisok = ".$VK->vkSelJson('SELECT `id`,`razmer` FROM `setup_skidka` ORDER BY `id`').";G.skidka_ass = SpisokToAss(G.skidka_spisok);";

    $spisok = $VK->QueryObjectArray("SELECT `id`,`name`,`rubrika_id` FROM `setup_pod_rubrika` ORDER BY `rubrika_id`,`sort`");
    $podrubrika = array();
    if (count($spisok) > 0) {
        foreach ($spisok as $sp) {
            if (!isset($podrubrika[$sp->rubrika_id])) { $podrubrika[$sp->rubrika_id] = array(); }
            array_push($podrubrika[$sp->rubrika_id], '{uid:'.$sp->id.',title:"'.$sp->name.'"}');
        }
        $v = array();
        foreach ($podrubrika as $n => $sp) { array_push($v, $n.":[".implode(',',$sp)."]"); }
        $podrubrika = $v;
    }
    $save .= "G.podrubrika_spisok = {".implode(',',$podrubrika)."};";
    $save .= "G.podrubrika_ass = []; G.podrubrika_ass[0] = ''; for (var k in G.podrubrika_spisok) { for (var n = 0; n < G.podrubrika_spisok[k].length; n++) { var sp = G.podrubrika_spisok[k][n]; G.podrubrika_ass[sp.uid] = sp.title; } }";

    $fp = fopen(PATH."/js/G_values.js","w+");
    fwrite($fp, $save);
    fclose($fp);

    $VK->Query("update setup_global set g_values=g_values+1");
} // end of GvaluesCreate()
?>