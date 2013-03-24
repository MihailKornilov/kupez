<?php
require_once('../../../../include/AjaxHeader.php');

$send->id = $VK->Query("INSERT INTO `gazeta_zayav` (
    `id`,
    `client_id`,
    `category`,

    `rubrika`,
    `podrubrika`,
    `txt`,
    `telefon`,
    `adres`,

    `size_x`,
    `size_y`,

    `summa_manual`,
    `skidka`,
    `skidka_sum`,
    `file`,
    `whence`,
    `viewer_id_add`
) VALUES (
    ".$_POST['id'].",
    ".$_POST['client_id'].",
    ".$_POST['category'].",

    ".$_POST['rubrika'].",
    ".$_POST['podrubrika'].",
    '".win1251(textFormat($_POST['txt']))."',
    '".win1251(textFormat($_POST['telefon']))."',
    '".win1251(textFormat($_POST['adres']))."',

    ".$_POST['size_x'].",
    ".$_POST['size_y'].",

    ".$_POST['summa_manual'].",
    ".$_POST['skidka'].",
    ".$_POST['skidka_sum'].",
    '".$_POST['file']."',
    'kupez',
    ".VIEWER_ID.")
ON DUPLICATE KEY UPDATE
    `client_id`=".$_POST['client_id'].",

    `rubrika`=".$_POST['rubrika'].",
    `podrubrika`=".$_POST['podrubrika'].",
    `txt`='".win1251(textFormat($_POST['txt']))."',
    `telefon`='".win1251(textFormat($_POST['telefon']))."',
    `adres`='".win1251(textFormat($_POST['adres']))."',

    `size_x`='".$_POST['size_x']."',
    `size_y`='".$_POST['size_y']."',

    `summa_manual`=".$_POST['summa_manual'].",
    `skidka`=".$_POST['skidka'].",
    `skidka_sum`=".$_POST['skidka_sum'].",
    `file`='".$_POST['file']."'
");

if ($_POST['id'] > 0) $send->id = $_POST['id'];

// Внесение номеров газет
$VK->Query('DELETE FROM `gazeta_nomer_pub` WHERE `general_nomer`>='.$_POST['gn_first'].' AND `zayav_id`='.$send->id);
    if ($_POST['gns']) {
    $gnArr = explode(',', $_POST['gns']);
    $insert = array();
    foreach ($gnArr as $sp) {
        $gn = explode(':', $sp);
        array_push($insert, '('.$gn[0].','.$gn[1].','.$gn[2].','.$send->id.','.VIEWER_ID.')');
    }
    $VK->Query('INSERT INTO `gazeta_nomer_pub` (
                            `general_nomer`,
                            `summa`,
                            `dop`,
                            `zayav_id`,
                            `viewer_id_add`
                            ) values '.implode(',', $insert));
}

// Обновление общей суммы и количества выходов
$z = $VK->QueryObjectOne('SELECT IFNULL(SUM(`summa`),0) AS `summa`,count(`id`) AS `count` FROM `gazeta_nomer_pub` WHERE `zayav_id`='.$send->id);
$VK->Query('UPDATE `gazeta_zayav` SET `summa`='.$z->summa.',`gn_count`='.$z->count.' WHERE `id`='.$send->id);

// Внесение платежа и кассы
if ($_POST['oplata'] == 1 and $_POST['money'] > 0) {
    $money_id = $VK->Query("INSERT INTO `gazeta_money` (
       `type`,
       `sum`,
       `client_id`,
       `zayav_id`,
       `kassa`,
       `viewer_id_add`
    ) values (
       ".$_POST['money_type'].",
       ".$_POST['money'].",
       ".$_POST['client_id'].",
       ".$send->id.",
       ".$_POST['money_kassa'].",
       ".VIEWER_ID."
    )");

    if ($_POST['money_kassa'] == 1) {
        $VK->Query("INSERT INTO `gazeta_kassa` (
            sum,
            zayav_id,
            money_id,
            viewer_id_add
            ) values (
            ".$_POST['money'].",
            ".$send->id.",
            ".$money_id.",
            ".VIEWER_ID."
        )");
    }
}



if ($_POST['client_id'] > 0) {
    $count = $VK->QRow('SELECT COUNT(`id`) FROM `gazeta_zayav` WHERE `client_id`='.$send->id);
    $VK->Query("UPDATE `gazeta_client` SET `zayav_count`=".$count." WHERE `id`=".$_POST['client_id']);
    setClientBalans($_POST['client_id']);
}


// Заметка
if($_POST['note']) {
    $VK->Query("INSERT INTO `vk_comment` (
        `table_name`,
        `table_id`,
        `txt`,
        `viewer_id_add`
    ) values (
        'zayav',
        ".$send->id.",
        '".win1251(textFormat($_POST['note']))."',
        ".VIEWER_ID."
    )");
}
echo json_encode($send);
?>


