<?php include('incHeader.php'); ?>

<SCRIPT type="text/javascript" src="/gazeta/setup/setup.js?<?php echo $G->script_style; ?>"></SCRIPT>
<SCRIPT type="text/javascript">
$(document).ready(function(){
	$("#razdelSel").vkSel({
		width:300,
		spisok:[
			<?php if($WR[$vkUser['viewer_id']] == 1) echo "{uid:8,title:'������ � ����� �����������'},"; ?>
			{uid:1,title:'���������'},
			{uid:2,title:'�������'},
			{uid:7,title:'����������'},
			{uid:9,title:'��������� ����� ����������'},
			{uid:6,title:'�������������� ��������� ����������'},
			{uid:4,title:'��������� ��&sup2; ��� ������ ������'},
			{uid:3,title:'������ ��������'},
			{uid:5,title:'������'}],
		func:function(ID){
			setupSet(ID);
			VK.callMethod('setLocation','setup_'+ID);
			}
		});
	setupSet($("#razdelSel").val());

	VK.callMethod('setLocation','setup_<?php echo $_GET['id']; ?>');
	});
</SCRIPT>
<?php $mLink7='Sel'; include 'gazeta/mainLinks.php'; ?>

<DIV id=setup>
	<DIV class=razdel><INPUT type=hidden id=razdelSel value=<?php echo ($_GET['id']?$_GET['id']:1); ?>></DIV>
	<DIV id=edit></DIV>
</DIV>


<?php include('incFooter.php'); ?>

