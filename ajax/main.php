<?php
require_once('config.php');
require_once(API_PATH.'/ajax/vk.php');


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
		if($data['filter']['page'] == 1)
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
		if(!$rubric_id = _isnum($_POST['rubric_id']))
			jsonError();

		$rubric_sub_id = _isnum($_POST['rubric_sub_id']);
		$txt = win1251(htmlspecialchars(trim($_POST['txt'])));
		$txt = preg_replace('/[ ]+/', ' ', $txt);
		$telefon = win1251(htmlspecialchars(trim($_POST['telefon'])));
		$country_id = _isnum($_POST['country_id']);
		$country_name = win1251(htmlspecialchars(trim($_POST['country_name'])));
		$city_id = _isnum($_POST['city_id']);
		$city_name = win1251(htmlspecialchars(trim($_POST['city_name'])));
		$viewer_id_show = _isbool($_POST['viewer_id_show']);
		$upload_url = trim($_POST['upload_url']);
		$album_id = _isnum($_POST['album_id']);
		$group_id = _isnum($_POST['group_id']);
		$rule = _isnum($_POST['rule']);

		if(empty($txt))
			jsonError();

		ini_set('max_execution_time', 120);

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

		$send['insert_id'] = $insert_id;

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
				rename(APP_PATH.'/files/images/'.$r['small_name'], APP_PATH.'/files/images/'.$small_name);
				rename(APP_PATH.'/files/images/'.$r['big_name'], APP_PATH.'/files/images/'.$big_name);
				query("UPDATE `images` SET `small_name`='".$small_name."',`big_name`='".$big_name."' WHERE `id`=".$r['id']);
				if(!$n) {
					$image_id = $r['id'];
					$image_link = $r['path'].$small_name;
					$image_post_url = $r['path'].$big_name; //изображение для сохранения на стену
				}
				$n++;
			}
			query("UPDATE `vk_ob` SET `image_id`=".$image_id.",`image_link`='".$image_link."' WHERE `id`=".$insert_id);

			//получение изображения для прикрепления к посту на стену
			if(!empty($upload_url)) {
				$img = file_get_contents($image_post_url);
				$name = APP_PATH.'/files/'.VIEWER_ID.time().'.jpg';
				$f = fopen($name, 'w');
				fwrite($f, $img);
				fclose($f);

				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $upload_url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, array('file1'=>'@'.$name));
				$out = json_decode(curl_exec($curl), true);
				curl_close($curl);
				unlink($name);

				$send['server'] = $out['server'];
				$send['photos_list'] = $out['photos_list'];
				$send['hash'] = $out['hash'];
			}
		}

		if($rule)
			_historyInsert(
				$rule,
				array('ob_id' => $insert_id),
				'vk_history'
			);

		jsonSuccess($send);
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

		$sql = "SELECT * FROM `vk_ob` WHERE !`deleted` ".(SA ? '' : "AND `viewer_id_add`=".VIEWER_ID)." AND `id`=".$id;
		if(!$r = mysql_fetch_assoc(query($sql)))
			jsonError();

		$ob = $r;

		if(!$ob['rubric_id'] = _isnum($_POST['rubric_id']))
			jsonError();
		$my = _isbool($_POST['my']);
		$ob['rubric_sub_id'] = _isnum($_POST['rubric_sub_id']);
		$ob['txt'] = win1251(htmlspecialchars(trim($_POST['txt'])));
		$ob['txt'] = preg_replace('/[ ]+/', ' ', $ob['txt']);
		$ob['telefon'] = win1251(htmlspecialchars(trim($_POST['telefon'])));
		$ob['country_id'] = _isnum($_POST['country_id']);
		$ob['country_name'] = win1251(htmlspecialchars(trim($_POST['country_name'])));
		$ob['city_id'] = _isnum($_POST['city_id']);
		$ob['city_name'] = win1251(htmlspecialchars(trim($_POST['city_name'])));
		$ob['viewer_id_show'] = _isbool($_POST['viewer_id_show']);
		$active = _isbool($_POST['active']);

		if($active && strtotime($ob['day_active']) - strtotime(strftime('%Y-%m-%d')) <= 0)
			$ob['day_active'] = strftime('%Y-%m-%d', time() + 86400 * 30);

		if(!$active)
			$ob['day_active'] = '0000-00-00';

		$ob['image_id'] = 0;
		$ob['image_link'] = '';
		$sql = "SELECT * FROM `images` WHERE !`deleted` AND `owner`='ob".$ob['id']."' ORDER BY `sort` LIMIT 1";
		if($i = mysql_fetch_assoc(query($sql))) {
			$ob['image_id'] = $i['id'];
			$ob['image_link'] = $i['path'].$i['small_name'];
		}

		$sql = "UPDATE `vk_ob`
		        SET `rubric_id`=".$ob['rubric_id'].",
					`rubric_sub_id`=".$ob['rubric_sub_id'].",
					`txt`='".addslashes($ob['txt'])."',
					`telefon`='".addslashes($ob['telefon'])."',
					`country_id`=".$ob['country_id'].",
					`country_name`='".addslashes($ob['country_name'])."',
					`city_id`=".$ob['city_id'].",
					`city_name`='".addslashes($ob['city_name'])."',
					`viewer_id_show`=".$ob['viewer_id_show'].",
					`day_active`='".$ob['day_active']."',
					`image_id`=".$ob['image_id'].",
					`image_link`='".$ob['image_link']."'
				WHERE `id`=".$id;
		query($sql);

		$changes = '';
		if($r['rubric_id'] != $ob['rubric_id'])
			$changes .= '<tr><th>Рубрика:<td>'._rubric($r['rubric_id']).'<td>»<td>'._rubric($ob['rubric_id']);
		if($r['rubric_sub_id'] != $ob['rubric_sub_id'])
			$changes .= '<tr><th>Подрубрика:<td>'._rubricsub($r['rubric_sub_id']).'<td>»<td>'._rubricsub($ob['rubric_sub_id']);
		if($r['txt'] != $ob['txt'])
			$changes .= '<tr><th>Текст:<td>'.nl2br($r['txt']).'<td>»<td>'.nl2br($ob['txt']);
		if($r['telefon'] != $ob['telefon'])
			$changes .= '<tr><th>Телефон:<td>'.$r['telefon'].'<td>»<td>'.$ob['telefon'];
		if($r['country_id'] != $ob['country_id'] || $r['city_id'] != $ob['city_id'])
			$changes .= '<tr><th>Регион:'.
							'<td>'.$r['country_name'].''.($r['city_id'] ? ', '.$r['city_name'] : '').
							'<td>»'.
							'<td>'.$ob['country_name'].''.($ob['city_id'] ? ', '.$ob['city_name'] : '');
		if($r['viewer_id_show'] != $ob['viewer_id_show'])
			$changes .= '<tr><th>Показывать имя из VK:<td>'.($r['viewer_id_show'] ? 'да' : 'нет').'<td>»<td>'.($ob['viewer_id_show'] ? 'да' : 'нет');
		if($r['image_id'] != $ob['image_id'])
			$changes .= '<tr><th>Главная фотография:'.
							'<td>'.($r['image_id'] ? '<img src="'.$r['image_link'].'" class="_iview" val="'.$r['image_id'].'" />' : '').
							'<td>»'.
							'<td>'.($ob['image_id'] ? '<img src="'.$ob['image_link'].'" class="_iview" val="'.$ob['image_id'].'" />' : '');
		if($r['day_active'] != $ob['day_active'])
			$changes .= '<tr><th>Активность:'.
							'<td>'.($r['day_active'] == '0000-00-00' || strtotime($r['day_active']) < time() ? 'в архиве' : 'до '.FullData($r['day_active'])).
							'<td>»'.
							'<td>'.($ob['day_active'] == '0000-00-00' ? 'в архиве' : 'до '.FullData($ob['day_active']));
		if($changes)
			_historyInsert(
				10,
				array(
					'ob_id' => $id,
					'value' => '<table>'.$changes.'</table>'
				),
				'vk_history'
			);

		$ob['edited'] = 1;
		$send['html'] = utf8($my ? ob_my_unit($ob) : ob_unit($ob));
		jsonSuccess($send);
		break;
	case 'ob_del':
		if(!$id = _isnum($_POST['id']))
			jsonError();

		$sql = "SELECT * FROM `vk_ob` WHERE !`deleted` AND `viewer_id_add`=".VIEWER_ID." AND `id`=".$id;
		if(!$r = query_assoc($sql))
			jsonError();

		query("UPDATE `vk_ob` SET `deleted`=1 WHERE`id`=".$id);

		_historyInsert(11, array('ob_id'=>$id), 'vk_history');

		jsonSuccess();
		break;
	case 'ob_post':
		if(!$id = _isnum($_POST['id']))
			jsonError();

		$sql = "SELECT * FROM `vk_ob` WHERE !`deleted` AND `id`=".$id;
		if(!$ob = query_assoc($sql))
			jsonError();

		//просмотры
		$view = 0;
		if(!SA && $ob['viewer_id_add'] != VIEWER_ID) {
			$sql = "SELECT COUNT(*)
					FROM `vk_ob_view`
					WHERE `ob_id`=".$id."
					  AND `viewer_id`=".VIEWER_ID."
					  AND `day`='".strftime('%Y-%m-%d')."'";
			if(!$view = query_value($sql)) {
				$sql = "INSERT INTO `vk_ob_view` (
							`ob_id`,
							`viewer_id`,
							`day`
						) VALUES (
							".$id.",
							".VIEWER_ID.",
							CURRENT_TIMESTAMP
						)";
				query($sql);
				$view = 1;
			}
		}

		//изображения
		$img = array();
		$sql = "SELECT * FROM `images` WHERE !`deleted` AND `owner`='ob".$ob['id']."' ORDER BY `sort`";
		$q = query($sql);
		while($r = mysql_fetch_assoc($q))
			$img[] = $r;

		$images = '';
		switch(count($img)) {
			case 1: $images = obImgBuild($img, 1); break;
			case 2: $images = obImgBuild($img, 2); break;
			case 3: $images = obImgBuild($img, 3); break;
			case 4: $images = obImgBuild($img, 4); break;
			case 5:
				$images = obImgBuild($img, 3);
				array_shift($img);
				array_shift($img);
				array_shift($img);
				$images .= obImgBuild($img, 2);
				break;
			case 6:
				$images = obImgBuild($img, 3);
				array_shift($img);
				array_shift($img);
				array_shift($img);
				$images .= obImgBuild($img, 3);
				break;
			case 7:
				$images = obImgBuild($img, 4);
				array_shift($img);
				array_shift($img);
				array_shift($img);
				array_shift($img);
				$images .= obImgBuild($img, 3);
				break;
			case 8:
				$images = obImgBuild($img, 3);
				array_shift($img);
				array_shift($img);
				array_shift($img);
				$images .= obImgBuild($img, 2);
				array_shift($img);
				array_shift($img);
				$images .= obImgBuild($img, 3);
				break;
		}

		$send['o'] = array(
			'id' => $ob['id'],
			'dtime' => utf8(FullDataTime($ob['dtime_add'])),
			'rub' => utf8(_rubric($ob['rubric_id']).($ob['rubric_sub_id'] ? '<em>»</em>'._rubricsub($ob['rubric_sub_id']) : '').':'),
			'txt' => utf8(nl2br($ob['txt'])),
			'view' => $view
		);

		if($ob['viewer_id_show'] && $ob['viewer_id_add'])
			$send['o'] += array(
				'viewer_id' => intval($ob['viewer_id_add']),
				'photo' => _viewer($ob['viewer_id_add'], 'photo'),
				'name' => utf8(_viewer($ob['viewer_id_add'], 'name'))
			);

		if($images)
			$send['o']['images'] = $images;

		if($ob['telefon'])
			$send['o']['tel'] = utf8($ob['telefon']);

		if($ob['city_name'])
			$send['o']['city'] = utf8($ob['country_name'].', '.$ob['city_name']);


		//сообщения
		$msg = '';
		$sql = "SELECT * FROM `vk_ob_msg` WHERE `ob_id`=".$id." AND (!`only_author` OR `only_author` AND `viewer_id_add`=".VIEWER_ID.") ORDER BY `id`";
		$q = query($sql);
		while($r = mysql_fetch_assoc($q))
			$msg .= ob_post_msg_unit($r);
		if($msg)
			$send['o']['msg'] = utf8($msg);

		if(SA)
			$send['o'] += array(
				'sa' => 1,
				'sa_viewer_id' => intval($ob['viewer_id_add']),
				'sa_name' => $ob['viewer_id_add'] ? utf8(_viewer($ob['viewer_id_add'], 'name')) : '',
				'sa_gazeta_id' => intval($ob['gazeta_id'])
			);

		jsonSuccess($send);
		break;
	case 'ob_post_msg':
		if(!$id = _isnum($_POST['id']))
			jsonError();

		$txt = win1251(htmlspecialchars(trim($_POST['txt'])));
		$anon = _isbool($_POST['anon']);
		$only_author = _isbool($_POST['only_author']);

		$sql = "SELECT * FROM `vk_ob` WHERE !`deleted` AND `id`=".$id;
		if(!$ob = query_assoc($sql))
			jsonError();

		$sql = "INSERT INTO `vk_ob_msg` (
					`ob_id`,
					`txt`,
					`anon`,
					`only_author`,
					`viewer_id_add`
				) VALUES (
					".$id.",
					'".addslashes($txt)."',
					".$anon.",
					".$only_author.",
					".VIEWER_ID."
				)";
		query($sql);

		$r = array(
			'id' => mysql_insert_id(),
			'ob_id' => $id,
			'txt' => $txt,
			'anon' => $anon,
			'viewer_id_add' => VIEWER_ID,
			'dtime_add' => curTime()
		);

		$send['msg'] = utf8(ob_post_msg_unit($r));

		jsonSuccess($send);
		break;
	case 'ob_my_spisok':
		$data = ob_my_spisok($_POST);
		if($data['filter']['page'] == 1)
			$send['result'] = utf8($data['result']);
		$send['spisok'] = utf8($data['spisok']);
		jsonSuccess($send);
		break;
}
jsonError();


