<?php
if (!isset($WR[$vkUser['viewer_id']])) Header("Location:".URL); // ������� �� ������� ��������, ���� ������������ ������ �������� ���
include('incHeader.php');

/*
// ��������� ������� ���������� ����� ���� �����������
$spisok=$VK->QueryObjectArray("select * from vk_user order by viewer_id");
foreach($spisok as $sp)
  {
  $last=$VK->QRow("select dtime_add from visit where viewer_id=".$sp->viewer_id." order by id desc");
  if(!$last) $last=$sp->dtime_add;
  $VK->Query("update vk_user set enter_last='".$last."' where viewer_id=".$sp->viewer_id);
  }



// ��������� ���������� �����٨���� ���������� ���� �����������
$spisok=$VK->QueryObjectArray("select * from vk_user order by viewer_id");
foreach($spisok as $sp)
  {
  $ob_count=$VK->QRow("select count(id) from zayav where category=1 and whence='vk' and viewer_id_add=".$sp->viewer_id);
  $VK->Query("update vk_user set ob_count='".$ob_count."' where viewer_id=".$sp->viewer_id);
  }



// ��������� ���� - ��������� �� ������������ ����������
//$VK->Query("update vk_user set app_setup=0");
require_once('include/vkapi.class.php');
$VKAPI = new vkapi(2881875,'h9IjOkxIMwoW8agQkW3M');
$spisok=$VK->QueryObjectArray("select * from vk_user order by viewer_id desc limit 500,100");
foreach($spisok as $sp)
  {
  $app_setup=$VKAPI->api('isAppUser',array('uid'=>$sp->viewer_id));
  $VK->Query("update vk_user set app_setup=".$app_setup['response']." where viewer_id=".$sp->viewer_id);
  }



// ��������� ���� - ��������� �� ������������ ������ � ����� ����
//$VK->Query("update vk_user set app_setup=0");
require_once('include/vkapi.class.php');
$VKAPI = new vkapi(2881875,'h9IjOkxIMwoW8agQkW3M');
$spisok=$VK->QueryObjectArray("select * from vk_user order by viewer_id desc limit 500,100");
foreach($spisok as $sp)
  {
  $mls=$VKAPI->api('getUserSettings',array('uid'=>$sp->viewer_id));
  $menu_left_set=$mls['response']&256;
  $VK->Query("update vk_user set menu_left_set=".($menu_left_set>0?1:0)." where viewer_id=".$sp->viewer_id);
  }
*/

$dLink1='Sel'; include 'vk/visit/dopLinks.php';
?>
<DIV id=findResult>&nbsp;</DIV>

<TABLE cellpadding=0 cellspacing=0 id=vk-visit>
<TR>
  <TD id=spisok><DIV id=vk-ob></DIV>
  <TD id=cond><INPUT TYPE=hidden id=findRadio value=2>
</TABLE>

<SCRIPT type="text/javascript" src="/vk/visit/user/user.js?<?php echo $G->script_style; ?>"></SCRIPT>

<?php include('incFooter.php'); ?>



