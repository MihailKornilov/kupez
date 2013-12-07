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
			jsonError('���� ������������ ��� ��������</br >�����������.');
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

	case 'setup_gn_spisok':
		if(!preg_match(REGEXP_YEAR, $_POST['year']))
			jsonError();
		$year = intval($_POST['year']);
		$send['html'] = utf8(setup_gn_spisok($year));
		jsonSuccess($send);
		break;
	case 'setup_gn_del':
		if(!preg_match(REGEXP_NUMERIC, $_POST['general']))
			jsonError();
		if(!preg_match(REGEXP_YEAR, $_POST['year']))
			jsonError();
		$general = intval($_POST['general']);
		$year = intval($_POST['year']);

		$sql = "SELECT * FROM `gazeta_nomer` WHERE `general_nomer`=".$general;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		$sql = "DELETE FROM `gazeta_nomer` WHERE `general_nomer`=".$general;
		query($sql);

		xcache_unset(CACHE_PREFIX.'gn');
		GvaluesCreate();

		$send['year'] = utf8(setup_gn_year($year));
		$send['html'] = utf8(setup_gn_spisok($year));
		jsonSuccess($send);
		break;

	case 'setup_person_add':
		$name = win1251(htmlspecialchars(trim($_POST['name'])));
		if(empty($name))
			jsonError();
		$sql = "INSERT INTO `setup_person` (
					`name`,
					`sort`
				) VALUES (
					'".addslashes($name)."',
					"._maxSql('setup_person', 'sort')."
				)";
		query($sql);

		xcache_unset(CACHE_PREFIX.'person');
		GvaluesCreate();
/*
		history_insert(array(
			'type' => 507,
			'value' => $name
		));
*/

		$send['html'] = utf8(setup_person_spisok());
		jsonSuccess($send);
		break;
	case 'setup_person_edit':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		$id = intval($_POST['id']);
		$name = win1251(htmlspecialchars(trim($_POST['name'])));
		if(empty($name))
			jsonError();

		$sql = "SELECT * FROM `setup_person` WHERE `id`=".$id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		$sql = "UPDATE `setup_person`
				SET `name`='".addslashes($name)."'
				WHERE `id`=".$id;
		query($sql);

		xcache_unset(CACHE_PREFIX.'person');
		GvaluesCreate();
/*
		$changes = '';
		if($r['name'] != $name)
			$changes .= '<tr><th>������������:<td>'.$r['name'].'<td>�<td>'.$name;
		if($r['kassa_put'] != $kassa_put)
			$changes .= '<tr><th>����������� �������� � �����:<td>'.($r['kassa_put'] ? '��' : '���').'<td>�<td>'.($kassa_put ? '��' : '���');
		if($changes)
			history_insert(array(
				'type' => 508,
				'value' => $name,
				'value1' => '<table>'.$changes.'</table>'
			));
*/
		$send['html'] = utf8(setup_person_spisok());
		jsonSuccess($send);
		break;
	case 'setup_person_del':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		$id = intval($_POST['id']);

		$sql = "SELECT * FROM `setup_person` WHERE `id`=".$id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		if(query_value("SELECT COUNT(`id`) FROM `gazeta_client` WHERE `person`=".$id))
			jsonError();
		$sql = "DELETE FROM `setup_person` WHERE `id`=".$id;
		query($sql);

		xcache_unset(CACHE_PREFIX.'person');
		GvaluesCreate();
