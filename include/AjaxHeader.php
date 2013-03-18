<?php
header("Content-type: text/html; charset=windows-1251");
header("Cache-Control: no-store, no-cache,must-revalidate"); 
header("Expires: ".date('r'));
require_once('global_functions.php');
require_once(realpath(__DIR__.'/../config.php'));
//setlocale(LC_ALL, 'ru_RU.CP1251', 'rus_RUS.CP1251', 'Russian_Russia.1251');
?>
