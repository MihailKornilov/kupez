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
	<DIV class=headName>������� � ��������� � ���������</DIV>
	<DIV class=help>���������� ����� ���� ��������� � ����������, ������� �������� ���������. ��� �������� ����� ���������.</DIV>
	<DIV id=comm></DIV>
</DIV>


<?php include('incFooter.php'); ?>

