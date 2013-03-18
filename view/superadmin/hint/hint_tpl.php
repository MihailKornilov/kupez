<?php
if (SA != [VIEWER_ID) Header("Location:".URL); // ��������� �� ������� ��������, ���� ������������ ������ �������� ���
include('incHeader.php');
?>

<SCRIPT LANGUAGE="JavaScript">
$(document).ready(function(){

	VK.callMethod('setLocation','admin-hint');
	});


function hintAdd() {
  dialogShow({
    head:'����� ���������',
    content:"<TEXTAREA id=hint-txt style=width:316px;></TEXTAREA>",
    submit:function () {
      $.post('/superadmin/hint/AjaxHintAdd.php?' + $("#VALUES").val(),{txt:$('#hint-txt').val()},function () {
        dialogHide();
      });
    },
    focus:'#hint-txt'
  });
  $('#hint-txt').textareaResize({minH:50});
}
</SCRIPT>

<DIV id=adminHint>
  <DIV class=headName>���������</DIV>
  <DIV class=vkButton><BUTTON onclick=hintAdd();>�������� ���������</BUTTON></DIV>
</DIV>

<?php include('incFooter.php'); ?>



