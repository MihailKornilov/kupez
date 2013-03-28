<?php
require_once('../../../../include/AjaxHeader.php');

$VK->Query("INSERT INTO `vk_user` (
`viewer_id`,
`first_name`,
`last_name`,
`sex`,
`photo`,
`gazeta_worker`
) values (
".$_POST['uid'].",
'".win1251($_POST['first_name'])."',
'".win1251($_POST['last_name'])."',
".$_POST['sex'].",
'".$_POST['photo']."',
1) ON DUPLICATE KEY UPDATE `gazeta_worker`=1");

$VK->Query('INSERT INTO `gazeta_log`
                (`type`,`value`,`viewer_id_add`)
            VALUES
                (1081,"'.win1251($_POST['first_name']).' '.win1251($_POST['last_name']).'",'.VIEWER_ID.')');

$send = 1;
echo json_encode($send);
?>



