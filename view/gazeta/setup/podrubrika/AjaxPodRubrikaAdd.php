<?php
require_once('../../../../include/AjaxHeader.php');

$sort=$VK->QRow("select ifnull(max(sort)+1,0) from setup_pod_rubrika where rubrika_id=".$_POST['rubrika_id']);
$send->id=$VK->Query("insert into setup_pod_rubrika (rubrika_id,name,viewer_id_add,sort) values (".$_POST['rubrika_id'].",'".win1251($_POST['name'])."',".VIEWER_ID.",".$sort.")");
GvaluesCreate();
echo json_encode($send);
?>



