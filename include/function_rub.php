<?php
// 2013-01-18
// ������������ ���������� �����, ��� ��� ��� �������� ����� ������ ���������� � g_values.js
/*
 * ������� ������������ ��������
 * ������ � ��������� ��� php � javascript � ������� json.
 * ����� �������� ����������� � ����.
*/

function rubrika() {
  $rub = xcache_get('rubrika'); // ��������� ������� � ������� json �� ����
  if (!$rub) {
    $rub = vkSelGetJson("select id,name from setup_rubrika order by sort");
    xcache_set('rubrika', $rub, 864000); // ���������� ������� �� 10 ����
  }
return $rub;
}



function podrubrika() {
  global $VK;
  $cache = xcache_get('podrubrika');
  if (!$cache) {
    $spisok = $VK->QueryObjectArray("select id,rubrika_id,name from setup_pod_rubrika order by sort");
    foreach ($spisok as $sp) {
      if (!isset($podrub[$sp->rubrika_id])) { $podrub[$sp->rubrika_id] = array(); }
      array_push($podrub[$sp->rubrika_id], array(
        'uid' => $sp->id,
        'title' => utf8($sp->name)
      ));
    }
    $cache = json_encode($podrub);
    xcache_set('podRubrika', $cache, 864000);
  }
  return $cache;
}
?>
