<?php
require_once('config.php');
require_once(DOCUMENT_ROOT.'/view/gazeta.php');

switch(@$_POST['op']) {
	case 'client_spisok':
		$data = client_data(1, clientFilter($_POST));
		$send['result'] = utf8($data['result']);
		$send['spisok'] = utf8($data['spisok']);
		jsonSuccess($send);
		break;
	case 'client_next':
		if(!preg_match(REGEXP_NUMERIC, $_POST['page']))
			jsonError();
		$data = client_data(intval($_POST['page']), clientFilter($_POST));
		$send['spisok'] = utf8($data['spisok']);
		jsonSuccess($send);
		break;
}

jsonError();