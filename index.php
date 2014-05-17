<?php
require_once('config.php');

_hashRead();
_header();

switch($_GET['p']) {
	default:
	case 'ob':
		switch(@$_GET['d']) {
			default:
			case 'ob': $html .= ob(); break;
			case 'create': $html .= ob_create(); break;
			case 'my': $html .= ob_my(); break;
		}
		break;
	case 'admin':
		if(!SA) {
			$html .= _noauth();
			break;
		}
		require_once(DOCUMENT_ROOT.'/view/admin.php');
		if(empty($_GET['d']))
			$_GET['d'] = 'user';
		$html .= adminMainLinks();
		switch($_GET['d']) {
			default:
			case 'user': $html .= _isnum(@$_GET['id']) ? admin_user_info($_GET['id']) : admin_user(); break;
			case 'exit': header('Location:'.URL.'&p=ob'); break;
		}
		break;
	case 'gazeta':
		if(!GAZETA_WORKER) {
			$html .= _noauth();
			break;
		}
		require_once(DOCUMENT_ROOT.'/view/gazeta.php');
		if(empty($_GET['d']))
			$_GET['d'] = 'zayav';
		_mainLinks();
		switch($_GET['d']) {
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
			case 'zayav':
				switch(@$_GET['d1']) {
					case 'add': $html .= zayav_add(); break;
					case 'info': $html .= zayav_info(intval(@$_GET['id'])); break;
					case 'edit': $html .= zayav_edit(intval(@$_GET['id'])); break;
					default:
						$html .= zayav_list();
				}
				break;
			case 'report': $html .= report(); break;
			case 'setup': $html .= setup(); break;
			case 'ob': header('Location:'.URL.'&p=ob'); break;
		}
		break;
}

_footer();
mysql_close();
echo $html;
exit;