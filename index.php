<?php
require_once('config.php');
include('include/global_functions.php');
include('view/main.php');
$vku = (array)$VK->QueryObjectOne('SELECT * FROM `vk_user` WHERE `viewer_id`='.VIEWER_ID);
$vku = vkUserCheck($vku, false);// isset($_GET['start'])
_header($vku);
//GvaluesCreate();

if(!isset($_GET['p'])) $_GET['p'] = 'gazeta';

if ($_GET['p'] == 'gazeta') {
    include('view/gazeta/dazeta.php');
    switch (main_links(@$_GET['d'])) {
        case 'client': clientSpisok(); break;
        case 'zayav': zayavSpisok(); break;
        case 'report': reportView(); break;
        case 'setup': setupView($vku['gazeta_admin']); break;
    }
}


if ($_GET['p'] == 'ob') {
    include('view/ob/ob.php');
    switch (@$_GET['d']) {
        case 'create': obCreate(); break;
        case 'my':echo 'my'; break;
        case 'spisok':
        default: obSpisok();  break;
    }
}
/*
if(isset($_GET['hash'])) {
  $ex = explode('_',$_GET['hash']);
  $_GET['my_page'] = $ex[0];
  $_GET['id'] = isset($ex[1]) ? $ex[1] : '';
}
*/
_footer();
?>