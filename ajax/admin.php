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

		if(!$u = query_assoc("SELECT * FROM `vk_user` WHERE `viewer_id`=".$viewer_id))
			jsonError();

		$res = _vkapi('users.get', array(
			'user_ids' => $viewer_id,
			'fields' => 'photo,'.
						'sex,'.
						'country,'.
						'city'
		));
		$send['user'] = $res['response'][0];

		$res = _vkapi('account.getAppPermissions', array('user_id' => $viewer_id));
		$send['menu_left'] = $res;

		$res = _vkapi('users.isAppUser', array('user_id' => $viewer_id));
		$send['is_app_user'] = $res;

		xcache_unset(CACHE_PREFIX.'viewer_166424274');
		xcache_unset(CACHE_PREFIX.'viewer_'.$viewer_id);

		jsonSuccess($send);
		break;
}
jsonError();