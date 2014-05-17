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
	case 'user_update':
		if(!$viewer_id = _isnum($_POST['viewer_id']))
			jsonError();

		require_once(VKPATH.'vkapi.class.php');
		$VKAPI = new vkapi(API_ID, SECRET);
		$res = $VKAPI->api('users.get', array(
			'user_ids' => $viewer_id,
			'fields' => 'photo,'.
						'sex,'.
						'country,'.
						'city'
		));
		$send['u'] = $res['response'][0];

		$app = $VKAPI->api('account.getAppPermissions', array('user_id'=>$viewer_id));
		$send['u']['app_setup'] = $app;
		jsonSuccess($send);
		break;
}
jsonError();