/*
		history_insert(array(
			'type' => 509,
			'value' => $r['name']
		));
*/
		$send['html'] = utf8(setup_person_spisok());
		jsonSuccess($send);
		break;

	case 'setup_rubric_add':
		$name = win1251(htmlspecialchars(trim($_POST['name'])));
		if(empty($name))
			jsonError();
		$sql = "INSERT INTO `setup_rubric` (
					`name`,
					`sort`,
					`viewer_id_add`
				) VALUES (
					'".addslashes($name)."',
					"._maxSql('setup_rubric', 'sort').",
					".VIEWER_ID."
				)";
		query($sql);

		xcache_unset(CACHE_PREFIX.'rubric');
		GvaluesCreate();
		/*
				history_insert(array(
					'type' => 507,
					'value' => $name
				));
		*/

		$send['html'] = utf8(setup_rubric_spisok());
		jsonSuccess($send);
		break;
	case 'setup_rubric_edit':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		$id = intval($_POST['id']);
		$name = win1251(htmlspecialchars(trim($_POST['name'])));
		if(empty($name))
			jsonError();

		$sql = "SELECT * FROM `setup_rubric` WHERE `id`=".$id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		$sql = "UPDATE `setup_rubric`
				SET `name`='".addslashes($name)."'
				WHERE `id`=".$id;
		query($sql);

		xcache_unset(CACHE_PREFIX.'rubric');
		GvaluesCreate();

		$send['html'] = utf8(setup_rubric_spisok());
		jsonSuccess($send);
		break;
	case 'setup_rubric_del':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		$id = intval($_POST['id']);

		$sql = "SELECT * FROM `setup_rubric` WHERE `id`=".$id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		if(query_value("SELECT COUNT(`id`) FROM `setup_rubric_sub` WHERE `rubric_id`=".$id))
			jsonError();
		if(query_value("SELECT COUNT(`id`) FROM `gazeta_zayav` WHERE `rubric_id`=".$id))
			jsonError();

		$sql = "DELETE FROM `setup_rubric` WHERE `id`=".$id;
		query($sql);

		xcache_unset(CACHE_PREFIX.'rubric');
		GvaluesCreate();

		$send['html'] = utf8(setup_rubric_spisok());
		jsonSuccess($send);
		break;

	case 'setup_rubric_sub_add':
		if(!preg_match(REGEXP_NUMERIC, $_POST['rubric_id']))
			jsonError();
		$rubric_id = intval($_POST['rubric_id']);
		$name = win1251(htmlspecialchars(trim($_POST['name'])));
		if(empty($name))
			jsonError();

		$sql = "SELECT * FROM `setup_rubric` WHERE `id`=".$rubric_id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		$sql = "INSERT INTO `setup_rubric_sub` (
					`rubric_id`,
					`name`,
					`sort`,
					`viewer_id_add`
				) VALUES (
					".$rubric_id.",
					'".addslashes($name)."',
					"._maxSql('setup_rubric_sub', 'sort').",
					".VIEWER_ID."
				)";
		query($sql);

		xcache_unset(CACHE_PREFIX.'rubric_sub');
		GvaluesCreate();

		$send['html'] = utf8(setup_rubric_sub_spisok($rubric_id));
		jsonSuccess($send);
		break;
	case 'setup_rubric_sub_edit':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();

		$id = intval($_POST['id']);
		$name = win1251(htmlspecialchars(trim($_POST['name'])));
		if(empty($name))
			jsonError();

		$sql = "SELECT * FROM `setup_rubric_sub` WHERE `id`=".$id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		$sql = "UPDATE `setup_rubric_sub`
				SET `name`='".addslashes($name)."'
				WHERE `id`=".$id;
		query($sql);

		xcache_unset(CACHE_PREFIX.'rubric_sub');
		GvaluesCreate();

		$send['html'] = utf8(setup_rubric_sub_spisok($r['rubric_id']));
		jsonSuccess($send);
		break;
	case 'setup_rubric_sub_del':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		$id = intval($_POST['id']);

		$sql = "SELECT * FROM `setup_rubric_sub` WHERE `id`=".$id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		if(query_value("SELECT COUNT(`id`) FROM `gazeta_zayav` WHERE `rubric_sub_id`=".$id))
			jsonError();

		$sql = "DELETE FROM `setup_rubric_sub` WHERE `id`=".$id;
		query($sql);

		xcache_unset(CACHE_PREFIX.'rubric_sub');
		GvaluesCreate();

		$send['html'] = utf8(setup_rubric_sub_spisok($r['rubric_id']));
		jsonSuccess($send);
		break;

	case 'setup_oblen':
		if(!preg_match(REGEXP_NUMERIC, $_POST['txt_len_first']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['txt_cena_first']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['txt_len_next']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['txt_cena_next']))
			jsonError();

		$txt_len_first = intval($_POST['txt_len_first']);
		$txt_cena_first = intval($_POST['txt_cena_first']);
		$txt_len_next = intval($_POST['txt_len_next']);
		$txt_cena_next = intval($_POST['txt_cena_next']);

		$sql = "UPDATE `setup_global`
				SET `txt_len_first`=".$txt_len_first.",
					`txt_cena_first`=".$txt_cena_first.",
					`txt_len_next`=".$txt_len_next.",
					`txt_cena_next`=".$txt_cena_next."
				LIMIT 1";
		query($sql);

		xcache_unset(CACHE_PREFIX.'setup_global');
		GvaluesCreate();

		jsonSuccess();
		break;

	case 'setup_obdop_edit':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		if(!preg_match(REGEXP_CENA, $_POST['cena']))
			jsonError();
		$id = intval($_POST['id']);
		$cena = intval($_POST['cena']);

		$sql = "SELECT * FROM `setup_ob_dop` WHERE `id`=".$id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		$sql = "UPDATE `setup_ob_dop`
				SET `cena`=".$cena."
				WHERE `id`=".$id;
		query($sql);

		xcache_unset(CACHE_PREFIX.'obdop');
		GvaluesCreate();

		$send['html'] = utf8(setup_obdop_spisok());
		jsonSuccess($send);
		break;

	case 'setup_polosa_add':
		if(!preg_match(REGEXP_CENA, $_POST['cena']))
			jsonError();
		$cena = round($_POST['cena'], 2);
		$name = win1251(htmlspecialchars(trim($_POST['name'])));
		if(empty($name))
			jsonError();
		$sql = "INSERT INTO `setup_polosa_cost` (
					`name`,
					`cena`,
					`sort`
				) VALUES (
					'".addslashes($name)."',
					".$cena.",
					"._maxSql('setup_polosa_cost', 'sort')."
				)";
		query($sql);

		xcache_unset(CACHE_PREFIX.'polosa');
		GvaluesCreate();

		$send['html'] = utf8(setup_polosa_spisok());
		jsonSuccess($send);
		break;
	case 'setup_polosa_edit':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		if(!preg_match(REGEXP_CENA, $_POST['cena']))
			jsonError();
		$id = intval($_POST['id']);
		$cena = round($_POST['cena'], 2);
		$name = win1251(htmlspecialchars(trim($_POST['name'])));
		if(empty($name))
			jsonError();

		$sql = "SELECT * FROM `setup_polosa_cost` WHERE `id`=".$id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		$sql = "UPDATE `setup_polosa_cost`
				SET `name`='".addslashes($name)."',
					`cena`=".$cena."
				WHERE `id`=".$id;
		query($sql);

		xcache_unset(CACHE_PREFIX.'polosa');
		GvaluesCreate();

		$send['html'] = utf8(setup_polosa_spisok());
		jsonSuccess($send);
		break;

	case 'setup_money_add':
		$name = win1251(htmlspecialchars(trim($_POST['name'])));
		if(empty($name))
			jsonError();
		$sql = "INSERT INTO `setup_money_type` (
					`name`,
					`sort`
				) VALUES (
					'".addslashes($name)."',
					"._maxSql('setup_money_type', 'sort')."
				)";
		query($sql);

		xcache_unset(CACHE_PREFIX.'money_type');
		GvaluesCreate();

		$send['html'] = utf8(setup_money_spisok());
		jsonSuccess($send);
		break;
	case 'setup_money_edit':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		$id = intval($_POST['id']);
		$name = win1251(htmlspecialchars(trim($_POST['name'])));
		if(empty($name))
			jsonError();

		$sql = "SELECT * FROM `setup_money_type` WHERE `id`=".$id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		$sql = "UPDATE `setup_money_type`
				SET `name`='".addslashes($name)."'
				WHERE `id`=".$id;
		query($sql);

		xcache_unset(CACHE_PREFIX.'money_type');
		GvaluesCreate();

		$send['html'] = utf8(setup_money_spisok());
		jsonSuccess($send);
		break;
	case 'setup_money_del':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		$id = intval($_POST['id']);

		$sql = "SELECT * FROM `setup_money_type` WHERE `id`=".$id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		if(query_value("SELECT COUNT(`id`) FROM `gazeta_money` WHERE `type`=".$id))
			jsonError();
		$sql = "DELETE FROM `setup_money_type` WHERE `id`=".$id;
		query($sql);

		xcache_unset(CACHE_PREFIX.'money_type');
		GvaluesCreate();

		$send['html'] = utf8(setup_money_spisok());
		jsonSuccess($send);
		break;

	case 'setup_skidka_add':
		if(!preg_match(REGEXP_NUMERIC, $_POST['razmer']) || $_POST['razmer'] == 0 || $_POST['razmer'] > 100)
			jsonError();
		$razmer = intval($_POST['razmer']);
		$about = win1251(htmlspecialchars(trim($_POST['about'])));
		if(query_value("SELECT * FROM `setup_skidka` WHERE `razmer`=".$razmer))
			jsonError();
		$sql = "INSERT INTO `setup_skidka` (
					`razmer`,
					`about`
				) VALUES (
					".$razmer.",
					'".addslashes($about)."'
				)";
		query($sql);

		xcache_unset(CACHE_PREFIX.'skidka');
		GvaluesCreate();

		$send['html'] = utf8(setup_skidka_spisok());
		jsonSuccess($send);
		break;
	case 'setup_skidka_edit':
		if(!preg_match(REGEXP_NUMERIC, $_POST['razmer']) || $_POST['razmer'] == 0 || $_POST['razmer'] > 100)
			jsonError();
		$razmer = intval($_POST['razmer']);
		$about = win1251(htmlspecialchars(trim($_POST['about'])));
		if(!query_value("SELECT * FROM `setup_skidka` WHERE `razmer`=".$razmer))
			jsonError();
		$sql = "UPDATE `setup_skidka`
				SET `about`='".addslashes($about)."'
				WHERE `razmer`=".$razmer."
				LIMIT 1";
		query($sql);

		xcache_unset(CACHE_PREFIX.'skidka');
		GvaluesCreate();

		$send['html'] = utf8(setup_skidka_spisok());
		jsonSuccess($send);
		break;
	case 'setup_skidka_del':
		if(!preg_match(REGEXP_NUMERIC, $_POST['razmer']))
			jsonError();
		$razmer = intval($_POST['razmer']);
		if(!query_value("SELECT * FROM `setup_skidka` WHERE `razmer`=".$razmer))
			jsonError();

		if(query_value("SELECT COUNT(`id`) FROM `gazeta_client` WHERE `skidka`=".$razmer))
			jsonError();
		$sql = "DELETE FROM `setup_skidka` WHERE `razmer`=".$razmer;
		query($sql);

		xcache_unset(CACHE_PREFIX.'skidka');
		GvaluesCreate();

		$send['html'] = utf8(setup_skidka_spisok());
		jsonSuccess($send);
		break;

	case 'setup_rashod_add':
		$name = win1251(htmlspecialchars(trim($_POST['name'])));
		if(empty($name))
			jsonError();
		$sql = "INSERT INTO `setup_rashod_category` (
					`name`,
					`sort`
				) VALUES (
					'".addslashes($name)."',
					"._maxSql('setup_rashod_category', 'sort')."
				)";
		query($sql);

		xcache_unset(CACHE_PREFIX.'rashod_category');
		GvaluesCreate();

		$send['html'] = utf8(setup_rashod_spisok());
		jsonSuccess($send);
		break;
	case 'setup_rashod_edit':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		$id = intval($_POST['id']);
		$name = win1251(htmlspecialchars(trim($_POST['name'])));
		if(empty($name))
			jsonError();

		$sql = "SELECT * FROM `setup_rashod_category` WHERE `id`=".$id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		$sql = "UPDATE `setup_rashod_category`
				SET `name`='".addslashes($name)."'
				WHERE `id`=".$id;
		query($sql);

		xcache_unset(CACHE_PREFIX.'rashod_category');
		GvaluesCreate();

		$send['html'] = utf8(setup_rashod_spisok());
		jsonSuccess($send);
		break;
	case 'setup_rashod_del':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		$id = intval($_POST['id']);

		$sql = "SELECT * FROM `setup_rashod_category` WHERE `id`=".$id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		if(query_value("SELECT COUNT(`id`) FROM `gazeta_money` WHERE `rashod_category`=".$id))
			jsonError();
		$sql = "DELETE FROM `setup_rashod_category` WHERE `id`=".$id;
		query($sql);

		xcache_unset(CACHE_PREFIX.'rashod_category');
		GvaluesCreate();

		$send['html'] = utf8(setup_rashod_spisok());
		jsonSuccess($send);
		break;
}

jsonError();