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
		xcache_unset(CACHE_PREFIX.'viewer_166424274');
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
	case 'ob_archive':
		//отправка объявления в архив
		if(!SA)
			jsonError();
		if(!$id = _isnum($_POST['id']))
			jsonError();

		$sql = "SELECT * FROM `vk_ob` WHERE !`deleted` AND `day_active`!='0000-00-00' AND `id`=".$id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		query("UPDATE `vk_ob` SET `day_active`='0000-00-00' WHERE `id`=".$id);

		jsonSuccess();
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
		$sql = "SELECT * FROM `images` WHERE !`deleted` AND `owner`='".VIEWER_ID."' ORDER BY `sort`";
		$q = query($sql);
		if(mysql_num_rows($q)) {
			query("UPDATE `images` SET `owner`='ob".$insert_id."' WHERE !`deleted` AND `owner`='".VIEWER_ID."'");
			$image_id = 0;
			$image_link = '';
			$n = 0;
			while($r = mysql_fetch_assoc($q)) {
				$small_name = str_replace(VIEWER_ID.'-', 'ob'.$insert_id.'-', $r['small_name']);
				$big_name = str_replace(VIEWER_ID.'-', 'ob'.$insert_id.'-', $r['big_name']);
				rename(PATH.'files/images/'.$r['small_name'], PATH.'files/images/'.$small_name);
				rename(PATH.'files/images/'.$r['big_name'], PATH.'files/images/'.$big_name);
				query("UPDATE `images` SET `small_name`='".$small_name."',`big_name`='".$big_name."' WHERE `id`=".$r['id']);
				if(!$n) {
					$image_id = $r['id'];
					$image_link = $r['path'].$small_name;
				}
				$n++;
			}
			query("UPDATE `vk_ob` SET `image_id`=".$image_id.",`image_link`='".$image_link."' WHERE `id`=".$insert_id);
		}

		jsonSuccess();
		break;
	case 'ob_load':
		if(!$id = _isnum($_POST['id']))
			jsonError();
		$sql = "SELECT * FROM `vk_ob` WHERE !`deleted` ".(SA ? '' : "AND `viewer_id_add`=".VIEWER_ID)." AND `id`=".$id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();
		$send = array(
			'rubric_id' => intval($r['rubric_id']),
			'rubric_sub_id' => intval($r['rubric_sub_id']),
			'txt' => utf8($r['txt']),
			'telefon' => utf8($r['telefon']),
			'images' => utf8(_imageAdd(array('owner'=>'ob'.$r['id']))),
			'country_id' => intval($r['country_id']),
			'city_id' => intval($r['city_id']),
			'city_name' => utf8($r['city_name']),
			'viewer_id_show' => intval($r['viewer_id_show']),
			'viewer_id_add' => intval($r['viewer_id_add']),
			'active' => strtotime($r['day_active']) - time() + 86400 < 0 ? 0 : 1
		);
		jsonSuccess($send);
		break;
	case 'ob_edit':
		if(!$id = _isnum($_POST['id']))
			jsonError();
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
		if(!preg_match(REGEXP_BOOL, $_POST['active']))
			jsonError();

		$sql = "SELECT * FROM `vk_ob` WHERE !`deleted` ".(SA ? '' : "AND `viewer_id_add`=".VIEWER_ID)." AND `id`=".$id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		$my = _isbool($_POST['my']);
		$r['rubric_id'] = intval($_POST['rubric_id']);
		$r['rubric_sub_id'] = intval($_POST['rubric_sub_id']);
		$r['txt'] = win1251(htmlspecialchars(trim($_POST['txt'])));
		$r['telefon'] = win1251(htmlspecialchars(trim($_POST['telefon'])));
		$r['country_id'] = intval($_POST['country_id']);
		$r['country_name'] = win1251(htmlspecialchars(trim($_POST['country_name'])));
		$r['city_id'] = intval($_POST['city_id']);
		$r['city_name'] = win1251(htmlspecialchars(trim($_POST['city_name'])));
		$r['viewer_id_show'] = intval($_POST['viewer_id_show']);
		$active = intval($_POST['active']);

		if($active && $r['day_active'] == '0000-00-00')
			$r['day_active'] = strftime('%Y-%m-%d', time() + 86400 * 30);

		if(!$active && $r['day_active'] != '0000-00-00')
			$r['day_active'] = '0000-00-00';

		$r['image_id'] = 0;
		$r['image_link'] = '';
		$sql = "SELECT * FROM `images` WHERE !`deleted` AND `owner`='ob".$r['id']."' ORDER BY `sort` LIMIT 1";
		if($i = mysql_fetch_assoc(query($sql))) {
			$r['image_id'] = $i['id'];
			$r['image_link'] = $i['path'].$i['small_name'];
		}

		$sql = "UPDATE `vk_ob`
		        SET `rubric_id`=".$r['rubric_id'].",
					`rubric_sub_id`=".$r['rubric_sub_id'].",
					`txt`='".addslashes($r['txt'])."',
					`telefon`='".addslashes($r['telefon'])."',
					`country_id`=".$r['country_id'].",
					`country_name`='".addslashes($r['country_name'])."',
					`city_id`=".$r['city_id'].",
					`city_name`='".addslashes($r['city_name'])."',
					`viewer_id_show`=".$r['viewer_id_show'].",
					`day_active`='".$r['day_active']."',
					`image_id`=".$r['image_id'].",
					`image_link`='".$r['image_link']."'
				WHERE `id`=".$id;
		query($sql);

		$r['edited'] = 1;
		$send['html'] = utf8($my ? ob_my_unit($r) : ob_unit($r));
		jsonSuccess($send);
		break;
	case 'ob_del':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']) || !$_POST['id'])
			jsonError();
		$id = intval($_POST['id']);
		$sql = "SELECT * FROM `vk_ob` WHERE !`deleted` AND `viewer_id_add`=".VIEWER_ID." AND `id`=".$id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();
		query("UPDATE `vk_ob` SET `deleted`=1 WHERE`id`=".$id);
		jsonSuccess();
		break;
	case 'ob_my_spisok':
		$data = ob_my_spisok($_POST);
		$send['result'] = utf8($data['result']);
		$send['spisok'] = utf8($data['spisok']);
		jsonSuccess($send);
		break;
}
jsonError();