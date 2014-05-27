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
		$send['url'] = $res['url'];

		$res = _vkapi('account.getAppPermissions', array('user_id' => $viewer_id));
		$send['menu_left'] = $res;

//		$res = _vkapi('users.isAppUser', array('user_id' => $viewer_id));
//		$send['is_app_user'] = $res;

//		$res = _vkapi('photos.getUploadServer', array('album_id' => 130124967));
//		$send['photos.getUploadServer'] = $res;

		$res = _vkapi('photos.getUploadServer', array(
			'v' => 5.21,
			'album_id' => 195528889,
			'group_id' => 72078602
		));
		$send['photos.getUploadServer'] = $res;
		if(!empty($res['response'])) {
			$upload_url = $res['response']['upload_url'];
			$album_id = $res['response']['album_id'];
			$user_id = $res['response']['user_id'];

			$img = file_get_contents('http://cs5383.vk.me/u982006/135809479/w_e702e92f.jpg');
			$name = PATH.'files/'.time().'.jpg';
			$f = fopen($name, 'w');
			fwrite($f, $img);
			fclose($f);

			$post_data = array (
				'file1' => '@'.$name
			);

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $upload_url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
			$out = json_decode(curl_exec($curl), true);
			curl_close($curl);
			unlink($name);
			$send['image'] = $out;

			$server = $out['server'];
			$photos_list = $out['photos_list'];
			$hash = $out['hash'];

			$res = _vkapi('photos.save', array(
				'v' => 5.21,
				'album_id' => 195528889,
				'group_id' => 72078602,
				'server' => $server,
				'photos_list' => $photos_list,
				'hash' => $hash,
				'caption' => utf8('текст описания фотографии')
			));
			$send['photos.save'] = $res;
		}

/*		$res = _vkapi('photos.getAlbums', array(
			'owner_id' => $viewer_id,
			'need_covers' => 1
		));
		$send['photos.getAlbums'] = $res;
*/

		xcache_unset(CACHE_PREFIX.'viewer_166424274');
		xcache_unset(CACHE_PREFIX.'viewer_'.$viewer_id);

		jsonSuccess($send);
		break;

	case 'find_query_spisok':
		$data = admin_find_query_spisok($_POST);
		if($data['filter']['page'] == 1)
			$send['result'] = utf8($data['result']);
		$send['spisok'] = utf8($data['spisok']);
		jsonSuccess($send);
		break;
}
jsonError();
