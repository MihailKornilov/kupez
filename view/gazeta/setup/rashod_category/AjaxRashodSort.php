<?php
require_once('../../../../include/AjaxHeader.php');

$sort = explode(',',$_GET['val']);
for($n = 0; $n < count($sort); $n++)
	$VK->Query("update setup_rashod_category set sort=".$n." where id=".$sort[$n]);
GvaluesCreate();
echo 1;
?>



