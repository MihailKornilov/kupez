<?php
require_once('../../../../include/AjaxHeader.php');

// ������ ������
$first_week = strtotime($_POST['first_day_public']);

// ����� ��� ������ ������
$first_print = date("w", $first_week);
if ($first_print == 0) $first_print = 7;

// ���������� ������ ������ � ������������
if ($first_print != 1) {
    $first_week -= 86400 * ($first_print - 1);
}

// ����������� ������� ��� ���������� ����, ���� ���� �� ���� ������, �� ���������
$timeEnd = strtotime(($_POST['year'] + 1)."-01-01");
$gnArr = array();
while($first_week < $timeEnd) {
    array_push($gnArr, '(
        '.$_POST['general_nomer'].',
        '.$_POST['week_nomer'].',
        DATE_ADD("'.strftime("%Y-%m-%d", $first_week).'", INTERVAL '.$_POST['day_print'].' DAY),
        DATE_ADD("'.strftime("%Y-%m-%d", $first_week).'", INTERVAL '.$_POST['day_public'].' DAY),
        '.VIEWER_ID.')'
    );

    $_POST['general_nomer']++;
    $_POST['week_nomer']++;
    $first_week += 604800;
}

$VK->Query('INSERT INTO `gazeta_nomer` (
`general_nomer`,
`week_nomer`,
`day_print`,
`day_public`,
`viewer_id_add`
) values '.implode(',', $gnArr));

GvaluesCreate();

$VK->Query('INSERT INTO `gazeta_log`
                (`type`,`value`,`viewer_id_add`)
            VALUES
                (1034,"'.$_POST['year'].'",'.VIEWER_ID.')');

$send = 1;

echo json_encode($send);
