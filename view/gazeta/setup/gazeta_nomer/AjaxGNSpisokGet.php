<?php
require_once('../../../../include/AjaxHeader.php');

// Формирование текста с днями недели: 31 декабря - 6 января 2013
function weekTxtCreate($day_begin, $day_end) {
    global $MonthFull;
    $b = explode('-', $day_begin);
    $e = explode('-', $day_end);
    $db = abs($b[2]).' '.$MonthFull[$b[1]].($b[0] != $e[0] ? ' '.$b[0] : '');
    $de = abs($e[2]).' '.$MonthFull[$e[1]].' '.$e[0];
    return $db.' - '.$de;
}

$send->spisok = array();

$spisok = $VK->QueryObjectArray("SELECT * FROM `gazeta_nomer` WHERE `day_public` LIKE '".$_GET['year']."-%' ORDER BY `general_nomer`");
if(count($spisok) > 0) {
    $curr = time();
    $zayav_count = $VK->QueryPtPArray("SELECT `general_nomer`,COUNT(`id`) FROM `gazeta_nomer_pub` GROUP BY `general_nomer`");
    foreach($spisok as $sp) {
        array_push($send->spisok, array(
            'general_nomer' => $sp->general_nomer,
            'grey' => $curr > strtotime($sp->day_print) + 86400 ? 'grey' : '',
            'week_nomer' => $sp->week_nomer,
//            'day_txt' => utf8(weekTxtCreate($sp->day_begin, $sp->day_end)),

//            'day_begin_val' => $sp->day_begin,
//            'day_begin' => utf8(FullData($sp->day_begin,1)),
//            'day_end_val' => $sp->day_end,
//            'day_end' => utf8(FullData($sp->day_end,1)),
    
            'day_print' => utf8(FullData($sp->day_print,1)),
            'day_print_val' => $sp->day_print,
            'day_public' => utf8(FullData($sp->day_public,1)),
            'day_public_val' => $sp->day_public,
            'zayav_count' => isset($zayav_count[$sp->general_nomer]) ? $zayav_count[$sp->general_nomer] : 0
        ));
    }
}
echo json_encode($send);
?>



