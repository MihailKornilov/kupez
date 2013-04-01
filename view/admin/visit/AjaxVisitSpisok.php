<?php
require_once('../../../include/AjaxHeader.php');

$find="WHERE `viewer_id`";

if ($_GET['input']) {
    if (preg_match('/^\d+$/', $_GET['input']))
        $find .= " AND `viewer_id`=".$_GET['input'];
    else
        $find .= " AND CONCAT(`first_name`,' ',`last_name`) LIKE '%".win1251($_GET['input'])."%'";
} else {
    switch($_GET['radio']) {
        case 2: $find.=" and enter_last>='".strftime("%Y-%m-%d 00:00:00",time())."'"; break; // сегодн€
        case 3: $find.=" and enter_last>='".strftime("%Y-%m-01 00:00:00",time())."'"; break; // в этом мес€це
        case 4: $find.=" and ob_count>0"; break;      // есть объ€влени€
        case 5: $find.=" and app_setup=1"; break;     // установили приложение
        case 6: $find.=" and menu_left_set=1";break;  // ссылка в левом меню
    }
}

$_GET['order'] = 'enter_last';

$send = AjaxSpisokCreate("SELECT * FROM `vk_user` ".$find);
if (count($send->spisok) > 0) {
    $spisok = $send->spisok;
    $send->spisok = array();

    $today = strftime("%Y-%m-%d");
    foreach($spisok as $sp) {
        $push = array(
            'viewer_id' => $sp->viewer_id,
            'name' => utf8($sp->first_name.' '.$sp->last_name),
            'photo' => $sp->photo,
            'place' => utf8($sp->country_name.' '.$sp->city_name)
        );

        if ($sp->ob_count > 0) $push['ob_count'] = $sp->ob_count;

        if($today == substr($sp->enter_last, 0, 10)) {
            $push['count_day'] = $sp->count_day;
            $push['time'] = round(substr($sp->enter_last, 10, 3)).substr($sp->enter_last, 13, 3);
        } else {
            $push['count_day'] = 0;
            $push['time'] = $sp->enter_last != '0000-00-00 00:00:00' ? utf8(FullDataTime($sp->enter_last, 1)) : '-';
        }

        array_push($send->spisok, $push);
    }
}

echo json_encode($send);
?>



