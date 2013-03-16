<?php
if (!isset($WR[$vkUser['viewer_id']])) Header("Location:".$URL); // перевод на главную страницу, если пользователю нельзя смотреть эту
include('incHeader.php');

$dLink2='Sel'; include 'vk/visit/dopLinks.php';

if(!isset($_GET['id'])) { $_GET['id'] = 0; }
?>

<DIV id=findResult>&nbsp;</DIV>

<TABLE cellpadding=0 cellspacing=0 id=vk-myOb>
<TR>
	<TD id=spisok><DIV id=vk-ob></DIV>
	<TD id=cond>

</TABLE>

<SCRIPT type="text/javascript" src="/include/upload/upload.js?<?php echo $G->script_style; ?>"></SCRIPT>
<SCRIPT type="text/javascript" src="/vk/myOb/ob_edit.js?<?php echo $G->script_style; ?>"></SCRIPT>
<SCRIPT type="text/javascript" src="/vk/visit/ob/ob.js?<?php echo $G->script_style; ?>"></SCRIPT>

<?php include('incFooter.php'); ?>



