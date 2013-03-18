<?php
require_once('../../../../include/AjaxHeader.php');

$send = array();
$spisok=$VK->QueryObjectArray("select * from setup_skidka order by razmer");
if(count($spisok) > 0)
    foreach($spisok as $n=>$sp)
        array_push($send, array(
            'id' => $sp->id,
            'razmer' => utf8($sp->razmer),
            'about' => utf8($sp->about)
        ));
echo json_encode($send);
?>



