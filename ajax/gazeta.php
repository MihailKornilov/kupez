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


	case 'setup_worker_add':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		$viewer_id = intval($_POST['id']);
		$sql = "SELECT `gazeta_worker` FROM `vk_user` WHERE `viewer_id`=".$viewer_id." LIMIT 1";
		if(query_value($sql))
			jsonError('Этот пользователь уже является</br >сотрудником.');
		_viewer($viewer_id);
		query("UPDATE `vk_user` SET `gazeta_worker`=1 WHERE `viewer_id`=".$viewer_id);
		xcache_unset(CACHE_PREFIX.'viewer_'.$viewer_id);
		/*
				history_insert(array(
					'type' => 1081,
					'value' => $viewer_id
				));
		*/
		$send['html'] = utf8(setup_worker_spisok());
		jsonSuccess($send);
		break;
	case 'setup_worker_del':
		if(!preg_match(REGEXP_NUMERIC, $_POST['viewer_id']))
			jsonError();
		$viewer_id = intval($_POST['viewer_id']);
		$sql = "SELECT * FROM `vk_user` WHERE `viewer_id`=".$viewer_id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();
		if($r['gazeta_admin'])
			jsonError();
		if(!$r['gazeta_worker'])
			jsonError();
		query("UPDATE `vk_user` SET `gazeta_worker`=0 WHERE `viewer_id`=".$viewer_id);
		xcache_unset(CACHE_PREFIX.'viewer_'.$viewer_id);
		/*
				history_insert(array(
					'type' => 1082,
					'value' => $viewer_id
				));
		*/
		$send['html'] = utf8(setup_worker_spisok());
		jsonSuccess($send);
		break;

}

jsonError();