<?php
/*
 * ¬озвращает Ajax результат со списком объ€влений
 * дл€ редактировани€ дл€ отдельного пользовател€
 * его личных объ€влений.
 * “акже все объ€влени€ дл€ администратора.
*/
require_once('../../../include/AjaxHeader.php');

$find = "WHERE `viewer_id_add`>0";

// показ объ€влений дл€ выбранного пользовател€ (администрирование)
if ($_GET['viewer_id_add'] > 0) {
  $find .= " AND `viewer_id_add`=".$_GET['viewer_id_add'];
}

if ($_GET['menu'] == 1)
    $find .= " AND `day_active`>=DATE_FORMAT(NOW(),'%Y-%m-%d')";

if ($_GET['menu'] == 2)
    $find .= " AND `day_active`<DATE_FORMAT(NOW(),'%Y-%m-%d')";

$send = AjaxSpisokCreate("SELECT * FROM `vk_ob` ".$find);
if (count($send->spisok) > 0) {
    $spisok = $send->spisok;
    $send->spisok = array();

    // —писок пользователей, размещавших объ€влени€
    $viewers = array();
    foreach ($spisok as $sp)
        array_push($viewers, $sp->viewer_id_add);

    if (count($viewers) > 0)
        $viewers = $VK->ObjectAss("SELECT
                                     `viewer_id` AS `id`,
                                     CONCAT(`first_name`,' ',`last_name`) AS `name`,
                                     `photo`
                                   FROM `vk_user`
                                   WHERE `viewer_id` IN (".implode(',',array_unique($viewers)).")");

    foreach($spisok as $sp) {
        $srok = strtotime($sp->day_active) - time() + 86400;
        $active = 0;
        $day_last = 0;
        if($srok > 0) {
            $active = 1;
            $day = floor($srok / 86400);
            $day_last = utf8("ќстал".ends($day, 'с€ ', 'ось ').$day.ends($day, ' день', ' дн€', ' дней'));
        }
        array_push($send->spisok, array(
            'id' => $sp->id,
            'rubrika' => $sp->rubrika,
            'podrubrika' => $sp->podrubrika,
            'txt' => utf8($sp->txt),
            'telefon' => utf8($sp->telefon),
            'file' => $sp->file,
            'dop' => $sp->dop,
            'viewer_id' => $sp->viewer_id_add,
            'viewer_id_show' => $sp->viewer_id_show,
            'viewer_name' => utf8($viewers[$sp->viewer_id_add]->name),
            'viewer_photo' => $viewers[$sp->viewer_id_add]->photo,
            'dtime' => utf8(FullData($sp->dtime_add,1)),
            'active' => $active,
            'day_last' => $day_last,
            'country_id' => $sp->country_id,
            'country_name' => utf8($sp->country_name),
            'city_id' => $sp->city_id,
            'city_name' => utf8($sp->city_name)
        ));
    }
}


echo json_encode($send);
?>



