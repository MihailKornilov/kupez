<?php
require_once('config.php');
include('include/global_functions.php');
include('view/main.php');
$vku = (array)$VK->QueryObjectOne('SELECT * FROM `vk_user` WHERE `viewer_id`='.VIEWER_ID);
$vku = vkUserCheck($vku, isset($_GET['start']));
_header($vku);
//GvaluesCreate();

if(!isset($_GET['p']))
    $_GET['p'] = 'ob';

if ($_GET['p'] == 'gazeta') {
    if ($vku['gazeta_worker'] == 0) {
        echo 'No access. <a href="'.URL.'&p=ob">Back</a>';
    } else {
        include('view/gazeta/dazeta.php');
        switch (main_links(@$_GET['d'])) {
            case 'client': clientSpisok(); break;
            case 'zayav': zayavSpisok(); break;
            case 'report': reportView(); break;
            case 'setup': setupView($vku['gazeta_admin']); break;
        }
    }
}


if ($_GET['p'] == 'ob') {
    include('view/ob/ob.php');
    switch (@$_GET['d']) {
        case 'create': obCreate(); break;
        case 'my': obMySpisok(); break;
        case 'spisok':
        default: obSpisok();  break;
    }
}

_footer();
?>