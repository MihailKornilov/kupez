<?php
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