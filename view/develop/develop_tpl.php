<?php include('incHeader.php'); ?>

<SCRIPT type="text/javascript">
$(document).ready(function(){
	$("#comm").vkComment({
		width:607,
		table_name:'develop',
		table_id:1
		});

	VK.callMethod('setLocation','develop');
	frameBodyHeightSet();
	});
</SCRIPT>


<?php include 'gazeta/mainLinks.php'; ?>

<DIV class=develop>
	<DIV class=headName>Вопросы и замечания к программе</DIV>
	<DIV class=help>Оставляйте здесь свои замечания и дополнения, которые касаются программы. Все недочёты будем устранять.</DIV>
	<DIV id=comm></DIV>
</DIV>


<?php include('incFooter.php'); ?>

