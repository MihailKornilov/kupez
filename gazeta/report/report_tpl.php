<?php
include('incHeader.php');

$mLink3='Sel'; include 'gazeta/mainLinks.php'; ?>

<TABLE cellpadding=0 cellspacing=0 id=report>
<TR>
  <TD id=cont>
  <TD id=right>
    <DIV id=links></DIV>
    <DIV id=years>
      <TABLE cellpadding=0 cellspacing=0>
      <TR><TD class=but>&laquo;<TD id=ycenter><SPAN>2012</SPAN><TD class=but>&raquo;
      </TABLE>
    </DIV>
    <INPUT type=hidden id=months>

</TABLE>


<SCRIPT type="text/javascript">
var report = {
  page:'zayav',
  go:[],          // �������, ����������� �������� ���������� �������
  thisYear:2012, // ������� ���
  mon:0,       // ������� �����
  img:function () { $(".img").html('<IMG src=/img/upload.gif>'); },
  allmon:1 // ��������� ������ �� ���������� ������ � ������ ������ (��� ������� �������)
};
</SCRIPT>
<SCRIPT type="text/javascript" src="/gazeta/report/rashod/rashod.js?<?php echo $G->script_style; ?>"></SCRIPT>
<SCRIPT type="text/javascript" src="/gazeta/report/zayav/zayav.js?<?php echo $G->script_style; ?>"></SCRIPT>
<SCRIPT type="text/javascript" src="/gazeta/report/report.js?<?php echo $G->script_style; ?>"></SCRIPT>


<?php include('incFooter.php'); ?>



