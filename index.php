<?php
require_once('config.php');

_hashRead();
_header();

if(empty($_GET['p'])) {
	$_GET['p'] = 'gazeta';
}

if(!GAZETA_WORKER)
	$html .= _noauth();
else {
	switch($_GET['p']) {
		default:
		case 'gazeta':
			require_once(DOCUMENT_ROOT.'/view/gazeta.php');
			if(empty($_GET['d']))
				$_GET['d'] = 'client';
			_mainLinks();
			switch(@$_GET['d']) {
				default:
				case 'client':
					switch(@$_GET['d1']) {
						case 'info':
							if(!preg_match(REGEXP_NUMERIC, $_GET['id'])) {
								$html .= 'Страницы не существует';
								break;
							}
							$html .= client_info(intval($_GET['id']));
							break;
						default:
							$html .= client_list();
					}
				break;
				case 'zayav': $html .= zayav_list(); break;
				case 'report': $html .= report(); break;
				case 'setup': $html .= setup(); break;
			}
			break;
	}
}

_footer();
mysql_close();
echo $html;



/*

if (!isset($_GET['p'])) {
    $_GET['p'] = 'ob';
    if ($vku['gazeta_worker'] == 1)
        $_GET['p'] = 'gazeta';
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


if ($_GET['p'] == 'admin') {
    if (VIEWER_ID != SA) {
        echo 'No access. <a href="'.URL.'&p=ob">Back</a>';
    } else {
        include('view/admin/admin.php');
        switch (@$_GET['d']) {
            case 'ob': adminObSpisok(); break;
            case 'visit':
            default: adminVisit();  break;
        }
    }
}
*/