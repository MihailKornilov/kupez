<?php
/*
// установка количества объявлений каждой рубрике
$spisok = $VK->QueryObjectArray("select id from setup_rubrika order by sort");
foreach ($spisok as $sp) {
  $count = $VK->QRow("select count(id) from zayav where rubrika=".$sp->id." and status=1 and category=1 and active_day>='".strftime("%Y-%m-%d",time())."'");
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

$rubrikaCount = xcache_get('rubrikaCount');
if (!$rubrikaCount) {
  // установка количества объявлений каждой рубрике
  $spisok = $VK->QueryObjectArray("select id from setup_rubrika order by sort");
  foreach ($spisok as $sp) {
    $count = $VK->QRow("select count(id) from zayav where rubrika=".$sp->id." and status=1 and category=1 and active_day>='".strftime("%Y-%m-%d",time())."'");
    $VK->Query("update setup_rubrika set ob_count=".$count." where id=".$sp->id);
  }
  $spisok = $VK->QueryObjectArray("select id,ob_count from setup_rubrika order by sort");
  $count = array();
  foreach ($spisok as $sp) { $count[$sp->id] = $sp->ob_count; }
  $rubrikaCount = json_encode($count);
  xcache_set('rubrikaCount', $rubrikaCount, 86400);
}

//if ($_GET['viewer_id'] == 982006) { echo "<A href='".$URL."&my_page=vk-create1'>Разместить объявление</A> app_setup=".$vkUser['app_setup'].", menu_left_set=".$vkUser['menu_left_set']; }
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
      <DIV class=findName id=ms_region>Регион</DIV><INPUT TYPE=hidden id=countries value=1><INPUT TYPE=hidden id=cities value=0><BR>

      <DIV class=findName>Рубрики</DIV><DIV id=rubrika></DIV>
      <DIV id=podrubrika></DIV>

      <DIV class=findName>Дополнительно</DIV>
      <!-- <INPUT TYPE=hidden id=type_gaz value=0><BR> -->
      <INPUT TYPE=hidden id=foto_only value=0>
      <?php echo isset($visit) ? $visit : ''; ?>
  </TABLE>
</DIV>


<SCRIPT type="text/javascript">
<?php
  if($vkUser['app_setup'] == 0) {
    if(!$VK->QRow("select id from hint_no_show where hint_id=1 and viewer_id=".$_GET['viewer_id'])) {
      echo "
      $('#vk-ob').alertShow({
        otstup:160,
        delayShow:10000,
        delayHide:15000,
        ugol:'top',
        left:400,
        txt:hintTxt('".$VK->QRow("select txt from hint where id=1")."',1)
      });
      ";
    }
  } else {
    if($vkUser['menu_left_set'] == 0) {
      if(!$VK->QRow("select id from hint_no_show where hint_id=2 and viewer_id=".$_GET['viewer_id'])) {
        echo "
        $('#vk-ob').alertShow({
          otstup:160,
          delayShow:10000,
          delayHide:15000,
          ugol:'top',
          left:405,
          txt:hintTxt('".$VK->QRow("select txt from hint where id=2")."',2)
        });
        ";
      }
    }
  }
?>
var spisok = {
  enter:"<?php echo isset($WR[$vkUser['viewer_id']]) ? "<A href='".$URL."' onclick=setCookie('enter',1); class=enter>Войти в программу</A>" : ''; ?>",
  rubCount:<?php echo $rubrikaCount; ?>,
  ob:<?php $x = xcache_get('obSpisokFirst'); echo $x ? $x : "''"; ?>,
  cities:<?php echo vkSelGetJson("select distinct(city_id),city_name from zayav where city_id>0 and city_name!='' and status=1 and category=1 and active_day>='".strftime("%Y-%m-%d",time())."' order by city_name"); ?>
};
</SCRIPT>
<SCRIPT type="text/javascript" src="/vk/spisok/obSpisok.js?<?php echo $G->script_style; ?>"></SCRIPT>

<?php include('incFooter.php'); ?>



