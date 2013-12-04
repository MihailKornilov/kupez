<?php
require_once('config.php');
require_once(VKPATH.'/vk_ajax.php');


switch(@$_POST['op']) {
	case 'cache_clear':
		if(!SA)
			jsonError();
		$sql = "SELECT `viewer_id` FROM `vk_user` WHERE `gazeta_worker`=1";
		$q = query($sql);
		while($r = mysql_fetch_assoc($q))
			xcache_unset(CACHE_PREFIX.'viewer_'.$r['viewer_id']);
		query("UPDATE `setup_global` SET `version`=`version`+1");

		//todo удалить
		require_once(DOCUMENT_ROOT.'/view/gazeta.php');
		GvaluesCreate();

		_cacheClear();
		jsonSuccess();
		break;
}

jsonError();