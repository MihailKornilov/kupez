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

// По умолчанию заявка вносится
$log_type = $_POST['client_id'] == 0 ? 1 : 2; // если есть клиент, уточнение, что внесение для клиента

// Если заявка редактируется
if ($_POST['id'] > 0) {
    $send->id = $_POST['id'];
    $log_type = 3;
}

$VK->Query('INSERT INTO `gazeta_log`
                (`type`,`zayav_id`,`client_id`,`viewer_id_add`)
            VALUES
                ('.$log_type.$_POST['category'].','.$send->id.','.$_POST['client_id'].','.VIEWER_ID.')');

// Внесение номеров газет
$VK->Query('DELETE FROM `gazeta_nomer_pub` WHERE `general_nomer`>='.$_POST['gn_first'].' AND `zayav_id`='.$send->id);
$gn_last = 0;
if ($_POST['gns']) {
    $gnArr = explode(',', $_POST['gns']);
    $insert = array();
    foreach ($gnArr as $sp) {
        $gn = explode(':', $sp);
        array_push($insert, '('.$gn[0].','.$gn[1].','.$gn[2].','.$send->id.','.VIEWER_ID.')');
        $gn_last = $gn[0];
    }
    $VK->Query('INSERT INTO `gazeta_nomer_pub` (
                            `general_nomer`,
                            `summa`,
                            `dop`,
                            `zayav_id`,
                            `viewer_id_add`
                            ) values '.implode(',', $insert));
}

//Получение даты активности для клиента
$day_public = '0000-00-00';
if ($gn_last > 0) {
    $day_public = $VK->QRow("SELECT `day_public` FROM `gazeta_nomer` WHERE `general_nomer`=".$gn_last);

    // Внесение объявления для пользователей ВКонтакте
    if ($_POST['category'] == 1 and $_POST['id'] == 0) {
    $VK->Query("INSERT INTO `vk_ob` (
        `rubrika`,
        `podrubrika`,
        `txt`,
        `telefon`,

        `country_id`,
        `country_name`,
        `city_id`,
        `city_name`,

        `file`,
        `day_active`
    ) VALUES (
        ".$_POST['rubrika'].",
        ".$_POST['podrubrika'].",
        '".win1251(textFormat($_POST['txt']))."',
        '".win1251(textFormat($_POST['telefon'].($_POST['adres'] ? ' Адрес: '.$_POST['adres'] : '')))."',

        1,
        '".win1251('Россия')."',
        3644,
        '".win1251('Няндома')."',

        '".$_POST['file']."',
        DATE_ADD('".$day_public."', INTERVAL 30 DAY)
        )");
    }
}

// Обновление общей суммы, количества выходов
$VK->Query('INSERT INTO
                `gazeta_zayav`
                (`id`,`summa`,`gn_count`)
            SELECT
                '.$send->id.' AS `id`,
                IFNULL(SUM(`summa`),0) AS `summa`,
                count(`id`) AS `gn_count`
            FROM `gazeta_nomer_pub`
            WHERE `zayav_id`='.$send->id.'
            ON DUPLICATE KEY UPDATE
                `summa`=VALUES(`summa`),
                `gn_count`=VALUES(`gn_count`)');

// Обновление данных клиента
if ($_POST['client_id'] > 0) {
    $VK->Query('INSERT INTO
                    `gazeta_client` (`id`,`zayav_count`,`activity`)
                SELECT
                    '.$_POST['client_id'].' AS `id`,
                    COUNT(`id`) AS `zayav_count`,
                    "'.$day_public.'" AS `activity`
                FROM `gazeta_zayav` WHERE `client_id`='.$_POST['client_id'].'
                ON DUPLICATE KEY UPDATE
                    `zayav_count`=VALUES(`zayav_count`),
                    `activity`=VALUES(`activity`)');
    // Приведение всех платежей по этой заявке к клиенту
    $VK->Query('UPDATE `gazeta_money` SET `client_id`='.$_POST['client_id'].' WHERE `zayav_id`='.$send->id);
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