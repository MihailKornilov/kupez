<?php
require_once('../../../../include/AjaxHeader.php');

$send = array();
$spisok = $VK->QueryObjectArray("SELECT * FROM `vk_user` WHERE `gazeta_worker`=1 AND `viewer_id`!= 982006 ORDER BY `dtime_add`");
if (count($spisok) > 0)
	foreach($spisok as $sp) {
        array_push($send, array(
            'viewer_id' => $sp->viewer_id,
            'full_name' => utf8($sp->first_name.' '.$sp->last_name),
            'photo' => $sp->photo,
            'dtime_add' => utf8("��������".($sp->sex == 1 ? 'a' : '')." ".FullData($sp->dtime_add)),
            'admin' => $sp->gazeta_admin
        ));
    }
echo json_encode($send);
?>