function obImgBuild($img, $count) {
	$sizes = array(
		1 => 498,
		2 => 246,
		3 => 162,
		4 => 120
	);
	$size = $sizes[$count];
	$h = 0;
	for($k = 0; $k < $count; $k++) {
		$s = _imageResize($img[$k]['big_x'], $img[$k]['big_y'], $size, $count > 1 ? $size + 100 : $size);
		$h += $s['y'];
	}
	$h = floor($h / $count);

	$w = 0;
	for($k = 0; $k < $count; $k++) {
		$s = _imageResize($img[$k]['big_x'], $img[$k]['big_y'], 504, $h);
		$img[$k]['x'] = $s['x'];
		$img[$k]['y'] = $s['y'];
		$w += $s['x'];
	}

	if($w + $count * 6 > 504) {
		$diff = ceil(($w + $count * 6 - 504) / $count);
		$h = 0;
		for($k = 0; $k < $count; $k++) {
			$s = _imageResize($img[$k]['big_x'], $img[$k]['big_y'], $img[$k]['x'] - $diff, $img[$k]['y']);
			$img[$k]['x'] = $s['x'];
			$h += $s['y'];
		}
		$h = floor($h / $count);
	}

	$send = '';
	for($k = 0; $k < $count; $k++) {
		$i = $img[$k];
		$send .= '<img src="'.$i['path'].$i['big_name'].'" width="'.$i['x'].'" height="'.$h.'" class="_iview" val="'.$i['id'].'" />';
	}

	return $send;
}//obImgBuild()
function ob_post_msg_unit($r) {
	return
	'<div class="msg-un">'.
		'<table>'.
			'<tr><td class="ms-photo">'.
					(!$r['anon'] ? _viewer($r['viewer_id_add'], 'photo_link') : '').
				'<td>'.
					(!$r['anon'] ? '<div class="ms-user">'._viewer($r['viewer_id_add'], 'link').'</div>' : '').
					'<div>'.nl2br($r['txt']).'</div>'.
					'<div class="dt">'.FullDataTime($r['dtime_add']).'</div>'.
		'</table>'.
	'</div>';

}//ob_post_msg_unit()
