<?php
require_once('../../../../include/AjaxHeader.php');

setlocale(LC_ALL, 'ru_RU.CP1251', 'rus_RUS.CP1251', 'Russian_Russia.1251');

function repl($pole) { global $input; return utf8(preg_replace("/(".$input.")/i", "<EM>\\1</EM>", $pole, 1)); }

switch ($_GET['order']) {
    default:
        $_GET['order'] = 'dtime_add';
        $_GET['sort'] = 'DESC';
        break;
    case 2:
        $_GET['order'] = 'activity';
        $_GET['sort'] = 'ASC';
        break;
}

$find="WHERE `id`";

if ($_GET['input']) {
    $input = win1251($_GET['input']);
    $find .= " AND (`org_name` LIKE '%".$input."%' OR
                  `fio` LIKE '%".$input."%' OR
                  `telefon` LIKE '%".$input."%' OR
                  `adres` LIKE '%".$input."%' OR
                  `inn` LIKE '%".$input."%' OR
                  `kpp` LIKE '%".$input."%' OR
                  `email` LIKE '%".$input."%')";
}

if ($_GET['person'] > 0)
    $find.=" AND `person`=".$_GET['person'];

if ($_GET['skidka'] > 0)
    $find .= " AND `skidka`=".$_GET['skidka'];

if ($_GET['dolg'] == 1)
    $find .= " AND `balans`<0";

$send = AjaxSpisokCreate("SELECT * FROM `gazeta_client` ".$find);

if ($_GET['dolg'] == 1)
    $send->dolg = round($VK->QRow("SELECT SUM(`balans`) FROM `gazeta_client` ".$find) * -1, 2);

if (count($send->spisok) > 0) {
    $spisok = $send->spisok;
    $send->spisok = array();

    foreach($spisok as $sp) {
        $push = array('id' => $sp->id);
        $push['org_name'] = utf8($sp->org_name);
        $push['fio'] = utf8($sp->fio);
        if ($sp->telefon) $push['telefon'] = utf8($sp->telefon);
        if ($sp->zayav_count > 0) $push['zayav_count'] = $sp->zayav_count;
        if ($sp->balans != 0) $push['balans'] = round($sp->balans, 2);

        if($_GET['input']) {
            $push['org_name'] = repl($sp->org_name);
            $push['fio'] =      repl($sp->fio);
            $push['telefon'] =  repl($sp->telefon);
            if (preg_match("/(".$input.")/i", $sp->adres)) $push['adres'] = repl($sp->adres);
            else if (preg_match("/(".$input.")/i", $sp->inn)) $push['inn'] = repl($sp->inn);
            else if (preg_match("/(".$input.")/i", $sp->kpp)) $push['kpp'] = repl($sp->kpp);
            else if (preg_match("/(".$input.")/i", $sp->email)) $push['kpp'] = repl($sp->email);
        }

        if ($_GET['order'] == 'activity') {
            $push['activity'] =  utf8($sp->activity == '0000-00-00' ? 'нет' : FullData($sp->activity));
        }

        array_push($send->spisok, $push);
    }
}

echo json_encode($send);
?>