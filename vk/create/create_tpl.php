<?php
include('incHeader.php');
?>

<DIV id=vk-create>
  <DIV class=headName>�������� ������ ����������</DIV>
  <DIV class=info>
    <P>����������, ��������� ��� ����������� ����. ����� ���������� ���������� ����� ���������� �������� ��� ������ ������������� ���������.
    <P>���������� ���������� ������ ��������� �� ����� ����� �������� ��� ��������� � ������ ����������, ���� ��� �������� <A>�������</A>.
    <P>���������� ����� ��������� ������ �� 1 �����, � ���������� �� ������� �������� ���� ����.
  </DIV>

  <TABLE cellpadding=0 cellspacing=10 class=tab>
  <TR><TD class=tdAbout>�������:                   <TD><INPUT TYPE=hidden id=rubrika value=0><TD><INPUT TYPE=hidden id=podrubrika value=0>
  <TR><TD class=tdAbout valign=top>�����:      <TD colspan=2><TEXTAREA id=txt></TEXTAREA>
  <TR><TD class='tdAbout top5' valign=top>��������� �����������:<TD colspan=2 id=ms_images><INPUT TYPE=hidden id=images>
  <TR><TD class=tdAbout>���������� ��������:  <TD colspan=2><INPUT TYPE=text id=telefon maxlength=200>
  <TR><TD class='tdAbout top5' valign=top>������:    <TD colspan=2 id=ms_adres><INPUT TYPE=hidden id=countries value=1><INPUT TYPE=hidden id=cities value=0>
  <TR><TD class=tdAbout>���������� ��� �� VK:  <TD colspan=2><INPUT TYPE=hidden id=viewer_id_show value=0>
  <TR><TD class=tdAbout>������� �������:  <TD colspan=2><INPUT TYPE=hidden id=pay_service value=0>
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
  <DIV id=vk-ob></DIV>
</DIV>

<SCRIPT type="text/javascript">
create = {
  back:"<?php echo isset($_GET['back']) ? $_GET['back'] : 'vk-ob'; ?>", // �� ����� �������� ������������ ��� ������
  order:{
    id:0,
    votes:0
  },
  top_week:1
};
</SCRIPT>
<SCRIPT type="text/javascript" src="/include/upload/upload.js?<?php echo $G->script_style; ?>"></SCRIPT>
<SCRIPT type="text/javascript" src="/vk/create/create.js?<?php echo $G->script_style; ?>"></SCRIPT>


<?php include('incFooter.php'); ?>



