<?php
require_once('../../../../include/AjaxHeader.php');

$send = $VK->QueryObjectOne("SELECT `txt_len_first`,`txt_len_next`,`txt_cena_first`,`txt_cena_next` from setup_global limit 1");
echo json_encode($send);
?>



