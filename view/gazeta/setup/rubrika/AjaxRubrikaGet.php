<?php
require_once('../../../../include/AjaxHeader.php');

$send = array();
$spisok = $VK->QueryObjectArray("select id,name from `setup_rubrika` order by sort");
if(count($spisok) > 0)
	foreach($spisok as $n=>$sp)
		array_push($send, array(
            'id' => $sp->id,
		    'name' => utf8($sp->name)
        ));
echo json_encode($send);
?>


