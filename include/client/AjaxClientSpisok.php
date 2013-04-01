<?php
require_once('../AjaxHeader.php');
require_once('../global_functions.php');

if(isset($_GET['input'])) {
    $input = win1251($_GET['input']);
    $find = " AND (`fio` LIKE '%".$input."%' OR
                   `org_name` LIKE '%".$input."%' OR
                   `telefon` LIKE '%".$input."%' OR
                   `adres` LIKE '%".$input."%')";
}

if (@$_GET['sel']) {
    $find = ' AND `id`='.$_GET['sel'];
}

$send->spisok = array();
$spisok = $VK->QueryObjectArray("SELECT * FROM `gazeta_client` WHERE id".$find." order by fio limit 50");
if (count($spisok) > 0) {
    foreach ($spisok as $sp) {
        $sp->fio = textUnFormat($sp->fio);
        $sp->org_name = textUnFormat($sp->org_name);
        $push = array('uid' => $sp->id, 'title' => utf8($sp->org_name ? $sp->org_name : $sp->fio));
        if (@$input) {
            $fio = preg_replace("/(".$input.")/i","<EM>\\1</EM>", $sp->fio);
            $org_name = preg_replace("/(".$input.")/i","<EM>\\1</EM>", $sp->org_name);
            $telefon = preg_replace("/(".$input.")/i","<EM>\\1</EM>", $sp->telefon);
            $adres = preg_replace("/(".$input.")/i","<EM>\\1</EM>", $sp->adres);
            $push['content'] = utf8(($org_name ? $org_name : $fio)."<DIV class=pole2><SPAN>".$telefon."</SPAN>".($telefon?'<BR>':'').$adres."</DIV>");
        }
        array_push($send->spisok, $push);
    }
}
echo json_encode($send);
?>



