<?php
/* форматирование текста для внесения в базу */
function textFormat($txt) {
    $txt = str_replace("'", "&#039;", $txt);
    $txt = str_replace('"', "&quot;", $txt);
    $txt = str_replace("<", "&lt;", $txt);
    $txt = str_replace(">", "&gt;", $txt);
    return str_replace("\n", "<BR>", $txt);
}
function textUnFormat($txt) {
    $txt = str_replace("&#039;", "'", $txt);
    $txt = str_replace("&quot;", '"', $txt);
    $txt = str_replace("&lt;", "<", $txt);
    $txt = str_replace("&gt;", ">", $txt);
    return str_replace("<BR>", "\n", $txt);
}


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
function AjaxSpisokCreate($sql, $sort='') {
    global $VK;
    if (!@$_GET['order']) $_GET['order'] = 'id';
    if (!@$_GET['sort']) $_GET['sort'] = $sort ? $sort : 'desc';
    $send->all = $VK->QNumRows($sql);
    $send->next = 0;
    $send->spisok = $VK->QueryObjectArray($sql." ORDER BY ".$_GET['order']." ".$_GET['sort']." LIMIT ".$_GET['start'].",".$_GET['limit']);
    if (count($send->spisok) > 0)
        if($VK->QNumRows($sql." LIMIT ".($_GET['start'] + $_GET['limit']).",".$_GET['limit']) > 0)
            $send->next = 1;
    $send->time = round(microtime(true) - TIME, 3);
    return $send;
} // end of AjaxSpisokCreate()

?>