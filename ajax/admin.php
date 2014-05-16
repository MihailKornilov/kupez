<?php
require_once('config.php');

if(!SA)
	jsonError();

require_once(DOCUMENT_ROOT.'/view/admin.php');

switch(@$_POST['op']) {
	case 'user_spisok':
		$data = admin_user_spisok($_POST);
		if($data['filter']['page'] == 1)
			$send['result'] = utf8($data['result']);
		$send['spisok'] = utf8($data['spisok']);
		jsonSuccess($send);
		break;
}
jsonError();