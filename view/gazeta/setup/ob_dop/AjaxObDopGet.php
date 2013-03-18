<?php
require_once('../../../../include/AjaxHeader.php');

$send = array();
$spisok=$VK->QueryObjectArray("select * from setup_ob_dop order by id");
if(count($spisok)>0)
    foreach($spisok as $sp)
        array_push($send, array(
            'id' => $sp->id,
            'name' => utf8($sp->name),
            'cena' => $sp->cena
        ));

echo json_encode($send);
?>



