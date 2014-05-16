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
	define('CURDAY', strftime('%Y-%m-%d'));
	$data = admin_user_spisok();
	return
	'<div class="admin-user">'.
		'<div class="result">'.$data['result'].'</div>'.
		'<table class="tabLR">'.
			'<tr><td class="left">'.$data['spisok'].
				'<td class="right">&nbsp;'.
		'</table>'.
	'</div>';
}//admin_user()
function admin_user_filter($v=array()) {
	return array(
		'page' => !empty($v['page']) && preg_match(REGEXP_NUMERIC, $v['page']) ? intval($v['page']) : 1,
		'limit' => !empty($v['limit']) && preg_match(REGEXP_NUMERIC, $v['limit']) ? intval($v['limit']) : 30,
		'find' => !empty($v['find']) ? win1251(htmlspecialchars(trim($v['find']))) : ''
	);
}//obFilter()
function admin_user_spisok($v=array()) {
	$filter = admin_user_filter($v);

	$limit = $filter['limit'];
	$page = $filter['page'];

	$cond = "`viewer_id`";

	if($filter['find']) {
		$cond .= " AND `txt` LIKE '%".$filter['find']."%'";
		$reg = '/('.$filter['find'].')/i';
	}

	$all = query_value("SELECT COUNT(*) AS `all` FROM `vk_user` WHERE ".$cond);

	if(!$all)
		return array(
			'all' => 0,
			'result' => 'Посетителей не найдено.',
			'spisok' => '<div class="_empty">Посетителей не найдено.</div>',
			'filter' => $filter
		);

	$send['all'] = $all;
	$send['result'] = 'Показано '.$all.' посетител'._end($all, 'ь', 'я', 'ей');
	$send['filter'] = $filter;
	$send['spisok'] = '';

	$start = ($page - 1) * $limit;
	$sql = "SELECT
				*,
				0 AS `ob`
			FROM `vk_user`
			WHERE ".$cond."
			ORDER BY `enter_last` DESC
			LIMIT ".$start.",".$limit;
	$q = query($sql);
	$user = array();
	while($r = mysql_fetch_assoc($q)) {
		/*if($filter['find']) {
			if(preg_match($reg, $r['txt']))
				$r['txt'] = preg_replace($reg, '<em>\\1</em>', $r['txt'], 1);
		}*/
		$user[$r['viewer_id']] = $r;
	}

	$sql = "SELECT
				`viewer_id_add`,
				COUNT(`id`) AS `ob`
			FROM `vk_ob`
			WHERE `viewer_id_add` IN (".implode(',', array_keys($user)).")
			GROUP BY `viewer_id_add`";
	$q = query($sql);
	while($r = mysql_fetch_assoc($q))
		$user[$r['viewer_id_add']]['ob'] = $r['ob'];


	foreach($user as $r)
		$send['spisok'] .= admin_user_unit($r);

	if($start + $limit < $all) {
		$c = $all - $start - $limit;
		$c = $c > $limit ? $limit : $c;
		$send['spisok'] .=
			'<div class="_next" val="'.($page + 1).'">'.
				'<span>Показать ещё '.$c.' посетител'._end($all, 'я', 'ей').'</span>'.
			'</div>';
	}

	return $send;
}//admin_user_spisok()
function admin_user_unit($r) {
	return
	'<div class="un" val="'.$r['viewer_id'].'">'.
		'<table class="tab">'.
			'<tr><td class="img"><a href="'.URL.'&p=admin&id='.$r['viewer_id'].'"><img src="'.$r['photo'].'"></a>'.
				'<td class="inf">'.
					'<div class="dlast">'.
						(substr($r['enter_last'], 0, 10) == CURDAY ? substr($r['enter_last'], 11, 5) : FullDataTime($r['enter_last'])).
					'</div>'.
					'<a href="'.URL.'&p=admin&id='.$r['viewer_id'].'"><b>'.$r['first_name'].' '.$r['last_name'].'</b></a>'.
					'<div class="city">'.$r['city_name'].($r['country_name'] ? ', '.$r['country_name'] : '').'</div>'.
					($r['ob'] ? '<a class="ob">Объявления: <b>'.$r['ob'].'</b></a>' : '').
		'</table>'.
	'</div>';
}//admin_user_unit()