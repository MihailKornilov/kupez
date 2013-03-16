<?php
if (SA != [$_GET['viewer_id']) Header("Location:".$URL); // переводим на главную страницу, если пользователю нельз€ смотреть эту
include('incHeader.php');
?>

<SCRIPT LANGUAGE="JavaScript">
$(document).ready(function(){

	VK.callMethod('setLocation','admin-hint');
	});


function hintAdd() {
  dialogShow({
    head:'Ќова€ подсказка',
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
  <DIV class=headName>ѕодсказки</DIV>
  <DIV class=vkButton><BUTTON onclick=hintAdd();>ƒобавить подсказку</BUTTON></DIV>
</DIV>

<?php include('incFooter.php'); ?>



