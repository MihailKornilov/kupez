<?php
/*
// установка количества объявлений каждой рубрике
$spisok = $VK->QueryObjectArray("select id from setup_rubrika order by sort");
foreach ($spisok as $sp) {
  $count = $VK->QRow("select count(id) from zayav where rubrika=".$sp->id." and status=1 and category=1 and vk_day_active>='".strftime("%Y-%m-%d",time())."'");
  $VK->Query("update setup_rubrika set ob_count=".$count." where id=".$sp->id);
}
xcache_unset('rubrikaCount');
*/


include('incHeader.php');

if (isset($WR[$vkUser['viewer_id']])) {
  $visit = "<BR><BR><BR><DIV class=findName>Посетители</DIV>";
  $today = $VK->QRow("select count(id) from visit where dtime_add>='".strftime("%Y-%m-%d",time())." 00:00:00'");
  $visit .= "<A href='".$URL."&my_page=vk-visit'>Сегодня: ".$today."</A>";
  if ($_GET['viewer_id'] == 982006) { $visit .= "<BR><BR><A id=cache_new>Очистить кэш</A>"; }
}

$rubrika = xcache_get('rubrika');
if (!$rubrika) {
  $rubrika = vkSelGetJson("select id,name from setup_rubrika order by sort");
  xcache_set('rubrika', $rubrika, 864000);
}

$rubrikaCount = xcache_get('rubrikaCount');
if (!$rubrikaCount) {
  // установка количества объявлений каждой рубрике
  $spisok = $VK->QueryObjectArray("select id from setup_rubrika order by sort");
  foreach ($spisok as $sp) {
    $count = $VK->QRow("select count(id) from zayav where rubrika=".$sp->id." and status=1 and category=1 and vk_day_active>='".strftime("%Y-%m-%d",time())."'");
    $VK->Query("update setup_rubrika set ob_count=".$count." where id=".$sp->id);
  }
  $spisok = $VK->QueryObjectArray("select id,ob_count from setup_rubrika order by sort");
  $count = array();
  foreach ($spisok as $sp) { $count[$sp->id] = $sp->ob_count; }
  $rubrikaCount = json_encode($count);
  xcache_set('rubrikaCount', $rubrikaCount, 86400);
}


$podRubrika = xcache_get('podRubrika');
if (!$podRubrika) {
  $spisok = $VK->QueryObjectArray("select id,rubrika_id,name from setup_pod_rubrika order by sort");
  foreach ($spisok as $sp) {
    if (!isset($podrub[$sp->rubrika_id])) { $podrub[$sp->rubrika_id] = array(); }
    array_push($podrub[$sp->rubrika_id], array(
      'uid' => $sp->id,
      'title' => utf8($sp->name)
    ));
  }
  $podRubrika = json_encode($podrub);
  xcache_set('podRubrika', $podRubrika, 864000);
}
?>




<DIV id=vk-ob>
  <TABLE cellpadding=0 cellspacing=0 id=tab1>
  <TR><TD><DIV id=vkFind></DIV>
      <TH><DIV class=vkButton><BUTTON onclick="location.href='<?php echo $URL."&my_page=vk-create"; ?>'";>Разместить объявление</BUTTON></DIV>
  </TABLE>
  <DIV id=findResult>&nbsp;</DIV>
  <TABLE cellpadding=0 cellspacing=0 width=100%>
  <TR>
    <TD id=spisok>&nbsp;
    <TD id=cond>
      <DIV id=rubrika></DIV>
      <DIV id=podrubrika></DIV>
      <INPUT TYPE=hidden id=type_gaz value=0><BR>
      <INPUT TYPE=hidden id=foto_only value=0>
      <?php echo isset($visit) ? $visit : ''; ?>
  </TABLE>
</DIV>

<SCRIPT type="text/javascript">
var spisok = {
  enter:"<?php echo isset($WR[$vkUser['viewer_id']]) ? "<A onclick=enter(); class=enter>Войти в программу</A>" : ''; ?>",
  rubrika:<?php echo $rubrika; ?>,
  rubCount:<?php echo $rubrikaCount; ?>,
  podRubrika:<?php echo $podRubrika; ?>,
  ob:<?php $x = xcache_get('obSpisokFirst'); echo $x ? $x : "null"; ?>,
  values:{
    rub:0,
    podrub:0
  },
  cache_new:0
}
</SCRIPT>
<SCRIPT type="text/javascript" src="/vk/spisok/obSpisok.js?<?php echo $G->script_style; ?>"></SCRIPT>

<?php include('incFooter.php'); ?>



