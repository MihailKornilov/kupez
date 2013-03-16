<?php include('incHeader.php'); ?>

<SCRIPT type="text/javascript" src="/gazeta/setup/setup.js?<?php echo $G->script_style; ?>"></SCRIPT>
<SCRIPT type="text/javascript">
$(document).ready(function(){
	$("#razdelSel").vkSel({
		width:300,
		spisok:[
			<?php if($WR[$vkUser['viewer_id']] == 1) echo "{uid:8,title:'Доступ и права сотрудников'},"; ?>
			{uid:1,title:'Заявители'},
			{uid:2,title:'Рубрики'},
			{uid:7,title:'Подрубрики'},
			{uid:9,title:'Стоимость длины объявления'},
			{uid:6,title:'Дополнительные параметры объявления'},
			{uid:4,title:'Стоимость см&sup2; для каждой полосы'},
			{uid:3,title:'Номера выпусков'},
			{uid:5,title:'Скидки'}],
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

