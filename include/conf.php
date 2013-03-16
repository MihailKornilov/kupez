<?php
define('TIME', microtime(true));
$T = getTime();


/* LOCALHOST */
if($_SERVER["SERVER_NAME"] == 'kupez') {
  ini_set('display_errors',1); error_reporting(E_ALL);
  $mysql=array(
    'host' => '127.0.0.1',
    'user' => 'root',
    'pass' => '4909099',
    'database' => 'kupez',
    'names' => 'cp1251'
  );

  $PATH_FILES = "c:/www/kupez/files/";
  $domain = 'http://kupez';
  $_GET['viewer_id'] = 982006;
}
/* END LOCALHOST */


if($_SERVER["SERVER_NAME"] == 'kupez.nyandoma.ru') {
  $mysql=array(
    'host' => 'a6460.mysql.mchost.ru',
    'user' => 'a6460_kupez',
    'pass' => '4909099',
    'database' => 'a6460_kupez',
    'names' => 'cp1251'
  );
  $PATH_FILES = "/home/httpd/vhosts/nyandoma.ru/subdomains/kupez/httpdocs/files/";
  $domain = "http://kupez.nyandoma.ru";
}


$values = "viewer_id=".$_GET['viewer_id']
    .'&api_id=2881875'
    ."&auth_key=".(isset($_GET['auth_key']) ? $_GET['auth_key'] : '');
define('DOMAIN', $domain);
define('VALUES', $values);
define('URL', DOMAIN.'/index.php?'.VALUES);
define('SA', 982006); // назначение суперадминистраторов

/* ИНИЦИАЛИЗАЦИЯ БАЗЫ ДАННЫХ, ВЫБОР МАСТЕРСКОЙ И ЗАГРУЗКА ДАННЫХ СОТРУДНИКА */
header('P3P: CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"'); // ВКЛЮЧАЕТ РАБОТУ КУКОВ В ie ЧЕРЕЗ ФРЕЙМ
require_once('class_MysqlDB.php');

$VK = new MysqlDB($mysql['host'],$mysql['user'],$mysql['pass'],$mysql['database'],$mysql['names']);

$G = $VK->QueryObjectOne("SELECT * FROM `setup_global` LIMIT 1");
define('JS_VERSION', $G->script_style);
define('CSS_VERSION', $G->script_style);
define('G_VALUES_VERSION', $G->g_values);


$zayavCategory = array(
  1 => 'Объявление',
  2 => 'Реклама',
  3 => 'Поздравление',
  4 => 'Статья'
);


/* форматирование текста для внесения в базу */
function textFormat($txt) {
  $txt = str_replace("'","&#039;", $txt);
  $txt = str_replace("<","&lt;", $txt);
  $txt = str_replace(">","&gt;", $txt);
  return str_replace("\n","<BR>", $txt);
}

function textUnFormat($txt) {
  $txt=str_replace("&#039;","'",$txt);
  $txt=str_replace("&lt;","<",$txt);
  $txt=str_replace("&gt;",">",$txt);
  return str_replace("<BR>","\n",$txt);
}



/* установка баланса клиента */
function setClientBalans($client_id = 0) {
  if ($client_id > 0) {
    global $VK;
    $rashod = $VK->QRow("select sum(summa) from zayav where client_id=".$client_id);
    $prihod = $VK->QRow("select sum(summa) from oplata where status=1 and client_id=".$client_id);
    $balans = $prihod - $rashod;
    $VK->Query("update client set balans=".$balans." where id=".$client_id);
    return $balans;
  } else {
    return 0;
  }
}


function win1251($txt) { return iconv("UTF-8","WINDOWS-1251",$txt); }
function utf8($txt) { return iconv("WINDOWS-1251","UTF-8",$txt); }
function curTime () { return strftime("%Y-%m-%d %H:%M:%S",time()); }



$MonthFull = array(
  1=>'января',
  2=>'февраля',
  3=>'марта',
  4=>'апреля',
  5=>'мая',
  6=>'июня',
  7=>'июля',
  8=>'августа',
  9=>'сентября',
  10=>'октября',
  11=>'ноября',
  12=>'декабря',
  '01'=>'января',
  '02'=>'февраля',
  '03'=>'марта',
  '04'=>'апреля',
  '05'=>'мая',
  '06'=>'июня',
  '07'=>'июля',
  '08'=>'августа',
  '09'=>'сентября'
);

$MonthCut = array(
  1=>'янв',
  2=>'фев',
  3=>'мар',
  4=>'апр',
  5=>'мая',
  6=>'июн',
  7=>'июл',
  8=>'авг',
  9=>'сент',
  10=>'окт',
  11=>'ноя',
  12=>'дек',
  '01'=>'янв',
  '02'=>'фев',
  '03'=>'мар',
  '04'=>'апр',
  '05'=>'мая',
  '06'=>'июн',
  '07'=>'июл',
  '08'=>'авг',
  '09'=>'сен'
);


$WeekName = array(
  1=>'пн',
  2=>'вт',
  3=>'ср',
  4=>'чт',
  5=>'пт',
  6=>'сб',
  0=>'вс'
);

function FullData($value, $cut = 0, $week = 0, $yYear = 0) {
  // 14 апреля 2010
  global $MonthFull,$MonthCut,$WeekName;
  $d=explode("-",$value);
  if($yYear) if($d[0]==strftime("%Y",time())) $d[0]=''; // если eYear!=0, а также год совпадает с текущим, то не отображаем его
  return ($week!=0?$WeekName[date('w',strtotime($value))].". ":'').abs($d[2])." ".($cut==0?$MonthFull[$d[1]]:$MonthCut[$d[1]])." ".$d[0];
}

function FullDataTime($value, $cut = 0) {
  // 14 апреля 2010 в 12:45
  global $MonthFull,$MonthCut;
  $arr=explode(" ",$value);
  $d=explode("-",$arr[0]);
  $t=explode(":",$arr[1]);
  return abs($d[2])." ".($cut==0?$MonthFull[$d[1]]:$MonthCut[$d[1]]).(date('Y')==$d[0]?'':' '.$d[0])." в ".$t[0].":".$t[1];
}

// получение списка для select. Пример: "select id,name from table"
function vkSelGetJson($q) {
  global $VK;
  $send = array();
  $spisok = $VK->QueryRowArray($q);
  if (count($spisok) > 0) {
    foreach($spisok as $sp) {
      array_push($send, array(
        'uid' => $sp[0],
        'title' => utf8($sp[1])
      ));
    }
  }
  return json_encode($send);
}


// текущее время в миллисекундах
function getTime($start = 0) {
  $arr = explode(' ', microtime());
  return round($arr[1] + $arr[0] - $start, 3);
}



  // обновление количества объявлений для рубрики
function rubrikaCountUpdate($rub) {
  global $VK;
  $count = $VK->QRow("select count(id) from zayav where rubrika=".$rub." and status=1 and category=1 and active_day>='".strftime("%Y-%m-%d",time())."'");
  $VK->Query("update setup_rubrika set ob_count=".$count." where id=".$rub);
  xcache_unset('rubrikaCount');
  xcache_unset('obSpisokFirst');
}

?>