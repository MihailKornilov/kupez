<?php
require_once('../../../../include/AjaxHeader.php');

$send = array();
$spisok=$VK->QueryObjectArray("select * from setup_polosa_cost order by sort");
if(count($spisok) > 0)
    foreach($spisok as $n=>$sp)
        array_push($send, array(
            'id' => $sp->id,
            'name' => utf8($sp->name),
            'cena' => round($sp->cena, 2)
        ));
echo json_encode($send);
?>



