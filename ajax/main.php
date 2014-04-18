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
		_cacheClear();
		jsonSuccess();
		break;

	case 'ob_spisok':
		$data = ob_spisok($_POST);
		$send['result'] = utf8($data['result']);
		$send['spisok'] = utf8($data['spisok']);
		jsonSuccess($send);
		break;
	case 'ob_create':
		if(!preg_match(REGEXP_NUMERIC, $_POST['rubric_id']) || !$_POST['rubric_id'])
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['rubric_sub_id']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['country_id']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['city_id']))
			jsonError();
		if(!preg_match(REGEXP_BOOL, $_POST['viewer_id_show']))
			jsonError();

		$rubric_id = intval($_POST['rubric_id']);
		$rubric_sub_id = intval($_POST['rubric_sub_id']);
		$txt = win1251(htmlspecialchars(trim($_POST['txt'])));
		$telefon = win1251(htmlspecialchars(trim($_POST['telefon'])));
		$country_id = intval($_POST['country_id']);
		$country_name = win1251(htmlspecialchars(trim($_POST['country_name'])));
		$city_id = intval($_POST['city_id']);
		$city_name = win1251(htmlspecialchars(trim($_POST['city_name'])));
		$viewer_id_show = intval($_POST['viewer_id_show']);

		$sql = "INSERT INTO `vk_ob` (
					`rubric_id`,
					`rubric_sub_id`,
					`txt`,
					`telefon`,
					`dop`,

					`country_id`,
					`country_name`,
					`city_id`,
					`city_name`,
					`viewer_id_show`,

					`day_active`,

					`viewer_id_add`
				) VALUES (
					".$rubric_id.",
					".$rubric_sub_id.",
					'".addslashes($txt)."',
					'".addslashes($telefon)."',
					'".$_POST['dop']."',

					".$country_id.",
					'".addslashes($country_name)."',
					".$city_id.",
					'".addslashes($city_name)."',
					".$viewer_id_show.",

					DATE_ADD(CURRENT_TIMESTAMP,INTERVAL 30 DAY),

					".VIEWER_ID."
				)";
		query($sql);

		$insert_id = mysql_insert_id();

		//сохранение изображений
		$sql = "SELECT * FROM `images` WHERE !`deleted` AND `owner`='".VIEWER_ID."'";
		$q = query($sql);
		if(mysql_num_rows($q))
			query("UPDATE `images` SET `owner`='ob".$insert_id."' WHERE !`deleted` AND `owner`='".VIEWER_ID."'");
		//while($r = mysql_fetch_assoc($q)) {}

		jsonSuccess();
		break;
}

jsonError();