<?php
require_once('config.php');
require_once(DOCUMENT_ROOT.'/view/gazeta.php');

switch(@$_POST['op']) {
	case 'client_sel':
		$send['spisok'] = array();
		if(!empty($_POST['val']) && !preg_match(REGEXP_WORDFIND, win1251($_POST['val'])))
			jsonSuccess($send);
		if(!preg_match(REGEXP_NUMERIC, $_POST['client_id']))
			jsonSuccess($send);
		$val = addslashes(win1251(trim($_POST['val'])));
		$client_id = intval($_POST['client_id']);
		$sql = "SELECT *
				FROM `gazeta_client`
				WHERE `deleted`=0".
			(!empty($val) ? " AND (`org_name` LIKE '%".$val."%' OR `fio` LIKE '%".$val."%' OR `telefon` LIKE '%".$val."%' OR `adres` LIKE '%".$val."%')" : '').
			($client_id ? " AND `id`<=".$client_id : '')."
				ORDER BY `id` DESC
				LIMIT 50";
		$q = query($sql);
		while($r = mysql_fetch_assoc($q)) {
			$name = $r['org_name'] ? $r['org_name'] : $r['fio'];
			$unit = array(
				'uid' => $r['id'],
				'title' => utf8(htmlspecialchars_decode($name))
			);
			$content = array();
			if($r['telefon'])
				$content[] = $r['telefon'];
			if($r['adres'])
				$content[] = $r['adres'];
			if(!empty($content))
				$unit['content'] = utf8($name.'<span>'.implode('<br />', $content).'</span>');
			$send['spisok'][] = $unit;
		}
		jsonSuccess($send);
		break;
	case 'client_add':
		if(!preg_match(REGEXP_NUMERIC, $_POST['person']) || !$_POST['person'])
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['skidka']))
			jsonError();
		$person = intval($_POST['person']);
		$fio = win1251(htmlspecialchars(trim($_POST['fio'])));
		$org_name = win1251(htmlspecialchars(trim($_POST['org_name'])));
		$telefon = win1251(htmlspecialchars(trim($_POST['telefon'])));
		$adres = win1251(htmlspecialchars(trim($_POST['adres'])));
		$inn = win1251(htmlspecialchars(trim($_POST['inn'])));
		$kpp = win1251(htmlspecialchars(trim($_POST['kpp'])));
		$email = win1251(htmlspecialchars(trim($_POST['email'])));
		$skidka = intval($_POST['skidka']);

		if(empty($fio) && empty($org_name))
			jsonError();

		$sql = "INSERT INTO `gazeta_client` (
					`person`,
					`fio`,
					`org_name`,
					`telefon`,
					`adres`,
					`inn`,
					`kpp`,
					`email`,
					`skidka`,
					`viewer_id_add`
				) VALUES (
					".$person.",
					'".addslashes($fio)."',
					'".addslashes($org_name)."',
					'".addslashes($telefon)."',
					'".addslashes($adres)."',
					'".addslashes($inn)."',
					'".addslashes($kpp)."',
					'".addslashes($email)."',
					".$skidka.",
					".VIEWER_ID."
				)";
		query($sql);

		$name = $org_name ? $org_name : $fio;
		$content = array();
		if($telefon)
			$content[] = $telefon;
		if($adres)
			$content[] = $adres;
		$send = array(
			'uid' => mysql_insert_id(),
			'title' => utf8($name),
			'content' => utf8($name.'<span>'.implode('<br />', $content).'</span>')
		);

		history_insert(array(
			'type' => 51,
			'client_id' => $send['uid']
		));
		jsonSuccess($send);
		break;
	case 'client_edit':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']) || !$_POST['id'])
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['person']) || !$_POST['person'])
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['skidka']))
			jsonError();

		$client_id = intval($_POST['id']);

		$sql = "SELECT * FROM `gazeta_client` WHERE `id`=".$client_id." LIMIT 1";
		if(!$client = mysql_fetch_assoc(query($sql)))
			jsonError();

		$send = array(
			'id' => $client_id,
			'person' => intval($_POST['person']),
			'fio' => win1251(htmlspecialchars(trim($_POST['fio']))),
			'org_name' => win1251(htmlspecialchars(trim($_POST['org_name']))),
			'telefon' => win1251(htmlspecialchars(trim($_POST['telefon']))),
			'adres' => win1251(htmlspecialchars(trim($_POST['adres']))),
			'inn' => win1251(htmlspecialchars(trim($_POST['inn']))),
			'kpp' => win1251(htmlspecialchars(trim($_POST['kpp']))),
			'email' => win1251(htmlspecialchars(trim($_POST['email']))),
			'skidka' => intval($_POST['skidka']),
			'balans' => clientBalansUpdate($client_id),
			'viewer_id_add' => $client['viewer_id_add'],
			'dtime_add' => $client['dtime_add']
		);

		if(empty($send['fio']) && empty($send['org_name']))
			jsonError();

		$sql = "UPDATE `gazeta_client`
				SET	`person`=".$send['person'].",
					`fio`='".addslashes($send['fio'])."',
					`org_name`='".addslashes($send['org_name'])."',
					`telefon`='".addslashes($send['telefon'])."',
					`adres`='".addslashes($send['adres'])."',
					`inn`='".addslashes($send['inn'])."',
					`kpp`='".addslashes($send['kpp'])."',
					`email`='".addslashes($send['email'])."',
					`skidka`=".$send['skidka']."
				WHERE `id`=".$client_id;
		query($sql);

		$changes = '';
		if($client['person'] != $send['person'])
			$changes .= '<tr><th>Категория:<td>'._person($client['person']).'<td>»<td>'._person($send['person']);
		if($client['fio'] != $send['fio'])
			$changes .= '<tr><th>Фио:<td>'.$client['fio'].'<td>»<td>'.$send['fio'];
		if($client['org_name'] != $send['org_name'])
			$changes .= '<tr><th>Организация:<td>'.$client['org_name'].'<td>»<td>'.$send['org_name'];
		if($client['telefon'] != $send['telefon'])
			$changes .= '<tr><th>Телефон:<td>'.$client['telefon'].'<td>»<td>'.$send['telefon'];
		if($client['adres'] != $send['adres'])
			$changes .= '<tr><th>Адрес:<td>'.$client['adres'].'<td>»<td>'.$send['adres'];
		if($client['inn'] != $send['inn'])
			$changes .= '<tr><th>ИНН:<td>'.$client['inn'].'<td>»<td>'.$send['inn'];
		if($client['kpp'] != $send['kpp'])
			$changes .= '<tr><th>КПП:<td>'.$client['kpp'].'<td>»<td>'.$send['kpp'];
		if($client['email'] != $send['email'])
			$changes .= '<tr><th>E-mail:<td>'.$client['email'].'<td>»<td>'.$send['email'];
		if($client['skidka'] != $send['skidka'])
			$changes .= '<tr><th>Скидка:<td>'.$client['skidka'].'%<td>»<td>'.$send['skidka'].'%';
		if($changes)
			history_insert(array(
				'type' => 52,
				'client_id' => $client_id,
				'value' => '<table>'.$changes.'</table>'
			));

		$send['html'] = clientInfoGet($send);
		foreach($send as $i => $v)
			$send[$i] = utf8($v);
		jsonSuccess($send);
		break;
	case 'client_del':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		$client_id = intval($_POST['id']);
		if(!query_value("SELECT COUNT(`id`) FROM `gazeta_client` WHERE `deleted`=0 AND `id`=".$client_id))
			jsonError();
		query("UPDATE `gazeta_client` SET `deleted`=1 WHERE `id`=".$client_id);
		//query("UPDATE `zayav` SET `deleted`=1 WHERE `client_id`=".$client_id);
		//query("UPDATE `money` SET `deleted`=1 WHERE `client_id`=".$client_id);
		history_insert(array(
			'type' => 53,
			'client_id' => $client_id
		));
		jsonSuccess();
		break;
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


	case 'zayav_spisok':
		$data = zayav_data($_POST);
		$send['result'] = utf8($data['result']);
		$send['spisok'] = utf8($data['spisok']);
		if(!$data['filter']['nomer'] && $data['filter']['page'] > 1)
			$send['gn_sel'] = gnJson($data['filter']['gnyear'], 1);
		jsonSuccess($send);
		break;
	case 'zayav_add':
		if(!preg_match(REGEXP_NUMERIC, $_POST['client_id']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['category']) || !$_POST['category'])
			jsonError();
		$category = intval($_POST['category']);
		if($category == 1 && (!preg_match(REGEXP_NUMERIC, $_POST['rubric_id']) || !$_POST['rubric_id']))
			jsonError();
		if($category == 1 && !preg_match(REGEXP_NUMERIC, $_POST['rubric_sub_id']))
			jsonError();
		if($category == 2 && (!preg_match(REGEXP_CENA, $_POST['size_x']) || $_POST['size_x'] == 0))
			jsonError();
		if($category == 2 && (!preg_match(REGEXP_CENA, $_POST['size_y']) || $_POST['size_y'] == 0))
			jsonError();
		if($category == 2 && !preg_match(REGEXP_NUMERIC, $_POST['skidka']))
			jsonError();
		if(!preg_match(REGEXP_BOOL, $_POST['summa_manual']))
			jsonError();
		$client_id = intval($_POST['client_id']);
		$rubric_id = $category == 1 ? intval($_POST['rubric_id']) : 0;
		$rubric_sub_id = $category == 1 ? intval($_POST['rubric_sub_id']) : 0;
		$txt = $category == 1 ? win1251(htmlspecialchars(trim($_POST['txt']))) : '';
		$telefon = $category == 1 ? win1251(htmlspecialchars(trim($_POST['telefon']))) : '';
		$adres = $category == 1 ? win1251(htmlspecialchars(trim($_POST['adres']))) : '';
		$size_x = $category == 2 ? round(str_replace(',', '.', $_POST['size_x']), 1) : 0;
		$size_y = $category == 2 ? round(str_replace(',', '.', $_POST['size_y']), 1) : 0;
		$skidka = $category == 2 ? intval($_POST['skidka']) : 0;
		$summa_manual = intval($_POST['summa_manual']);
		$note = win1251(htmlspecialchars(trim($_POST['note'])));
		if($category == 2 && !$client_id)
			jsonError();
		if($category == 1 && !$telefon && !$adres)
			jsonError();
		if(empty($_POST['gns']))
			jsonError();
		if(!$gns = gns_control($_POST['gns'], $category))
			jsonError();
		$skidka_sum = $category == 2 && $skidka ? round($gns['summa']/(100 - $skidka) * 100 - $gns['summa'], 2) : 0;

		$sql = "INSERT INTO `gazeta_zayav` (
				    `client_id`,
				    `category`,

				    `rubric_id`,
				    `rubric_sub_id`,
				    `txt`,
				    `telefon`,
				    `adres`,

				    `size_x`,
				    `size_y`,

				    `summa_manual`,
				    `summa`,
				    `skidka`,
				    `skidka_sum`,

				    `gn_count`,
					`viewer_id_add`
				) VALUES (
				    ".$client_id.",
				    ".$category.",

				    ".$rubric_id.",
				    ".$rubric_sub_id.",
				    '".addslashes($txt)."',
				    '".addslashes($telefon)."',
				    '".addslashes($adres)."',

				    ".$size_x.",
				    ".$size_y.",

				    ".$summa_manual.",
				    ".$gns['summa'].",
				    ".$skidka.",
				    ".$skidka_sum.",

				    ".$gns['count'].",
				    ".VIEWER_ID."
				)";
		query($sql);
		$send['id'] = mysql_insert_id();

		query("INSERT INTO `gazeta_nomer_pub` (
					`zayav_id`,
					`general_nomer`,
					`dop`,
					`cena`
			   ) VALUES ".str_replace('{zayav_id}', $send['id'], $gns['insert']));

		history_insert(array(
			'type' => 11,
			'client_id' => $client_id,
			'zayav_id' => $send['id']
		));

		_vkCommentAdd('zayav', $send['id'], $note);

		jsonSuccess($send);
		break;
	case 'zayav_edit':
		if(!preg_match(REGEXP_NUMERIC, $_POST['zayav_id']) || !$_POST['zayav_id'])
			jsonError();
		$zayav_id = intval($_POST['zayav_id']);
		$sql = "SELECT * FROM `gazeta_zayav` WHERE !`deleted` AND `id`=".$zayav_id;
		if(!$z = mysql_fetch_assoc(query($sql)))
			jsonError();

		$client_id = $z['client_id'];
		$rubric_id = 0;
		$rubric_sub_id = 0;
		$txt = '';
		$telefon = '';
		$adres = '';
		$size_x = 0;
		$size_y = 0;
		$summa_manual = 0;
		$skidka = 0;
		$skidka_sum = 0;

		if(!$client_id) {
			if(!preg_match(REGEXP_NUMERIC, $_POST['client_id']))
				jsonError();
			$client_id = intval($_POST['client_id']);
		}

		if(!$gns = gns_control($_POST['gns'], $z['category'], $zayav_id))
			jsonError();

		if($z['category'] == 1) {
			if(!preg_match(REGEXP_NUMERIC, $_POST['rubric_id']))
				jsonError();
			if(!preg_match(REGEXP_NUMERIC, $_POST['rubric_sub_id']))
				jsonError();
			$rubric_id = intval($_POST['rubric_id']);
			$rubric_sub_id = intval($_POST['rubric_sub_id']);
			$txt = win1251(htmlspecialchars(trim($_POST['txt'])));
			$telefon = win1251(htmlspecialchars(trim($_POST['telefon'])));
			$adres = win1251(htmlspecialchars(trim($_POST['adres'])));
			if(!$telefon && !$adres)
				jsonError();
		}
		if($z['category'] == 2) {
			if(!preg_match(REGEXP_CENA, $_POST['size_x']) && $_POST['size_x'] == 0)
				jsonError();
			if(!preg_match(REGEXP_CENA, $_POST['size_y']) && $_POST['size_y'] == 0)
				jsonError();
			if(!preg_match(REGEXP_NUMERIC, $_POST['skidka']))
				jsonError();
			$size_x = round(str_replace(',', '.', $_POST['size_x']), 1);
			$size_y = round(str_replace(',', '.', $_POST['size_y']), 1);
			$skidka = intval($_POST['skidka']);
			$skidka_sum = $skidka ? round($gns['summa']/(100 - $skidka) * 100 - $gns['summa'], 2) : 0;
		}
		if($z['category'] < 3) {
			if(!preg_match(REGEXP_BOOL, $_POST['summa_manual']))
				jsonError();
			$summa_manual = intval($_POST['summa_manual']);
		}

		//Сохранение предыдущий номеров выпуска для истории
		$gnPrev = array();
		$sql = "SELECT *
				FROM `gazeta_nomer_pub`
				WHERE `zayav_id`=".$zayav_id."
				  AND `general_nomer`>=".GN_FIRST_ACTIVE."
				ORDER BY `general_nomer`";
		$q = query($sql);
		while($r = mysql_fetch_assoc($q))
			$gnPrev[$r['general_nomer']] = array(
				'cena' => round($r['cena'], 2),
				'dop' => $r['dop']
			);

		//Обновление номеров выпуска
		query("DELETE FROM `gazeta_nomer_pub` WHERE `zayav_id`=".$zayav_id." AND `general_nomer`>=".GN_FIRST_ACTIVE);
		if($gns['count'])
			query("INSERT INTO `gazeta_nomer_pub` (
					`zayav_id`,
					`general_nomer`,
					`dop`,
					`cena`
			       ) VALUES ".$gns['insert']);
		$pub = query_assoc("
				SELECT
					COUNT(*) AS `count`,
					IFNULL(SUM(`cena`),0) AS `summa`
				FROM `gazeta_nomer_pub`
				WHERE `zayav_id`=".$zayav_id);

		$sql = "UPDATE `gazeta_zayav`
		        SET `client_id`=".$client_id.",

				    `rubric_id`=".$rubric_id.",
				    `rubric_sub_id`=".$rubric_sub_id.",
				    `txt`='".addslashes($txt)."',
				    `telefon`='".addslashes($telefon)."',
				    `adres`='".addslashes($adres)."',

				    `size_x`=".$size_x.",
				    `size_y`=".$size_y.",

				    `summa_manual`=".$summa_manual.",
				    `summa`=".$pub['summa'].",
				    `skidka`=".$skidka.",
				    `skidka_sum`=".$skidka_sum.",

				    `gn_count`=".$pub['count']."
				WHERE `id`=".$zayav_id;
		query($sql);

		$changes = '';
		if($z['client_id'] != $client_id)
			$changes .= '<tr><th>Клиент:<td><td>»<td>'._clientLink($client_id);
		if($z['rubric_id'] != $rubric_id || $z['rubric_sub_id'] != $rubric_sub_id)
			$changes .= '<tr><th>Рубрика:'.
							'<td>'._rubric($z['rubric_id']).($z['rubric_sub_id'] ? ', '._rubricsub($z['rubric_sub_id']) : '').
							'<td>»'.
							'<td>'._rubric($rubric_id).($rubric_sub_id ? ', '._rubricsub($rubric_sub_id) : '');
		if($z['txt'] != $txt)
			$changes .= '<tr><th>Текст:<td>'.$z['txt'].'<td>»<td>'.$txt;
		if($z['telefon'] != $telefon)
			$changes .= '<tr><th>Телефон:<td>'.$z['telefon'].'<td>»<td>'.$telefon;
		if($z['adres'] != $adres)
			$changes .= '<tr><th>Адрес:<td>'.$z['adres'].'<td>»<td>'.$adres;
		if($z['size_x'] != $size_x || $z['size_y'] != $size_y)
			$changes .= '<tr><th>Размер блока:'.
							'<td>'.round($size_x, 1).'x'.round($size_y, 1).'='.round($size_x * $size_y).
							'<td>»'.
							'<td>'.round($z['size_x'], 1).'x'.round($z['size_y'], 1).'='.round($z['size_x'] * $z['size_y']);
		if($z['summa_manual'] != $summa_manual)
			$changes .= '<tr><th>Сумма указана вручную:<td>'.($z['summa_manual'] ? 'да' : 'нет').'<td>»<td>'.($summa_manual ? 'да' : 'нет');
		if($z['skidka'] != $skidka)
			$changes .= '<tr><th>Скидка:<td>'.$z['skidka'].'%<td>»<td>'.$skidka.'%';
		if(round($z['summa'], 2) != round($pub['summa'], 2))
			$changes .= '<tr><th>Стоимость:<td>'.round($z['summa'], 2).'<td>»<td>'.round($pub['summa'], 2);
		if($z['skidka_sum'] != $skidka_sum)
			$changes .= '<tr><th>Сумма скидки:<td>'.round($z['skidka_sum'], 2).'<td>»<td>'.round($skidka_sum, 2);
		//Проверка на удаление номеров выпуска или их изменение
		$gnAdd = array();
		$gnDel = array();
		$gnCh = array();
		foreach($gnPrev as $n => $r)
			if(empty($gns['array'][$n]))
				$gnDel[] =
					'<b>'._gn($n, 'week').'</b>('.$n.') '.
					_gn($n, 'pub').', '.
					'цена: '.$r['cena'].
					($z['category'] == 1 && $r['dop'] ? ', '._obDop($r['dop']) : '').
					($z['category'] == 2 && $r['dop'] ? ', полоса: '._polosa($r['dop']) : '');
			else {
				$new = $gns['array'][$n];
				if($new['cena'] != $r['cena'] || $new['dop'] != $r['dop'])
					$gnCh[] =
						'<b>'._gn($n, 'week').'</b>('.$n.') '.
						_gn($n, 'pub').'<br />'.
						'&nbsp;&nbsp;цена: '.$r['cena'].' » '.$new['cena'].
						($z['category'] == 1 && $r['dop'] ? '<br />&nbsp;&nbsp;доп: '._obDop($r['dop']).' » '._obDop($new['dop']) : '').
						($z['category'] == 2 && $r['dop'] ? '<br />&nbsp;&nbsp;полоса: '._polosa($r['dop']).' » '._polosa($new['dop']) : '');
				unset($gns['array'][$n]);
			}
		//Проверка на добавление новых номеров выпуска
		foreach($gns['array'] as $n => $r)
			$gnAdd[] =
				'<b>'._gn($n, 'week').'</b>('.$n.') '.
				_gn($n, 'pub').', '.
				'цена: '.$r['cena'].
				($z['category'] == 1 && $r['dop'] ? ', '._obDop($r['dop']) : '').
				($z['category'] == 2 && $r['dop'] ? ', полоса: '._polosa($r['dop']) : '');
		$gnChanges = '';
		if(!empty($gnAdd))
			$gnChanges .= '<tr><th>Добавлены<br />номера выпуска:'.
							'<td colspan="3">'.implode('<br />', $gnAdd);
		if(!empty($gnDel))
			$gnChanges .= '<tr><th>Удалены<br />номера выпуска:'.
							'<td colspan="3">'.implode('<br />', $gnDel);
		if(!empty($gnCh))
			$gnChanges .= '<tr><th>Изменены<br />номера выпуска:'.
							'<td colspan="3">'.implode('<br />', $gnCh);
		if($changes || $gnChanges)
			history_insert(array(
				'type' => 31,
				'client_id' => $client_id,
				'zayav_id' => $zayav_id,
				'value' => ($changes ? '<table>'.$changes.'</table>' : '').
						   ($gnChanges ? '<table>'.$gnChanges.'</table>' : '')
			));
		jsonSuccess();
		break;

	case 'history_next':
		if(!preg_match(REGEXP_NUMERIC, $_POST['page']))
			jsonError();
		$page = intval($_POST['page']);
		$send['html'] = utf8(history_spisok($page));
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

		history_insert(array(
			'type' => 1081,
			'value' => $viewer_id
		));

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

		history_insert(array(
			'type' => 1082,
			'value' => $viewer_id
		));

		$send['html'] = utf8(setup_worker_spisok());
		jsonSuccess($send);
		break;

	case 'setup_gn_spisok_get':
		if(!preg_match(REGEXP_YEAR, $_POST['year']))
			jsonError();
		$year = intval($_POST['year']);
		$send['html'] = utf8(setup_gn_spisok($year));
		jsonSuccess($send);
		break;
	case 'setup_gn_spisok_create':
		if(!preg_match(REGEXP_YEAR, $_POST['year']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['week_nomer']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['general_nomer']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['day_print']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['day_public']))
			jsonError();
		if(!preg_match(REGEXP_DATE, $_POST['day_first']))
			jsonError();
		$year = intval($_POST['year']);
		$week_nomer = intval($_POST['week_nomer']);
		$general_nomer = intval($_POST['general_nomer']);
		$day_print = intval($_POST['day_print']);
		$day_public = intval($_POST['day_public']);
		$day_first = $_POST['day_first'];

		$sql = "SELECT `general_nomer` FROM `gazeta_nomer` WHERE `general_nomer`=".$general_nomer;
		if(query_value($sql))
			jsonError('Номер газеты <b>'.$general_nomer.'</b> уже есть в списке');

		// Первая неделя
		$weekFirst = strtotime($day_first);
		// Номер дня первой недели
		$printFirst = date('w', $weekFirst);
		if($printFirst == 0)
			$printFirst = 7;
		// Приведение первой недели к понедельнику
		if($printFirst != 1)
			$weekFirst -= 86400 * ($printFirst - 1);
		// Определение первого дня следующего года, если цикл за него уходит, то остановка
		$timeEnd = strtotime($year.'-12-31');
		$gnArr = array();
		while($weekFirst < $timeEnd) {
			array_push($gnArr, '('.
		        $general_nomer++.','.
		        $week_nomer++.','.
		        'DATE_ADD("'.strftime('%Y-%m-%d', $weekFirst).'", INTERVAL '.$day_print.' DAY),'.
		        'DATE_ADD("'.strftime('%Y-%m-%d', $weekFirst).'", INTERVAL '.$day_public.' DAY))'
			);
			$weekFirst += 604800;
		}

		$sql = 'INSERT INTO `gazeta_nomer` (
					`general_nomer`,
					`week_nomer`,
					`day_print`,
					`day_public`
				) VALUES '.implode(',', $gnArr);
		query($sql);

		xcache_unset(CACHE_PREFIX.'gn');
		GvaluesCreate();

		history_insert(array(
			'type' => 1034,
			'value' => $year
		));

		$send['year'] = utf8(setup_gn_year($year));
		$send['html'] = utf8(setup_gn_spisok($year));
		jsonSuccess($send);
		break;
	case 'setup_gn_add':
		if(!preg_match(REGEXP_NUMERIC, $_POST['week_nomer']))
			jsonError('Некорректно указан номер недели выпуска');
		if(!preg_match(REGEXP_NUMERIC, $_POST['general_nomer']))
			jsonError('Некорректно указан общий номер выпуска');
		if(!preg_match(REGEXP_DATE, $_POST['day_print']))
			jsonError('Некорректно указана дата отправки в печать');
		if(!preg_match(REGEXP_DATE, $_POST['day_public']))
			jsonError('Некорректно указана дата выхода газеты');
		if(!preg_match(REGEXP_YEAR, $_POST['year']))
			jsonError();
		$week_nomer = intval($_POST['week_nomer']);
		$general_nomer = intval($_POST['general_nomer']);
		$day_print = $_POST['day_print'];
		$day_public = $_POST['day_public'];
		$year = intval($_POST['year']);

		if(query_value("SELECT `general_nomer` FROM `gazeta_nomer` WHERE `general_nomer`=".$general_nomer))
			jsonError('Номер газеты <b>'.$general_nomer.'</b> уже есть в списке');

		$sql = "INSERT INTO `gazeta_nomer` (
					`week_nomer`,
					`general_nomer`,
					`day_print`,
					`day_public`
				) VALUES (
					".$week_nomer.",
					".$general_nomer.",
					'".$day_print."',
					'".$day_public."'
				)";
		query($sql);

		xcache_unset(CACHE_PREFIX.'gn');
		GvaluesCreate();

		history_insert(array(
			'type' => 1031,
			'value' => $general_nomer
		));

		$send['year'] = utf8(setup_gn_year($year));
		$send['html'] = utf8(setup_gn_spisok($year, $general_nomer));
		jsonSuccess($send);
		break;
	case 'setup_gn_edit':
		if(!preg_match(REGEXP_NUMERIC, $_POST['gn']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['week_nomer']))
			jsonError('Некорректно указан номер недели выпуска');
		if(!preg_match(REGEXP_NUMERIC, $_POST['general_nomer']))
			jsonError('Некорректно указан общий номер выпуска');
		if(!preg_match(REGEXP_DATE, $_POST['day_print']))
			jsonError('Некорректно указана дата отправки в печать');
		if(!preg_match(REGEXP_DATE, $_POST['day_public']))
			jsonError('Некорректно указана дата выхода газеты');
		if(!preg_match(REGEXP_YEAR, $_POST['year']))
			jsonError();
		$gn = intval($_POST['gn']);
		$week_nomer = intval($_POST['week_nomer']);
		$general_nomer = intval($_POST['general_nomer']);
		$day_print = $_POST['day_print'];
		$day_public = $_POST['day_public'];
		$year = intval($_POST['year']);

		if($gn != $general_nomer) {
			$sql = "SELECT `general_nomer` FROM `gazeta_nomer` WHERE `general_nomer`=".$general_nomer;
			if(query_value($sql))
				jsonError('Номер газеты <b>'.$general_nomer.'</b> уже есть в списке');
		}

		$sql = "SELECT * FROM `gazeta_nomer` WHERE `general_nomer`=".$general_nomer." LIMIT 1";
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		$sql = "UPDATE `gazeta_nomer`
				SET `week_nomer`=".$week_nomer.",
					`general_nomer`=".$general_nomer.",
					`day_print`='".$day_print."',
					`day_public`='".$day_public."'
				WHERE `general_nomer`=".$gn."
				LIMIT 1";
		query($sql);

		xcache_unset(CACHE_PREFIX.'gn');
		GvaluesCreate();

		$changes = '';
		if($r['week_nomer'] != $week_nomer || $r['general_nomer'] != $general_nomer)
			$changes .= '<tr><th>Номер выпуска:<td><b>'.$r['week_nomer'].'</b>('.$r['general_nomer'].')'.
							'<td>»'.
							'<td><b>'.$week_nomer.'</b>('.$general_nomer.')';
		if($r['day_print'] != $day_print)
			$changes .= '<tr><th>День отправки в печать:<td>'.FullData($r['day_print']).'<td>»<td>'.FullData($day_print);
		if($r['day_public'] != $day_public)
			$changes .= '<tr><th>День выхода:<td>'.FullData($r['day_public']).'<td>»<td>'.FullData($day_public);
		if($changes)
			history_insert(array(
				'type' => 1032,
				'value' => $general_nomer,
				'value1' => '<table>'.$changes.'</table>'
			));

		$send['year'] = utf8(setup_gn_year($year));
		$send['html'] = utf8(setup_gn_spisok($year, $general_nomer));
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

		history_insert(array(
			'type' => 1033,
			'value' => $general
		));

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

		history_insert(array(
			'type' => 1011,
			'value' => $name
		));

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

		$changes = '';
		if($r['name'] != $name)
			$changes .= '<tr><th>Наименование:<td>'.$r['name'].'<td>»<td>'.$name;
		if($changes)
			history_insert(array(
				'type' => 1012,
				'value' => $name,
				'value1' => '<table>'.$changes.'</table>'
			));

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

		history_insert(array(
			'type' => 1013,
			'value' => $r['name']
		));

		$send['html'] = utf8(setup_person_spisok());
		jsonSuccess($send);
		break;

	case 'setup_rubric_add':
		$name = win1251(htmlspecialchars(trim($_POST['name'])));
		if(empty($name))
			jsonError();
		$sql = "INSERT INTO `setup_rubric` (
					`name`,
					`sort`
				) VALUES (
					'".addslashes($name)."',
					"._maxSql('setup_rubric', 'sort')."
				)";
		query($sql);

		xcache_unset(CACHE_PREFIX.'rubric');
		GvaluesCreate();

		history_insert(array(
			'type' => 1021,
			'value' => $name
		));

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

		$changes = '';
		if($r['name'] != $name)
			$changes .= '<tr><th>Наименование:<td>'.$r['name'].'<td>»<td>'.$name;
		if($changes)
			history_insert(array(
				'type' => 1022,
				'value' => $name,
				'value1' => '<table>'.$changes.'</table>'
			));

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

		history_insert(array(
			'type' => 1023,
			'value' => $r['name']
		));

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
					`sort`
				) VALUES (
					".$rubric_id.",
					'".addslashes($name)."',
					"._maxSql('setup_rubric_sub', 'sort')."
				)";
		query($sql);

		xcache_unset(CACHE_PREFIX.'rubric_sub');
		GvaluesCreate();

		history_insert(array(
			'type' => 1071,
			'value' => _rubric($rubric_id),
			'value1' => $name
		));

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

		$changes = '';
		if($r['name'] != $name)
			$changes .= '<tr><th>Наименование:<td>'.$r['name'].'<td>»<td>'.$name;
		if($changes)
			history_insert(array(
				'type' => 1072,
				'value' => _rubric($r['rubric_id']),
				'value1' => '<table>'.$changes.'</table>'
			));

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

		history_insert(array(
			'type' => 1073,
			'value' => _rubric($r['rubric_id']),
			'value1' => $r['name']
		));

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

		$sql = "SELECT * FROM `setup_global` LIMIT 1";
		$g = mysql_fetch_assoc(query($sql));

		if($g['txt_len_first'] == $txt_len_first &&
		   $g['txt_cena_first'] == $txt_cena_first &&
		   $g['txt_len_next'] == $txt_len_next &&
		   $g['txt_cena_next'] == $txt_cena_next)
			jsonError();

		$sql = "UPDATE `setup_global`
				SET `txt_len_first`=".$txt_len_first.",
					`txt_cena_first`=".$txt_cena_first.",
					`txt_len_next`=".$txt_len_next.",
					`txt_cena_next`=".$txt_cena_next."
				LIMIT 1";
		query($sql);

		history_insert(array(
			'type' => 1091
		));

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

		$changes = '';
		if($r['cena'] != $cena)
			$changes .= '<tr><th>Стоимость:<td>'.$r['cena'].'<td>»<td>'.$cena;
		if($changes)
			history_insert(array(
				'type' => 1062,
				'value' => $r['name'],
				'value1' => '<table>'.$changes.'</table>'
			));

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

		history_insert(array(
			'type' => 1041,
			'value' => $name
		));

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

		$changes = '';
		if($r['name'] != $name)
			$changes .= '<tr><th>Наименование:<td>'.$r['name'].'<td>»<td>'.$name;
		if($r['cena'] != $cena)
			$changes .= '<tr><th>Наименование:<td>'.round($r['cena'], 2).'<td>»<td>'.round($cena, 2);
		if($changes)
			history_insert(array(
				'type' => 1042,
				'value' => $name,
				'value1' => '<table>'.$changes.'</table>'
			));

		$send['html'] = utf8(setup_polosa_spisok());
		jsonSuccess($send);
		break;

	case 'setup_invoice_add':
		$name = win1251(htmlspecialchars(trim($_POST['name'])));
		$about = win1251(htmlspecialchars(trim($_POST['about'])));
		$types = trim($_POST['types']);
		if(empty($name))
			jsonError();

		if(!empty($types)) {
			foreach(explode(',', $types) as $id)
				if(!preg_match(REGEXP_NUMERIC, $id))
					jsonError();
			$prihod = query_value("SELECT `name` FROM `setup_income` WHERE `id` IN (".$types.") AND `invoice_id`>0 LIMIT 1");
			if($prihod)
				jsonError('Вид платежа <u>'.$prihod.'</u> задействован в другом счёте');
		}
		$sql = "INSERT INTO `gazeta_invoice` (
					`name`,
					`about`
				) VALUES (
					'".addslashes($name)."',
					'".addslashes($about)."'
				)";
		query($sql);

		if(!empty($types))
			query("UPDATE `setup_income` SET `invoice_id`=".mysql_insert_id()." WHERE `id` IN (".$types.")");

		xcache_unset(CACHE_PREFIX.'invoice');
		GvaluesCreate();

		history_insert(array(
			'type' => 1121,
			'value' => $name
		));


		$send['html'] = utf8(setup_invoice_spisok());
		jsonSuccess($send);
		break;
	case 'setup_invoice_edit':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		$invoice_id = intval($_POST['id']);
		$name = win1251(htmlspecialchars(trim($_POST['name'])));
		$about = win1251(htmlspecialchars(trim($_POST['about'])));
		$types = trim($_POST['types']);
		if(empty($name))
			jsonError();

		if(!empty($types)) {
			foreach(explode(',', $types) as $id)
				if(!preg_match(REGEXP_NUMERIC, $id))
					jsonError();
			$prihod = query_value("SELECT `name`
								   FROM `setup_income`
								   WHERE `id` IN (".$types.")
								     AND `invoice_id`>0
								     AND `invoice_id`!=".$invoice_id."
								   LIMIT 1");
			if($prihod)
				jsonError('Вид платежа <u>'.$prihod.'</u> задействован в другом счёте');
		}

		$sql = "SELECT * FROM `gazeta_invoice` WHERE `id`=".$invoice_id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		$sql = "UPDATE `gazeta_invoice`
				SET `name`='".addslashes($name)."',
					`about`='".addslashes($about)."'
				WHERE `id`=".$invoice_id;
		query($sql);

		query("UPDATE `setup_income` SET `invoice_id`=0 WHERE `invoice_id`=".$invoice_id);
		if(!empty($types))
			query("UPDATE `setup_income` SET `invoice_id`=".$invoice_id." WHERE `id` IN (".$types.")");


		xcache_unset(CACHE_PREFIX.'invoice');
		GvaluesCreate();

		$changes = '';
		if($r['name'] != $name)
			$changes .= '<tr><th>Наименование:<td>'.$r['name'].'<td>»<td>'.$name;
		if($r['about'] != $about)
			$changes .= '<tr><th>Описание:<td>'.str_replace("\n", '<br />', $r['about']).'<td>»<td>'.str_replace("\n", '<br />', $about);
		if($changes)
			history_insert(array(
				'type' => 1122,
				'value' => $name,
				'value1' => '<table>'.$changes.'</table>'
			));

		$send['html'] = utf8(setup_invoice_spisok());
		jsonSuccess($send);
		break;
	case 'setup_invoice_del':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		$invoice_id = intval($_POST['id']);

		$sql = "SELECT * FROM `gazeta_invoice` WHERE `id`=".$invoice_id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		query("DELETE FROM `gazeta_invoice` WHERE `id`=".$invoice_id);
		query("UPDATE `setup_income` SET `invoice_id`=0 WHERE `invoice_id`=".$invoice_id);

		xcache_unset(CACHE_PREFIX.'invoice');
		GvaluesCreate();

		history_insert(array(
			'type' => 1123,
			'value' => $r['name']
		));

		$send['html'] = utf8(setup_invoice_spisok());
		jsonSuccess($send);
		break;

	case 'setup_money_add':
		$name = win1251(htmlspecialchars(trim($_POST['name'])));
		if(empty($name))
			jsonError();
		$sql = "INSERT INTO `setup_income` (
					`name`,
					`sort`
				) VALUES (
					'".addslashes($name)."',
					"._maxSql('setup_income', 'sort')."
				)";
		query($sql);

		xcache_unset(CACHE_PREFIX.'money_type');
		GvaluesCreate();

		history_insert(array(
			'type' => 1111,
			'value' => $name
		));

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

		$sql = "SELECT * FROM `setup_income` WHERE `id`=".$id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		$sql = "UPDATE `setup_income`
				SET `name`='".addslashes($name)."'
				WHERE `id`=".$id;
		query($sql);

		xcache_unset(CACHE_PREFIX.'money_type');
		GvaluesCreate();

		$changes = '';
		if($r['name'] != $name)
			$changes .= '<tr><th>Наименование:<td>'.$r['name'].'<td>»<td>'.$name;
		if($changes)
			history_insert(array(
				'type' => 1112,
				'value' => $name,
				'value1' => '<table>'.$changes.'</table>'
			));

		$send['html'] = utf8(setup_money_spisok());
		jsonSuccess($send);
		break;
	case 'setup_money_del':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		$id = intval($_POST['id']);

		$sql = "SELECT * FROM `setup_income` WHERE `id`=".$id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		if(query_value("SELECT COUNT(`id`) FROM `gazeta_money` WHERE `income_id`=".$id))
			jsonError();
		$sql = "DELETE FROM `setup_income` WHERE `id`=".$id;
		query($sql);

		xcache_unset(CACHE_PREFIX.'money_type');
		GvaluesCreate();

		history_insert(array(
			'type' => 1113,
			'value' => $r['name']
		));

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

		history_insert(array(
			'type' => 1051,
			'value' => $razmer
		));

		$send['html'] = utf8(setup_skidka_spisok());
		jsonSuccess($send);
		break;
	case 'setup_skidka_edit':
		if(!preg_match(REGEXP_NUMERIC, $_POST['razmer']) || $_POST['razmer'] == 0 || $_POST['razmer'] > 100)
			jsonError();
		$razmer = intval($_POST['razmer']);
		$about = win1251(htmlspecialchars(trim($_POST['about'])));

		$sql = "SELECT * FROM `setup_skidka` WHERE `razmer`=".$razmer;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		$sql = "UPDATE `setup_skidka`
				SET `about`='".addslashes($about)."'
				WHERE `razmer`=".$razmer."
				LIMIT 1";
		query($sql);

		xcache_unset(CACHE_PREFIX.'skidka');
		GvaluesCreate();

		$changes = '';
		if($r['about'] != $about)
			$changes .= '<tr><th>Описание:<td>'.$r['about'].'<td>»<td>'.$about;
		if($changes)
			history_insert(array(
				'type' => 1052,
				'value' => $razmer,
				'value1' => '<table>'.$changes.'</table>'
			));

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

		history_insert(array(
			'type' => 1053,
			'value' => $razmer
		));

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

		history_insert(array(
			'type' => 1101,
			'value' => $name
		));

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

		$changes = '';
		if($r['name'] != $name)
			$changes .= '<tr><th>Наименование:<td>'.$r['name'].'<td>»<td>'.$name;
		if($changes)
			history_insert(array(
				'type' => 1102,
				'value' => $name,
				'value1' => '<table>'.$changes.'</table>'
			));

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

		history_insert(array(
			'type' => 1103,
			'value' => $r['name']
		));

		$send['html'] = utf8(setup_rashod_spisok());
		jsonSuccess($send);
		break;
}

jsonError();