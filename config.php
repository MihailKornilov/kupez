<?php
// Включение работы куков через фрейм в IE
header('P3P: CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');

define('TIME', microtime(true));
define('SA', 982006); // назначение суперадминистратора
define('DOMAIN', $_SERVER["SERVER_NAME"]);

switch (DOMAIN) {
    case 'kupez': // localhost
        ini_set('display_errors',1);
        error_reporting(E_ALL);
        $mysql = array(
            'host' => '127.0.0.1',
            'user' => 'root',
            'pass' => '4909099',
            'database' => 'kupez',
            'names' => 'cp1251'
        );
        define('PATH', 'c:/www/kupez/');
        define('VIEWER_ID', 982006);
        break;
    case 'kupez.nyandoma.ru':
        $mysql = array(
            'host' => 'a6460.mysql.mchost.ru',
            'user' => 'a6460_kupez',
            'pass' => '4909099',
            'database' => 'a6460_kupez',
            'names' => 'cp1251'
        );
        define('PATH', '/home/httpd/vhosts/nyandoma.ru/subdomains/kupez/httpdocs/');
        define('VIEWER_ID', $_GET['viewer_id']);
        define('API_ID', 2881875);
        define('API_SECRET', 'h9IjOkxIMwoW8agQkW3M');
        define('API_AUTH_KEY', $_GET['auth_key']);
        apiAuth();
        break;
    case 'kupeztest.nyandoma.ru':
        ini_set('display_errors',1);
        error_reporting(E_ALL);
        $mysql = array(
            'host' => 'a6460.mysql.mchost.ru',
            'user' => 'a6460_kupeztest',
            'pass' => '4909099',
            'database' => 'a6460_kupeztest',
            'names' => 'cp1251'
        );
        define('PATH', '/home/httpd/vhosts/nyandoma.ru/subdomains/kupeztest/httpdocs/');
        define('VIEWER_ID', $_GET['viewer_id']);
        define('API_ID', 3495523);
        define('API_SECRET', 'acnJyLI2QDM6yTXQXcwC');
        define('API_AUTH_KEY', $_GET['auth_key']);
        apiAuth();
        break;
    default: echo 'domain error'; exit; break;
}

if (!defined('API_ID')) define('API_ID', '');
if (!defined('API_AUTH_KEY')) define('API_AUTH_KEY', '');

define('VALUES', 'viewer_id='.VIEWER_ID.'&api_id='.API_ID."&auth_key=".API_AUTH_KEY);
define('URL', 'http://'.DOMAIN.'/index.php?'.VALUES);

require_once('include/class_MysqlDB.php');
$VK = new MysqlDB($mysql['host'],$mysql['user'],$mysql['pass'],$mysql['database'],$mysql['names']);

$G = $VK->QueryObjectOne("SELECT * FROM `setup_global` LIMIT 1");
define('JS_VERSION',       $G->script_style);
define('CSS_VERSION',      $G->script_style);
define('G_VALUES_VERSION', $G->g_values);
define('KASSA_START',      $G->kassa_start);



// Авторизация пользователя ВКонтакте
function apiAuth()
{
    if ($_GET['auth_key'] != md5($_GET['api_id']."_".VIEWER_ID."_".API_SECRET)) {
        echo 'auth error';
        exit;
    }
}

/* установка баланса клиента */
function setClientBalans($client_id = 0) {
    if ($client_id > 0) {
        global $VK;
        $rashod = $VK->QRow("SELECT SUM(`summa`) FROM `gazeta_zayav` WHERE `client_id`=".$client_id);
        $prihod = $VK->QRow("SELECT SUM(`sum`) FROM `gazeta_money` WHERE `status`=1 AND `client_id`=".$client_id);
        $balans = $prihod - $rashod;
        $zayav_count = $VK->QRow("SELECT COUNT(`id`) FROM `gazeta_zayav` WHERE `client_id`=".$client_id);
        $VK->Query("UPDATE `gazeta_client` SET
                        `balans`=".$balans.",
                        `zayav_count`=".$zayav_count." WHERE `id`=".$client_id);
        return $balans;
    } else {
        return 0;
    }
}
?>