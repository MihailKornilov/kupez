<?php
require_once('../../../../include/AjaxHeader.php');

$val = $VK->QRow('SELECT '.$_POST['name'].' FROM `setup_global`');
if ($val != $_POST['val']) {
    $VK->Query("update setup_global set ".$_POST['name']."=".$_POST['val']);

    GvaluesCreate();

    $cost = $VK->QueryObjectOne('SELECT * FROM `setup_global`');
    $value = 'Первые '.$cost->txt_len_first.' симв. = '.$cost->txt_cena_first.' руб. '.
             'Следующие '.$cost->txt_len_next.' симв. = '.$cost->txt_cena_next.' руб.';

    $VK->Query('INSERT INTO `gazeta_log`
                    (`type`,`value`,`viewer_id_add`)
                VALUES
                    (1091,"'.win1251($value).'",'.VIEWER_ID.')');
}

echo 1;
?>



