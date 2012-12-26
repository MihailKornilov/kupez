<?php
require_once('../../../include/AjaxHeader.php');

$sort=explode(',',$_GET['val']);
for($n=0;$n<count($sort);$n++)
	$VK->Query("update setup_polosa_cost set sort=".$n." where id=".$sort[$n]);

echo 1;
?>



