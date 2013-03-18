<?php
require_once('../../../../include/AjaxHeader.php');

$VK->Query("insert into setup_skidka (razmer,about,viewer_id_add) values (".$_POST['razmer'].",'".win1251($_POST['about'])."',".VIEWER_ID.")");
GvaluesCreate();
echo 1;
?>



