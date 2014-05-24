<?php
function adminMainLinks() {
	$links = array(
		array(
			'name' => 'Посетители',
			'page' => 'user',
			'show' => 1
		),
		array(
			'name' => 'Поисковые запросы',
			'page' => 'query',
			'show' => 1
		),
		array(
			'name' => 'История действий',
			'page' => 'history',
			'show' => 1
		),
		array(
			'name' => 'Выход',
			'page' => 'exit',
			'show' => 1
		)
	);

	$send = '<div id="mainLinks">';
	foreach($links as $l)
		if($l['show']) {
			$sel = $l['page'] == $_GET['d'] ? ' class="sel"' : '';
			$send .= '<a href="'.URL.'&p=admin&d='.$l['page'].'"'.$sel.'>'.$l['name'].'</a>';
		}
	$send .= '</div>';

	return $send;
}//adminMainLinks()

function admin_user() {
	$data = admin_user_spisok();
	$ob_count = query_value("SELECT COUNT(DISTINCT `viewer_id_add`) FROM `vk_ob` WHERE !`gazeta_id` AND `viewer_id_add`");
	$ob_act = query_value("SELECT COUNT(DISTINCT `viewer_id_add`)
						   FROM `vk_ob`
						   WHERE !`gazeta_id`
						     AND `viewer_id_add`
						     AND !`deleted`
						     AND `day_active`>=DATE_FORMAT(NOW(),'%Y-%m-%d')");

	$app_user = query_value("SELECT COUNT(*) FROM `vk_user` WHERE `is_app_user`");
	$menu_left = query_value("SELECT COUNT(*) FROM `vk_user` WHERE `rule_menu_left`");
	$notify = query_value("SELECT COUNT(*) FROM `vk_user` WHERE `rule_notify`");

	$ob_write = array(
		0 => 'Все',
		1 => 'Размещали<span>'.$ob_count.'</span>',
		2 => 'Есть активные<span>'.$ob_act.'</span>'
	);
	$rules = array(
		0 => 'Все',
		1 => 'Приложение установлено<span>'.$app_user.'</span>',
		2 => 'Добавлено в левое меню<span>'.$menu_left.'</span>',
		3 => 'Разрешены уведомления<span>'.$notify.'</span>'
	);
	return
	'<div class="admin-user">'.
		'<div class="result">'.$data['result'].'</div>'.
		'<table class="tabLR">'.
			'<tr><td class="left">'.$data['spisok'].
				'<td class="right">'.
					'<div id="find"></div>'.
					'<div class="findHead">Объявления</div>'.
					_radio('ob_write', $ob_write, 0, 1).
					'<div class="findHead">Права</div>'.
					_radio('rules', $rules, 0, 1).
		'</table>'.
	'</div>';
}//admin_user()
function admin_user_filter($v=array()) {
	return array(
		'page' => !empty($v['page']) && preg_match(REGEXP_NUMERIC, $v['page']) ? intval($v['page']) : 1,
		'limit' => !empty($v['limit']) && preg_match(REGEXP_NUMERIC, $v['limit']) ? intval($v['limit']) : 30,
		'find' => !empty($v['find']) ? win1251(htmlspecialchars(trim($v['find']))) : '',
		'ob_write' => !empty($v['ob_write']) && preg_match(REGEXP_NUMERIC, $v['ob_write']) ? intval($v['ob_write']) : 0,
		'rules' => !empty($v['rules']) && preg_match(REGEXP_NUMERIC, $v['rules']) ? intval($v['rules']) : 0
	);
}//obFilter()
function admin_user_spisok($v=array()) {
	$filter = admin_user_filter($v);

	$limit = $filter['limit'];
	$page = $filter['page'];

	$cond = "`u`.`viewer_id`";

	$obCount = 0;

	if($filter['find']) {
		if(_isnum($filter['find']))
			$cond .= " AND `u`.`viewer_id`=".$filter['find'];
		else {
			$cond .= " AND CONCAT(`u`.`first_name`,' ',`u`.`last_name`) LIKE '%".$filter['find']."%'";
			$reg = '/('.$filter['find'].')/i';
		}
	} else {
		switch($filter['ob_write']) {
			case 1:
				$cond .= " AND `ob`.`viewer_id_add` AND `ob`.`viewer_id_add`=`u`.`viewer_id`";
				$obCount = query_value("SELECT COUNT(*) FROM `vk_ob` WHERE !`gazeta_id` AND `viewer_id_add`");
				break;
			case 2:
				$cond .= " AND `ob`.`viewer_id_add`
				           AND `ob`.`viewer_id_add`=`u`.`viewer_id`
				           AND !`ob`.`deleted`
						   AND `ob`.`day_active`>=DATE_FORMAT(NOW(), '%Y-%m-%d')";
				$obCount = query_value("SELECT COUNT(*)
									   FROM `vk_ob`
									   WHERE !`gazeta_id`
									     AND `viewer_id_add`
									     AND !`deleted`
									     AND `day_active`>=DATE_FORMAT(NOW(),'%Y-%m-%d')");
				break;
		}
		switch($filter['rules']) {
			case 1: $cond .= " AND `u`.`is_app_user`"; break;
			case 2: $cond .= " AND `u`.`rule_menu_left`"; break;
			case 3: $cond .= " AND `u`.`rule_notify`"; break;
		}
	}

	$sql = "SELECT `u`.`viewer_id`
			FROM `vk_user` `u`
				".($filter['ob_write'] ? ",`vk_ob` `ob`" : '')."
			WHERE ".$cond."
			GROUP BY `u`.`viewer_id`";
	$q = query($sql);
	$all = mysql_num_rows($q);

	if(!$all)
		return array(
			'all' => 0,
			'result' => 'Посетителей не найдено.',
			'spisok' => '<div class="_empty">Посетителей не найдено.</div>',
			'filter' => $filter
		);

	$send['all'] = $all;
	$send['result'] = 'Показан'._end($all, '', 'о').' '.$all.' посетител'._end($all, 'ь', 'я', 'ей').
					  ($obCount ? '<span>('.$obCount.' объявлени'._end($obCount, 'е', 'я', 'й').')</span>' : '');
	$send['filter'] = $filter;
	$send['spisok'] = '';

	$start = ($page - 1) * $limit;
	$sql = "SELECT
				`u`.*,
				CONCAT(`u`.`first_name`,' ',`u`.`last_name`) AS `name`,
				0 AS `ob`,
				0 AS `act`,
				0 AS `arc`,
				0 AS `del`
			FROM `vk_user` `u`
				".($filter['ob_write'] ? ",`vk_ob` `ob`" : '')."
			WHERE ".$cond."
			GROUP BY `u`.`viewer_id`
			ORDER BY ".($filter['ob_write'] ? "COUNT(`ob`.`id`)" : '`u`.`enter_last`')." DESC
			LIMIT ".$start.",".$limit;
	$q = query($sql);
	$user = array();
	while($r = mysql_fetch_assoc($q)) {
		if($filter['find'] && !_isnum($filter['find'])) {
			if(preg_match($reg, $r['name']))
				$r['name'] = preg_replace($reg, '<em>\\1</em>', $r['name'], 1);
		}
		$user[$r['viewer_id']] = $r;
	}

	define('CURDAY', strftime('%Y-%m-%d'));

	//все объявления
	$sql = "SELECT
				`viewer_id_add`,
				COUNT(`id`) AS `c`
			FROM `vk_ob`
			WHERE `viewer_id_add` IN (".implode(',', array_keys($user)).")
			GROUP BY `viewer_id_add`";
	$q = query($sql);
	while($r = mysql_fetch_assoc($q))
		$user[$r['viewer_id_add']]['ob'] = $r['c'];

	//активные объявления
	$sql = "SELECT
				`viewer_id_add`,
				COUNT(`id`) AS `c`
			FROM `vk_ob`
			WHERE `viewer_id_add` IN (".implode(',', array_keys($user)).")
			  AND !`deleted`
			  AND `day_active`>=DATE_FORMAT(NOW(), '%Y-%m-%d')
			GROUP BY `viewer_id_add`";
	$q = query($sql);
	while($r = mysql_fetch_assoc($q))
		$user[$r['viewer_id_add']]['act'] = $r['c'];

	//архивные объявления
	$sql = "SELECT
				`viewer_id_add`,
				COUNT(`id`) AS `c`
			FROM `vk_ob`
			WHERE `viewer_id_add` IN (".implode(',', array_keys($user)).")
			  AND !`deleted`
			  AND `day_active`<DATE_FORMAT(NOW(), '%Y-%m-%d')
			GROUP BY `viewer_id_add`";
	$q = query($sql);
	while($r = mysql_fetch_assoc($q))
		$user[$r['viewer_id_add']]['arc'] = $r['c'];

	//удалённые объявления
	$sql = "SELECT
				`viewer_id_add`,
				COUNT(`id`) AS `c`
			FROM `vk_ob`
			WHERE `viewer_id_add` IN (".implode(',', array_keys($user)).")
			  AND `deleted`
			GROUP BY `viewer_id_add`";
	$q = query($sql);
	while($r = mysql_fetch_assoc($q))
		$user[$r['viewer_id_add']]['del'] = $r['c'];

	foreach($user as $r)
		$send['spisok'] .= admin_user_unit($r);

	if($start + $limit < $all) {
		$c = $all - $start - $limit;
		$c = $c > $limit ? $limit : $c;
		$send['spisok'] .=
			'<div class="_next" val="'.($page + 1).'">'.
				'<span>Показать ещё '.$c.' посетител'._end($c, 'я', 'я', 'ей').'</span>'.
			'</div>';
	}

	return $send;
}//admin_user_spisok()
function admin_user_unit($r) {
	return
	'<div class="user-unit" val="'.$r['viewer_id'].'">'.
		'<table class="tab">'.
			'<tr><td class="img"><a href="'.URL.'&p=admin&d=user&id='.$r['viewer_id'].'"><img src="'.$r['photo'].'"></a>'.
				'<td class="inf">'.
					'<div class="dlast">'.
						(substr($r['enter_last'], 0, 10) == CURDAY ?
							($r['count_day'] > 1 ? '<span class="cday">'.$r['count_day'].'x</span>' : '').'<span class="today">'.substr($r['enter_last'], 11, 5).'</span>' :
							FullDataTime($r['enter_last'])
						).
						(substr($r['dtime_add'], 0, 10) == CURDAY ? '<br /><span class="ob new">Новый</span>' : '').
					'</div>'.
					'<a href="'.URL.'&p=admin&id='.$r['viewer_id'].'"><b>'.$r['name'].'</b></a>'.
					'<div class="city">'.$r['country_name'].($r['city_name'] ? ', '.$r['city_name'] : '').'</div>'.
					($r['ob'] ? '<a class="ob">Объявления: <b>'.$r['ob'].'</b></a>' : '').
					($r['act'] ? '<span class="ob act">'.$r['act'].'</span>' : '').
					($r['arc'] ? '<span class="ob arc">'.$r['arc'].'</span>' : '').
					($r['del'] ? '<span class="ob del">'.$r['del'].'</span>' : '').
		'</table>'.
	'</div>';
}//admin_user_unit()

function admin_user_info($viewer_id) {
	if(!$r = query_assoc("SELECT * FROM `vk_user` WHERE `viewer_id`=".$viewer_id))
		return 'Пользователь не внесён в базу';

	//все объявления
	$ob = query_value("SELECT COUNT(`id`) FROM `vk_ob` WHERE `viewer_id_add`=".$viewer_id);
	$act = query_value("SELECT COUNT(`id`)
						FROM `vk_ob`
						WHERE !`deleted`
						  AND `day_active`>=DATE_FORMAT(NOW(), '%Y-%m-%d')
						  AND`viewer_id_add`=".$viewer_id);
	$arc = query_value("SELECT COUNT(`id`)
						FROM `vk_ob`
						WHERE !`deleted`
						  AND `day_active`<DATE_FORMAT(NOW(), '%Y-%m-%d')
						  AND`viewer_id_add`=".$viewer_id);
	$del = query_value("SELECT COUNT(`id`) FROM `vk_ob` WHERE `deleted` AND `viewer_id_add`=".$viewer_id);

	define('CURDAY', strftime('%Y-%m-%d'));

	$menu = array(
		0 => 'Информация',
		1 => 'Объявления',
		2 => 'История действий'
	);

	return
	'<div id="user-info">'.
		'<table class="tabLR">'.
			'<tr><td class="left user-unit">'.

					'<table class="tab">'.
							'<tr><td class="img">'.
									'<img src="'.$r['photo'].'">'.
									'<div class="id">'.$viewer_id.'</div>'.
								'<td class="inf">'.
									'<div class="dlast">'.
										(substr($r['enter_last'], 0, 10) == CURDAY ? '<span class="today">'.substr($r['enter_last'], 11, 5).'</span>' : FullDataTime($r['enter_last'])).
										(substr($r['dtime_add'], 0, 10) == CURDAY ? '<br /><span class="ob new">Новый</span>' : '').
									'</div>'.
									'<a href="http://vk.com/id'.$r['viewer_id'].'" target="_blank"><b>'.$r['first_name'].' '.$r['last_name'].'</b></a>'.
									'<div class="city">'.$r['country_name'].($r['city_name'] ? ', '.$r['city_name'] : '').'</div>'.
									($ob ? '<a class="ob">Объявления: <b>'.$ob.'</b></a>' : '').
									($act ? '<span class="ob act">'.$act.'</span>' : '').
									($arc ? '<span class="ob arc">'.$arc.'</span>' : '').
									($del ? '<span class="ob del">'.$del.'</span>' : '').
						'</table>'.

						'<table class="itab">'.
							'<tr><td class="label r">Регистрация:<td>'.FullDataTime($r['dtime_add']).
							'<tr><td class="label r">Приложение<td> '.($r['is_app_user'] ? '' : 'не ').'установлено'.
							'<tr><td class="label r">В левое меню<td>'.($r['rule_menu_left'] ? '' : 'не ').'добавлено'.
							'<tr><td class="label r">Уведомления<td>'.($r['rule_notify'] ? '' : 'не ').'разрешены'.
						'</table>'.
						'<div class="vkButton update" val="'.$viewer_id.'"><button>Обновить данные</button></div>'.
					'<td class="right">'.
						_rightLink('menu', $menu).
		'</table>'.

	'</div>';

}//admin_user_info()
