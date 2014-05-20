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

		_historyInsert(
			51,
			array('client_id' => $send['uid']),
			'gazeta_history'
		);

		jsonSuccess($send);
		break;
	case 'client_edit':
		if(!$client_id = _isnum($_POST['id']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['person']) || !$_POST['person'])
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['skidka']))
			jsonError();

		$sql = "SELECT * FROM `gazeta_client` WHERE !`deleted` AND `id`=".$client_id;
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
			'dtime_add' => $client['dtime_add'],
			'deleted' => 0
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
			_historyInsert(
				52,
				array(
					'client_id' => $client_id,
					'value' => '<table>'.$changes.'</table>'
				),
				'gazeta_history'
			);

		$send['html'] = clientInfoGet($send);
		foreach($send as $i => $v)
			$send[$i] = utf8($v);
		jsonSuccess($send);
		break;
	case 'client_del':
		if(!$client_id = _isnum($_POST['id']))
			jsonError();
		if(!query_value("SELECT COUNT(`id`) FROM `gazeta_client` WHERE !`deleted` AND `id`=".$client_id))
			jsonError();
		if(query_value("SELECT COUNT(`id`) FROM `gazeta_zayav` WHERE !`deleted` AND `client_id`=".$client_id))
			jsonError();

		query("UPDATE `gazeta_client`
		       SET `deleted`=1,
		           `dtime_del`=CURRENT_TIMESTAMP,
			       `viewer_id_del`=".VIEWER_ID."
		       WHERE `id`=".$client_id);
		query("UPDATE `gazeta_money`
			   SET `deleted`=1,
			       `dtime_del`=CURRENT_TIMESTAMP,
			       `viewer_id_del`=".VIEWER_ID."
			   WHERE !`deleted`
				 AND `client_id`=".$client_id);
		clientBalansUpdate($client_id);
		_historyInsert(
			53,
			array('client_id' => $client_id),
			'gazeta_history'
		);
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
		$skidka_sum = $category == 2 && $skidka ? round($gns['summa'] / (100 - $skidka) * 100 - $gns['summa'], 2) : 0;

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


		//сохранение изображений
		$sql = "SELECT * FROM `images` WHERE !`deleted` AND `owner`='".VIEWER_ID."' ORDER BY `sort`";
		$q = query($sql);
		$image_id = 0;
		$image_link = '';
		if(mysql_num_rows($q)) {
			query("UPDATE `images` SET `owner`='zayav".$send['id']."' WHERE !`deleted` AND `owner`='".VIEWER_ID."'");
			$n = 0;
			while($r = mysql_fetch_assoc($q)) {
				$small_name = str_replace(VIEWER_ID.'-', 'zayav'.$send['id'].'-', $r['small_name']);
				$big_name = str_replace(VIEWER_ID.'-', 'zayav'.$send['id'].'-', $r['big_name']);
				rename(PATH.'files/images/'.$r['small_name'], PATH.'files/images/'.$small_name);
				rename(PATH.'files/images/'.$r['big_name'], PATH.'files/images/'.$big_name);
				query("UPDATE `images` SET `small_name`='".$small_name."',`big_name`='".$big_name."' WHERE `id`=".$r['id']);
				if(!$n) {
					$image_id = $r['id'];
					$image_link = $r['path'].$small_name;
				}
				$n++;
			}
			query("UPDATE `gazeta_zayav` SET `image_id`=".$image_id.",`image_link`='".$image_link."' WHERE `id`=".$send['id']);
		}

		clientBalansUpdate($client_id, 'activity');

		//Внесение объявления в список для общего доступа
		if($category == 1) {
			$sql = "SELECT IFNULL(MAX(`general_nomer`),0)
					FROM `gazeta_nomer_pub`
					WHERE `zayav_id`=".$send['id'];
			if($max = query_value($sql)) {
				$sql = "INSERT INTO `vk_ob` (
						`rubric_id`,
						`rubric_sub_id`,
						`txt`,
						`telefon`,

						`country_id`,
						`country_name`,
						`city_id`,
						`city_name`,

						`image_id`,
						`image_link`,

						`day_active`,
						`gazeta_id`
					) VALUES (
						".$rubric_id.",
						".$rubric_sub_id.",
						'".addslashes($txt)."',
						'".addslashes(trim($telefon.($adres ? ' Адрес: '.$adres : '')))."',

						1,
						'Россия',
						3644,
						'Няндома',

						".$image_id.",
						'".addslashes($image_link)."',

						DATE_ADD('"._gn($max, 'day_public')."',INTERVAL 30 DAY),
						".$send['id']."
					)";
				query($sql);
			}
		}

		_historyInsert(
			11,
			array(
				'client_id' => $client_id,
				'zayav_id' => $send['id']
			),
			'gazeta_history'
		);

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

		//Если заявке привязывается клиент, к нему привязываются платежи и история
		if(!$z['client_id'] && $client_id) {
			query("UPDATE `gazeta_money` SET `client_id`=".$client_id." WHERE `zayav_id`=".$zayav_id);
			query("UPDATE `gazeta_history` SET `client_id`=".$client_id." WHERE `zayav_id`=".$zayav_id);
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

		//Обновление главного изображения
		$image_id = 0;
		$image_link = '';
		$sql = "SELECT * FROM `images` WHERE !`deleted` AND `owner`='zayav".$zayav_id."' ORDER BY `sort` LIMIT 1";
		if($i = mysql_fetch_assoc(query($sql))) {
			$image_id = $i['id'];
			$image_link = $i['path'].$i['small_name'];
		}

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

				    `gn_count`=".$pub['count'].",
				    `image_id`=".$image_id.",
				    `image_link`='".$image_link."'
				WHERE `id`=".$zayav_id;
		query($sql);

		clientBalansUpdate($client_id);

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
							'<td>'.round($z['size_x'], 1).'x'.round($z['size_y'], 1).'='.round($z['size_x'] * $z['size_y']).
							'<td>»'.
							'<td>'.round($size_x, 1).'x'.round($size_y, 1).'='.round($size_x * $size_y);
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
			_historyInsert(
				31,
				array(
					'client_id' => $client_id,
					'zayav_id' => $zayav_id,
					'value' => ($changes ? '<table>'.$changes.'</table>' : '').
							   ($gnChanges ? '<table>'.$gnChanges.'</table>' : '')
				),
				'gazeta_history'
			);
		jsonSuccess();
		break;
	case 'zayav_del':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		$zayav_id = intval($_POST['id']);
		$sql = "SELECT * FROM `gazeta_zayav` WHERE !`deleted` AND `id`=".$zayav_id;
		if(!$z = mysql_fetch_assoc(query($sql)))
			jsonError();

		//Проверка на выходившие номера
		$sql = "SELECT COUNT(`id`)
				FROM `gazeta_nomer_pub`
				WHERE `zayav_id`=".$zayav_id."
				  AND `general_nomer`<".GN_FIRST_ACTIVE;
		if(query_value($sql))
			jsonError();

		query("UPDATE `gazeta_zayav`
		       SET `deleted`=1,
				   `summa`=0,
		           `dtime_del`=CURRENT_TIMESTAMP,
			       `viewer_id_del`=".VIEWER_ID."
		       WHERE `id`=".$zayav_id);

		//Удаление номеров выпуска
		query("DELETE FROM `gazeta_nomer_pub` WHERE `zayav_id`=".$zayav_id);

		clientBalansUpdate($z['client_id']);

		_historyInsert(
			61,
			array(
				'client_id' => $z['client_id'],
				'zayav_id' => $zayav_id
			),
			'gazeta_history'
		);
		jsonSuccess();
		break;

	case 'income_spisok':
		$data = income_spisok($_POST);
		if($data['filter']['page'] == 1)
			$send['path'] = utf8(income_path($_POST['day']));
		$send['html'] = utf8($data['spisok']);
		jsonSuccess($send);
		break;
	case 'income_add':
		if(!empty($_POST['client_id']) && !preg_match(REGEXP_NUMERIC, $_POST['client_id']))
			jsonError();
		if(!empty($_POST['zayav_id']) && !preg_match(REGEXP_NUMERIC, $_POST['zayav_id']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['income_id']) || !$_POST['income_id'])
			jsonError();
		if(!preg_match(REGEXP_CENA, $_POST['sum']) || $_POST['sum'] == 0)
			jsonError();

		$send['html'] = utf8(income_insert($_POST));
		if(empty($send))
			jsonError();
		jsonSuccess($send);
		break;
	case 'income_del':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		$id = intval($_POST['id']);

		$sql = "SELECT *
				FROM `gazeta_money`
				WHERE !`deleted`
				  AND `id`=".$id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		$sql = "UPDATE `gazeta_money`
		        SET	`deleted`=1,
					`viewer_id_del`=".VIEWER_ID.",
					`dtime_del`=CURRENT_TIMESTAMP
				WHERE `id`=".$id;
		query($sql);

		clientBalansUpdate($r['client_id']);
		//_zayavBalansUpdate($r['zayav_id']);

		invoice_history_insert(array(
			'action' => 2,
			'table' => 'gazeta_money',
			'id' => $id
		));

		_historyInsert(
			47,
			array(
				'zayav_id' => $r['zayav_id'],
				'client_id' => $r['client_id'],
				'value' => round($r['sum'], 2),
				'value1' => $r['prim'],
				'value2' => $r['income_id']
			),
			'gazeta_history'
		);

		jsonSuccess();
		break;

	case 'expense_spisok':
		$data = expense_spisok($_POST);
		$send['html'] = utf8($data['spisok']);
		$send['mon'] = utf8(expenseMonthSum($_POST));
		jsonSuccess($send);
		break;
	case 'expense_add':
		if(!preg_match(REGEXP_NUMERIC, $_POST['category']))
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['worker']))
			jsonError();
		if(!preg_match(REGEXP_CENA, $_POST['sum']) && $_POST['sum'] ==0)
			jsonError();
		if(empty($_POST['invoice']) || !preg_match(REGEXP_NUMERIC, $_POST['invoice']))
			jsonError();

		$category = intval($_POST['category']);
		$about = win1251(htmlspecialchars(trim($_POST['about'])));
		if(!$category && empty($about))
			jsonError();
		$worker = intval($_POST['worker']);
		$invoice = intval($_POST['invoice']);
		$sum = str_replace(',', '.', $_POST['sum']);
		$sql = "INSERT INTO `gazeta_money` (
					`sum`,
					`prim`,
					`invoice_id`,
					`expense_id`,
					`worker_id`,
					`viewer_id_add`
				) VALUES (
					-".$sum.",
					'".addslashes($about)."',
					".$invoice.",
					".$category.",
					".$worker.",
					".VIEWER_ID."
				)";
		query($sql);

		invoice_history_insert(array(
			'action' => 6,
			'table' => 'gazeta_money',
			'id' => mysql_insert_id()
		));

		_historyInsert(
			81,
			array(
				'value' => abs($sum),
				'value1' => $category,
				'value2' => $about,
				'value3' => $worker ? $worker : ''
			),
			'gazeta_history'
		);
		jsonSuccess();
		break;
	case 'expense_del':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		$id = intval($_POST['id']);

		$sql = "SELECT *
				FROM `gazeta_money`
				WHERE !`deleted`
				  AND `sum`<0
				  AND `id`=".$id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		$sql = "UPDATE `gazeta_money` SET
					`deleted`=1,
					`viewer_id_del`=".VIEWER_ID.",
					`dtime_del`=CURRENT_TIMESTAMP
				WHERE `id`=".$id;
		query($sql);

		invoice_history_insert(array(
			'action' => 7,
			'table' => 'gazeta_money',
			'id' => $id
		));

		_historyInsert(
			82,
			array(
				'value' => round(abs($r['sum']), 2),
				'value1' => $r['expense_id'],
				'value2' => $r['prim'],
				'value3' => $r['worker_id'] ? $r['worker_id'] : ''
			),
			'gazeta_history'
		);

		jsonSuccess();
		break;

	case 'invoice_set':
		if(!VIEWER_ADMIN)
			jsonError();
		if(!preg_match(REGEXP_NUMERIC, $_POST['invoice_id']))
			jsonError();
		if(!preg_match(REGEXP_CENA, $_POST['sum']))
			jsonError();

		$invoice_id = intval($_POST['invoice_id']);
		$sum = str_replace(',', '.', $_POST['sum']);

		$sql = "SELECT * FROM `gazeta_invoice` WHERE `id`=".$invoice_id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		query("UPDATE `gazeta_invoice` SET `start`="._invoiceBalans($invoice_id, $sum)." WHERE `id`=".$invoice_id);
		xcache_unset(CACHE_PREFIX.'invoice');

		invoice_history_insert(array(
			'action' => 5,
			'invoice_id' => $invoice_id
		));

		_historyInsert(
			91,
			array(
				'value' => $sum,
				'value1' => $invoice_id
			),
			'gazeta_history'
		);

		$send['i'] = utf8(invoice_spisok());
		jsonSuccess($send);
		break;
	case 'invoice_history':
		if(empty($_POST['invoice_id']) || !preg_match(REGEXP_NUMERIC, $_POST['invoice_id']))
			jsonError();
		$send['html'] = utf8(invoice_history($_POST));
		jsonSuccess($send);
		break;
	case 'invoice_transfer':
		if(empty($_POST['from']) || !preg_match(REGEXP_NUMERIC, $_POST['from']))
			jsonError();
		if(empty($_POST['to']) || !preg_match(REGEXP_NUMERIC, $_POST['to']))
			jsonError();
		if(!preg_match(REGEXP_CENA, $_POST['sum']) || $_POST['sum'] == 0)
			jsonError();

		$from = intval($_POST['from']);
		$to = intval($_POST['to']);
		$sum = str_replace(',', '.', $_POST['sum']);
		$note = win1251(htmlspecialchars(trim($_POST['note'])));

		if($from == $to)
			jsonError();

		$invoice_from = $from;
		$invoice_to = $to;
		$sql = "INSERT INTO `gazeta_invoice_transfer` (
					`invoice_from`,
					`invoice_to`,
					`worker_from`,
					`worker_to`,
					`sum`,
					`note`,
					`viewer_id_add`
				) VALUES (
					".$invoice_from.",
					".$invoice_to.",
					".($from > 100 ? $from : 0).",
					".($to > 100  ? $to : 0).",
					".$sum.",
					'".addslashes($note)."',
					".VIEWER_ID."
				)";
		query($sql);

		invoice_history_insert(array(
			'action' => 4,
			'table' => 'gazeta_invoice_transfer',
			'id' => mysql_insert_id()
		));

		_historyInsert(
			92,
			array(
				'value' => $sum,
				'value1' => $from,
				'value2' => $to
			),
			'gazeta_history'
		);

		$send['i'] = utf8(invoice_spisok());
		$send['t'] = utf8(transfer_spisok());
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

		_historyInsert(
			1081,
			array('value' => $viewer_id),
			'gazeta_history'
		);

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

		_historyInsert(
			1082,
			array('value' => $viewer_id),
			'gazeta_history'
		);

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

		_historyInsert(
			1034,
			array('value' => $year),
			'gazeta_history'
		);

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

		_historyInsert(
			1031,
			array('value' => $general_nomer),
			'gazeta_history'
		);

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
			_historyInsert(
				1032,
				array(
					'value' => $general_nomer,
					'value1' => '<table>'.$changes.'</table>'
				),
				'gazeta_history'
			);

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

		_historyInsert(
			1033,
			array('value' => $general),
			'gazeta_history'
		);

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

		_historyInsert(
			1011,
			array('value' => $name),
			'gazeta_history'
		);

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
			_historyInsert(
				1012,
				array(
					'value' => $name,
					'value1' => '<table>'.$changes.'</table>'
				),
				'gazeta_history'
			);

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

		_historyInsert(
			1013,
			array('value' => $r['name']),
			'gazeta_history'
		);

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

		_historyInsert(
			1021,
			array('value' => $name),
			'gazeta_history'
		);

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
			_historyInsert(
				1022,
				array(
					'value' => $name,
					'value1' => '<table>'.$changes.'</table>'
				),
				'gazeta_history'
			);

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

		_historyInsert(
			1023,
			array('value' => $r['name']),
			'gazeta_history'
		);

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

		_historyInsert(
			1071,
			array(
				'value' => _rubric($rubric_id),
				'value1' => $name
			),
			'gazeta_history'
		);

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
			_historyInsert(
				1072,
				array(
					'value' => _rubric($r['rubric_id']),
					'value1' => '<table>'.$changes.'</table>'
				),
				'gazeta_history'
			);

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

		_historyInsert(
			1073,
			array(
				'value' => _rubric($r['rubric_id']),
				'value1' => $r['name']
			),
			'gazeta_history'
		);

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

		_historyInsert(1091, array(), 'gazeta_history');

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
			_historyInsert(
				1062,
				array(
					'value' => $r['name'],
					'value1' => '<table>'.$changes.'</table>'
				),
				'gazeta_history'
			);

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

		_historyInsert(
			1041,
			array('value' => $name),
			'gazeta_history'
		);

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
			_historyInsert(
				1042,
				array(
					'value' => $name,
					'value1' => '<table>'.$changes.'</table>'
				),
				'gazeta_history'
			);

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

		_historyInsert(
			1121,
			array('value' => $name),
			'gazeta_history'
		);

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
			_historyInsert(
				1122,
				array(
					'value' => $name,
					'value1' => '<table>'.$changes.'</table>'
				),
				'gazeta_history'
			);

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

		_historyInsert(
			1123,
			array('value' => $r['name']),
			'gazeta_history'
		);

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

		_historyInsert(
			1111,
			array('value' => $name),
			'gazeta_history'
		);

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
			_historyInsert(
				1112,
				array(
					'value' => $name,
					'value1' => '<table>'.$changes.'</table>'
				),
				'gazeta_history'
			);

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

		_historyInsert(
			1113,
			array('value' => $r['name']),
			'gazeta_history'
		);

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

		_historyInsert(
			1051,
			array('value' => $razmer),
			'gazeta_history'
		);

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
			_historyInsert(
				1052,
				array(
					'value' => $razmer,
					'value1' => '<table>'.$changes.'</table>'
				),
				'gazeta_history'
			);

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

		_historyInsert(
			1053,
			array('value' => $razmer),
			'gazeta_history'
		);

		$send['html'] = utf8(setup_skidka_spisok());
		jsonSuccess($send);
		break;

	case 'setup_expense_add':
		if(!preg_match(REGEXP_BOOL, $_POST['show_worker']))
			jsonError();

		$name = win1251(htmlspecialchars(trim($_POST['name'])));
		$show_worker = intval($_POST['show_worker']);

		if(empty($name))
			jsonError();
		$sql = "INSERT INTO `setup_expense` (
					`name`,
					`show_worker`,
					`sort`
				) VALUES (
					'".addslashes($name)."',
					".$show_worker.",
					"._maxSql('setup_expense', 'sort')."
				)";
		query($sql);

		xcache_unset(CACHE_PREFIX.'expense');
		GvaluesCreate();

		_historyInsert(1101, array('value' => $name), 'gazeta_history');

		$send['html'] = utf8(setup_expense_spisok());
		jsonSuccess($send);
		break;
	case 'setup_expense_edit':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		if(!preg_match(REGEXP_BOOL, $_POST['show_worker']))
			jsonError();

		$id = intval($_POST['id']);
		$name = win1251(htmlspecialchars(trim($_POST['name'])));
		$show_worker = intval($_POST['show_worker']);

		if(empty($name))
			jsonError();

		$sql = "SELECT * FROM `setup_expense` WHERE `id`=".$id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		$sql = "UPDATE `setup_expense`
				SET `name`='".addslashes($name)."',
					`show_worker`=".$show_worker."
				WHERE `id`=".$id;
		query($sql);

		xcache_unset(CACHE_PREFIX.'expense');
		GvaluesCreate();

		$changes = '';
		if($r['name'] != $name)
			$changes .= '<tr><th>Наименование:<td>'.$r['name'].'<td>»<td>'.$name;
		if($r['show_worker'] != $show_worker)
			$changes .= '<tr><th>Список сотрудников:<td>'.($r['show_worker'] ? 'да' : 'нет').'<td>»<td>'.($show_worker ? 'да' : 'нет');
		if($changes)
			_historyInsert(
				1102,
				array(
					'value' => $name,
					'value1' => '<table>'.$changes.'</table>'
				),
				'gazeta_history'
			);

		$send['html'] = utf8(setup_expense_spisok());
		jsonSuccess($send);
		break;
	case 'setup_expense_del':
		if(!preg_match(REGEXP_NUMERIC, $_POST['id']))
			jsonError();
		$id = intval($_POST['id']);

		$sql = "SELECT * FROM `setup_expense` WHERE `id`=".$id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		if(query_value("SELECT COUNT(`id`) FROM `gazeta_money` WHERE `expense_id`=".$id))
			jsonError();
		$sql = "DELETE FROM `setup_expense` WHERE `id`=".$id;
		query($sql);

		xcache_unset(CACHE_PREFIX.'expense');
		GvaluesCreate();

		_historyInsert(
			1103,
			array('value' => $r['name']),
			'gazeta_history'
		);

		$send['html'] = utf8(setup_expense_spisok());
		jsonSuccess($send);
		break;
}

jsonError();