<?php
// ������ ����������
function obSpisok()
{
    global $VK, $vku;
    if (SA == VIEWER_ID) {
        $today = $VK->QRow("SELECT COUNT(`id`) FROM `visit` WHERE `dtime_add`>='".strftime("%Y-%m-%d",time())." 00:00:00'");
        $visit = "<BR><BR><BR><DIV class=findName>����������</DIV>"
                ."<A href='".URL."&p=ob&o=visit'>�������: ".$today."</A>"
                ."<BR><BR><A id=cache_new>�������� ���</A>";
    }
    // ������� ���������� ���������� ������ �������
    $rubrikaCount = $VK->ptpJson('SELECT
                                    `rubrika` AS `id`,
                                    COUNT(`id`) AS `ob_count`
                                  FROM `zayav` WHERE
                                    `status`=1 AND
                                    `category`=1 AND
                                    `active_day`>=DATE_FORMAT(NOW(), "%Y-%m-%d") GROUP BY `rubrika`');
    // ������ �������, ��� ������� ���� ����������
    $cities = $VK->vkSelJson('SELECT DISTINCT(`city_id`),`city_name` FROM `zayav`
                              WHERE
                                `city_id`>0 AND
                                `city_name`!="" AND
                                `status`=1 AND
                                `category`=1 AND
                                `active_day`>=DATE_FORMAT(NOW(), "%Y-%m-%d") ORDER BY `city_name`');
?>
<DIV id=obSpisok>
  <TABLE cellpadding=0 cellspacing=0 id=tab1>
  <TR><TD><DIV id=vkFind></DIV>
      <TH><DIV class=vkButton><BUTTON onclick="location.href='<?=URL.'&p=ob&d=create'?>'";>���������� ����������</BUTTON></DIV>
  </TABLE>
  <DIV id=findResult>&nbsp;</DIV>
  <TABLE cellpadding=0 cellspacing=0 width=100%>
    <TR>
    <TD id=spisok>&nbsp;
    <TD id=cond>
        <DIV class=findName>������</DIV><INPUT TYPE=hidden id=countries value=1><INPUT TYPE=hidden id=cities value=0><BR>

        <DIV class=findName>�������</DIV><DIV id=rubrika></DIV>
        <DIV id=podrubrika></DIV>

        <DIV class=findName>�������������</DIV>
        <INPUT TYPE=hidden id=foto_only value=0>
        <?=@$visit?>
  </TABLE>
</DIV>
    <?php
/*
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
    }*/
    ?>
<SCRIPT type="text/javascript">
var spisok = {
    enter:"<?=$vku['gazeta_worker'] == 1 ? "<A href='".URL."' onclick=setCookie('enter',1); class=enter>����� � ���������</A>" : ''?>",
    rubCount:<?=$rubrikaCount?>,
    cities:<?=$cities?>
};
</SCRIPT>
<SCRIPT type="text/javascript" src="/view/ob/spisok/obSpisok.js?<?=JS_VERSION?>"></SCRIPT>
<?php
} // end of obSpisok()

// ���������� ����������
function obCreate() {
?>
<DIV id=vk-create>
  <DIV class=headName>�������� ������ ����������</DIV>
  <DIV class=info>
    <P>����������, ��������� ��� ����������� ����. ����� ���������� ���������� ����� ���������� �������� ��� ������ ������������� ���������.
    <P>���������� ���������� ������ ��������� �� ����� ����� �������� ��� ��������� � ������ ����������, ���� ��� �������� <A>�������</A>.
    <P>���������� ����� ��������� ������ �� 1 �����, � ���������� �� ������� �������� ���� ����.
  </DIV>

  <TABLE cellpadding=0 cellspacing=10 class=tab>
  <TR><TD class=tdAbout>�������:               <TD><INPUT TYPE=hidden id=rubrika value=0><TD><INPUT TYPE=hidden id=podrubrika value=0>
  <TR><TD class=tdAbout valign=top>�����:      <TD colspan=2><TEXTAREA id=txt></TEXTAREA>
  <TR><TD class='tdAbout top5' valign=top>��������� �����������:<TD colspan=2 id=ms_images><INPUT TYPE=hidden id=images>
  <TR><TD class=tdAbout>���������� ��������:   <TD colspan=2><INPUT TYPE=text id=telefon maxlength=200>
  <TR><TD class='tdAbout top5' valign=top>������:    <TD colspan=2 id=ms_adres><INPUT TYPE=hidden id=countries value=1><INPUT TYPE=hidden id=cities value=0>
  <TR><TD class=tdAbout>���������� ��� �� VK:  <TD colspan=2><INPUT TYPE=hidden id=viewer_id_show value=0>
  <TR><TD class=tdAbout>������� �������:       <TD colspan=2><INPUT TYPE=hidden id=pay_service value=0>
  </TABLE>

  <TABLE cellpadding=0 cellspacing=8  id=payContent>
    <TR><TD class=tdAbout><TD><INPUT TYPE=hidden id=dop value=0>
    <TR><TD class=tdAbout>������� ����������:<TD valign=bottom><INPUT TYPE=hidden id=top><SPAN id=top_week>�� <EM class=bok>&nbsp;</EM><EM class=a>-</EM><EM class=inp>1</EM><EM class=a>+</EM><EM class=bok>&nbsp;</EM> �����<EM class=end></EM></SPAN>
   </TABLE>

  <INPUT TYPE=hidden id=file>

  <DIV id=zMsg></DIV>
  <DIV id=butts>
    <DIV class=vkButton><BUTTON>���������� ����������<SPAN></SPAN></BUTTON></DIV>
    <DIV class=vkCancel><BUTTON>������</BUTTON></DIV>
  </DIV>

<DIV id=callbacks></DIV>

  <DIV class=headName>���������� ����������</DIV>
  <DIV id=obSpisok></DIV>
</DIV>

<SCRIPT type="text/javascript">
        create = {
        back:"<?php echo isset($_GET['back']) ? $_GET['back'] : 'spisok'; ?>", // �� ����� �������� ������������ ��� ������
        order:{
            id:0,
            votes:0
        },
        top_week:1
};
</SCRIPT>
<SCRIPT type="text/javascript" src="/include/upload/upload.js?<?=JS_VERSION?>"></SCRIPT>
<SCRIPT type="text/javascript" src="/view/ob/create/create.js?<?=JS_VERSION?>"></SCRIPT>
<?php
} // end of obCreate()
?>