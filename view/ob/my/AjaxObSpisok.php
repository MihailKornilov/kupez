<?php
/*
 * ���������� Ajax ��������� �� ������� ����������
 * ��� �������������� ��� ���������� ������������
 * ��� ������ ����������.
 * ����� ��� ���������� ��� ��������������.
*/


function zayavEnd($count) {
  $ost=$count%10;
  $ost10=$count/10%10;

  if($ost10==1) return '�';
  else
    switch($ost) {
      case '1': return '�';
      case '2': return '�';
      case '3': return '�';
      case '4': return '�';
      default: return '�';
      }
  }

function activeEnd($count) {
  $ost=$count%10;
  $ost10=$count/10%10;

  if($ost10==1) return '��';
  else
    switch($ost) {
      case '1': return '��';
      default: return '��';
      }
  }

function dayEnd($count) {
  $ost=$count%10;
  $ost10=$count/10%10;

  if($ost10==1) return ' ����';
  else
    switch($ost) {
      case '1': return ' ����';
      case '2': return ' ���';
      case '3': return ' ���';
      case '4': return ' ���';
      default: return ' ����';
      }
  }

function ostEnd($count) {
  $ost=$count%10;
  $ost10=$count/10%10;
  if($ost10==1) return '��� ';
  else
    if($ost==1) return '�� ';
    else return '��� ';
}

require_once('../../include/AjaxHeader.php');

$find = "where status=1 and category=1 and whence='vk'";

// ���� ��������� ������ ���������� ��� ������������, �� ��� ��� �� ������������
if ($_GET['type'] == 'local') {
  $find .= " and viewer_id_add=".VIEWER_ID;
  $where = "� ��� ";
} else { $where = ''; }

// ����� ���������� ��� ���������� ������������ (�����������������)
if (isset($_GET['viewer_id_add'])) {
  $find .= " and viewer_id_add=".$_GET['viewer_id_add'];
}

if($VK->QRow("select count(id) from zayav ".$find) == 0) {
  $send->result = utf8("�� �� �� ��������� ����������");
  $send->count = 0;
} else {
  if ($_GET['menu'] == 1) {
    $find.=" and active_day>='".strftime("%Y-%m-%d",time())."'";
    $active=" ������";
  }
  if ($_GET['menu'] == 2) {
    $find .= " and active_day<'".strftime("%Y-%m-%d",time())."'";
    $archive = " � ������";
  }
  $send->count = $VK->QRow("select count(id) from zayav ".$find);
  if ($send->count > 0)
    $send->result = utf8($where.$send->count.(isset($active)?$active.activeEnd($send->count):'')." ���������".zayavEnd($send->count).(isset($archive)?$archive:''));
  }

$send->page = 0;
$send->spisok = array();

$maxCount = 20;
$spisok=$VK->QueryObjectArray("select * from zayav ".$find." order by id desc limit ".(($_GET['page']-1)*$maxCount).",".$maxCount);
if (count($spisok) > 0) {
  $ids = '0';
  foreach ($spisok as $sp) { $ids .= ",".$sp->viewer_id_add; }
  if ($ids != '0') {
    $vkUsers = $VK->QueryObjectArray("select viewer_id,first_name,last_name from vk_user where viewer_id in (".$ids.")");
    foreach ($vkUsers as $us) {
      $vkName[$us->viewer_id] = utf8($us->first_name." ".$us->last_name);
    }
  }

  foreach($spisok as $sp) {
    if (!isset($vkName[$sp->viewer_id_add])) { $vkName[$sp->viewer_id_add] = ''; }
    $srok = strtotime($sp->active_day)-time()+86400;
    $active = 0;
    $day_last = 0;
    if($srok > 0) {
      $active = 1;
      $day = floor($srok / 86400);
      $day_last = utf8("�����".ostEnd($day).$day.dayEnd($day));
    }
    array_push($send->spisok, array(
      'id' => $sp->id,
      'rubrika' => $sp->rubrika,
      'podrubrika' => $sp->podrubrika,
      'txt' => utf8($sp->txt),
      'telefon' => utf8($sp->telefon),
      'adres' => utf8($sp->adres),
      'file' => $sp->file,
      'dop' => $sp->dop,
      'viewer_id' => $sp->viewer_id_add,
      'viewer_id_show' => $sp->viewer_id_show,
      'viewer_name' => $vkName[$sp->viewer_id_add],
      'dtime' => utf8(FullData($sp->dtime_add,1)),
      'active' => $active,
      'day_last' => $day_last,
      'country_name' => utf8($sp->country_name),
      'city_id' => $sp->city_id,
      'city_name' => utf8($sp->city_name)
    ));
  }
  if(count($spisok) == $maxCount) {
    if($VK->QNumRows("select id from zayav ".$find." limit ".($_GET['page']*$maxCount).",".$maxCount) > 0) {
      $send->page = $_GET['page'] + 1;
    }
  }
}

echo json_encode($send);
?>



