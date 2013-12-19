<?php
function _mainLinks() {
	global $html;
	$links = array(
		array(
			'name' => 'Клиенты',
			'page' => 'client',
			'show' => 1
		),
		array(
			'name' => 'Заявки',
			'page' => 'zayav',
			'show' => 1
		),
		array(
			'name' => 'Отчёты',
			'page' => 'report',
			'show' => 1
		),
		array(
			'name' => 'Установки',
			'page' => 'setup',
			'show' => 1
		)
	);

	$send = '<div id="mainLinks">';
	foreach($links as $l)
		if($l['show']) {
			$sel = $l['page'] == $_GET['d'] ? ' class="sel"' : '';
			$send .= '<a href="'.URL.'&p=gazeta&d='.$l['page'].'"'.$sel.'>'.$l['name'].'</a>';
		}
	$send .= pageHelpIcon().'</div>';

	$html .= $send;
}//_mainLinks()

function _category($id=false) {
	$cat = array(
		1 => 'Объявление',
		2 => 'Реклама',
		3 => 'Поздравление',
		4 => 'Статья'
	);
	return $id ? $cat[$id] : $cat;
}//_category()
function _person($person_id=false) {//Список изделий для заявок
	if(!defined('PERSON_LOADED') || $person_id === false) {
		$key = CACHE_PREFIX.'person';
		$arr = xcache_get($key);
		if(empty($arr)) {
			$sql = "SELECT `id`,`name` FROM `setup_person` ORDER BY `sort`";
			$q = query($sql);
			while($r = mysql_fetch_assoc($q))
				$arr[$r['id']] = $r['name'];
			xcache_set($key, $arr, 86400);
		}
		if(!defined('PERSON_LOADED')) {
			foreach($arr as $id => $name)
				define('PERSON_'.$id, $name);
			define('PERSON_0', '');
			define('PERSON_LOADED', true);
		}
	}
	return $person_id !== false ? constant('PERSON_'.$person_id) : $arr;
}//_person()
function _rubric($rubric_id=false) {//Список изделий для заявок
	if(!defined('RUBRIC_LOADED') || $rubric_id === false) {
		$key = CACHE_PREFIX.'rubric';
		$arr = xcache_get($key);
		if(empty($arr)) {
			$sql = "SELECT `id`,`name` FROM `setup_rubric` ORDER BY `sort`";
			$q = query($sql);
			while($r = mysql_fetch_assoc($q))
				$arr[$r['id']] = $r['name'];
			xcache_set($key, $arr, 86400);
		}
		if(!defined('RUBRIC_LOADED')) {
			foreach($arr as $id => $name)
				define('RUBRIC_'.$id, $name);
			define('RUBRIC_0', '');
			define('RUBRIC_LOADED', true);
		}
	}
	return $rubric_id !== false ? constant('RUBRIC_'.$rubric_id) : $arr;
}//_rubric()
function _rubricsub($item_id=false) {//Список изделий для заявок
	if(!defined('RUBRICSUB_LOADED') || $item_id === false) {
		$key = CACHE_PREFIX.'rubric_sub';
		$arr = xcache_get($key);
		if(empty($arr)) {
			$sql = "SELECT `id`,`name` FROM `setup_rubric_sub` ORDER BY `sort`";
			$q = query($sql);
			while($r = mysql_fetch_assoc($q))
				$arr[$r['id']] = $r['name'];
			xcache_set($key, $arr, 86400);
		}
		if(!defined('RUBRICSUB_LOADED')) {
			foreach($arr as $id => $name)
				define('RUBRICSUB_'.$id, $name);
			define('RUBRICSUB_0', '');
			define('RUBRICSUB_LOADED', true);
		}
	}
	return $item_id !== false ? constant('RUBRICSUB_'.$item_id) : $arr;
}//_rubricsub()

// ---===! client !===--- Секция клиентов

function clientFilter($v=array()) {
	if(empty($v['fast']) || !preg_match(REGEXP_WORDFIND, win1251($v['fast'])))
		$v['fast'] = '';
	if(empty($v['person']) || !preg_match(REGEXP_NUMERIC, $v['person']))
		$v['person'] = 0;
	if(empty($v['skidka']) || !preg_match(REGEXP_NUMERIC, $v['skidka']))
		$v['skidka'] = 0;
	if(empty($v['dolg']) || !preg_match(REGEXP_BOOL, $v['dolg']))
		$v['dolg'] = 0;
	$filter = array(
		'fast' => win1251(htmlspecialchars(trim($v['fast']))),
		'person' => intval($v['person']),
		'skidka' => intval($v['skidka']),
		'dolg' => intval($v['dolg'])
	);
	switch(intval(@$v['order'])) {
		default:
			$filter['order'] = 'dtime_add';
			$filter['sort'] = 'DESC';
			break;
		case 2:
			$filter['order'] = 'activity';
			$filter['sort'] = 'ASC';
			break;
	}

	return $filter;
}//clientFilter()
function client_data($page=1, $filter=array()) {
	$cond = "`deleted`=0";
	$reg = '';
	$regEngRus = '';
	if(!empty($filter['fast'])) {
		$engRus = _engRusChar($filter['fast']);
		$cond .= " AND (`org_name` LIKE '%".$filter['fast']."%'
					 OR `fio` LIKE '%".$filter['fast']."%'
                     OR `telefon` LIKE '%".$filter['fast']."%'
                     OR `adres` LIKE '%".$filter['fast']."%'
                     OR `inn` LIKE '%".$filter['fast']."%'
                     OR `kpp` LIKE '%".$filter['fast']."%'
                     OR `email` LIKE '%".$filter['fast']."%'".
					($engRus ?
					"OR `org_name` LIKE '%".$engRus."%'
                     OR `fio` LIKE '%".$engRus."%'
                     OR `telefon` LIKE '%".$engRus."%'
                     OR `adres` LIKE '%".$engRus."%'
                     OR `inn` LIKE '%".$engRus."%'
                     OR `kpp` LIKE '%".$engRus."%'
                     OR `email` LIKE '%".$engRus."%'"
					: '')."
				 )";
		$reg = '/('.$filter['fast'].')/i';
		if($engRus)
			$regEngRus = '/('.$engRus.')/i';
	} else {
		if(!empty($filter['person']))
			$cond .= " AND `person`=".$filter['person'];
		if(!empty($filter['skidka']))
			$cond .= " AND `skidka`=".$filter['skidka'];
		if(isset($filter['dolg']) && $filter['dolg'] == 1)
			$cond .= " AND `balans`<0";
	}
	$all = query_value("SELECT COUNT(`id`) AS `all` FROM `gazeta_client` WHERE ".$cond." LIMIT 1");
	if($all == 0)
		return array(
			'all' => 0,
			'result' => 'Клиентов не найдено',
			'spisok' => '<div class="_empty">Клиентов не найдено.</div>'
		);

	$send['all'] = $all;
	$dolg = empty($filter['dolg']) ? 0 : abs(query_value("SELECT SUM(`balans`) FROM `gazeta_client` WHERE `deleted`=0 AND `balans`<0 LIMIT 1"));
	$send['result'] =
		'Найден'._end($all, ' ', 'о ').$all.' клиент'._end($all, '', 'а', 'ов').
		($dolg ? '<span class="dolg">(Общая сумма долга = '.$dolg.' руб.)</span>' : '');

	$limit = 20;
	$start = ($page - 1) * $limit;
	$spisok = array();
	$sql = "SELECT *
			FROM `gazeta_client`
			WHERE ".$cond."
			ORDER BY ".$filter['order']." ".$filter['sort']."
			LIMIT ".$start.",".$limit;
	$q = query($sql);
	while($r = mysql_fetch_assoc($q)) {
		$u = array(
			'name_label' => $r['org_name'] ? 'Организация' : 'Фио',
			'name' => $r['org_name'] ? $r['org_name'].($r['fio'] ? '<span class="dop_fio">'.$r['fio'].'</span>' : '') : $r['fio']
		);
		if($r['telefon'])
			$u['telefon'] = $r['telefon'];
		if($r['balans'] != 0)
			$u['balans'] = round($r['balans'], 2);
		if($filter['order'] == 'activity')
			$u['activity'] = $r['activity'] == '0000-00-00' ? 'нет' : FullData($r['activity']);
		if(!empty($filter['fast'])) {
			if(preg_match($reg, $u['name']))
				$u['name'] = preg_replace($reg, '<em>\\1</em>', $u['name'], 1);
			if(preg_match($reg, $r['telefon']))
				$u['telefon'] = preg_replace($reg, '<em>\\1</em>', $r['telefon'], 1);
			if(preg_match($reg, $r['adres']))
				$u['adres'] = preg_replace($reg, '<em>\\1</em>', $r['adres'], 1);
			if(preg_match($reg, $r['inn']))
				$u['inn'] = preg_replace($reg, '<em>\\1</em>', $r['inn'], 1);
			if(preg_match($reg, $r['kpp']))
				$u['kpp'] = preg_replace($reg, '<em>\\1</em>', $r['kpp'], 1);
			if(preg_match($reg, $r['email']))
				$u['email'] = preg_replace($reg, '<em>\\1</em>', $r['email'], 1);

			if($regEngRus) {
				if(preg_match($regEngRus, $u['name']))
					$u['name'] = preg_replace($regEngRus, '<em>\\1</em>', $u['name'], 1);
				if(preg_match($regEngRus, $r['telefon']))
					$u['telefon'] = preg_replace($regEngRus, '<em>\\1</em>', $r['telefon'], 1);
				if(preg_match($regEngRus, $r['adres']))
					$u['adres'] = preg_replace($regEngRus, '<em>\\1</em>', $r['adres'], 1);
				if(preg_match($regEngRus, $r['inn']))
					$u['inn'] = preg_replace($regEngRus, '<em>\\1</em>', $r['inn'], 1);
				if(preg_match($regEngRus, $r['kpp']))
					$u['kpp'] = preg_replace($regEngRus, '<em>\\1</em>', $r['kpp'], 1);
				if(preg_match($regEngRus, $r['email']))
					$u['email'] = preg_replace($regEngRus, '<em>\\1</em>', $r['email'], 1);
			}
		}
		$spisok[$r['id']] = $u;
	}

	$sql = "SELECT
				`client_id` AS `id`,
				COUNT(`id`) AS `count`
			FROM `gazeta_zayav`
			WHERE `deleted`=0
			  AND `client_id` IN (".implode(',', array_keys($spisok)).")
			GROUP BY `client_id`";
	$q = query($sql);
	while($r = mysql_fetch_assoc($q))
		$spisok[$r['id']]['zayav_count'] = $r['count'];

	$send['spisok'] = '';
	foreach($spisok as $id => $r)
		$send['spisok'] .=
		'<div class="unit">'.
			(!empty($r['balans']) ? '<div class="balans">Баланс: <b'.($r['balans'] < 0 ? ' class="minus"' : '').'>'.$r['balans'].'</b></div>' : '').
			'<table>'.
				'<tr><td class="label">'.$r['name_label'].':<td><a href="'.URL.'&p=gazeta&d=client&d1=info&id='.$id.'">'.$r['name'].'</a>'.
				(!empty($r['telefon']) ? '<tr><td class="label">Телефон:<td>'.$r['telefon'] : '').
				(!empty($r['adres']) ? '<tr><td class="label">Адрес:<td>'.$r['adres'] : '').
				(!empty($r['inn']) ? '<tr><td class="label">ИНН:<td>'.$r['inn'] : '').
				(!empty($r['kpp']) ? '<tr><td class="label">КПП:<td>'.$r['kpp'] : '').
				(!empty($r['email']) ? '<tr><td class="label">E-mail:<td>'.$r['email'] : '').
				(!empty($r['zayav_count']) ? '<tr><td class="label">Заявки:<td>'.$r['zayav_count'] : '').
				(!empty($r['activity']) ? '<tr><td class="label">Активность:<td>'.$r['activity'] : '').
			'</table>'.
		'</div>';

	if($start + $limit < $send['all']) {
		$c = $send['all'] - $start - $limit;
		$c = $c > $limit ? $limit : $c;
		$send['spisok'] .=
			'<div class="ajaxNext" val="'.($page + 1).'">'.
				'<span>Показать ещё '.$c.' клиент'._end($c, 'а', 'а', 'ов').'</span>'.
			'</div>';
	}
	return $send;
}//client_data()
function client_list() {
	$data = client_data(1, clientFilter());
	return
	'<div id="client">'.
		'<div id="find"></div>'.
		'<div class="result">'.$data['result'].'</div>'.
			'<table class="tabLR">'.
				'<tr><td class="left">'.$data['spisok'].
					'<td class="right">'.
						'<div id="buttonCreate"><a>Новый клиент</a></div>'.
						'<div class="findHead">Сортировка<div><input type="hidden" id="order" value="1">'.
						'<div class="filter">'.
							'<div class="findHead">Категория<div><input type="hidden" id="person">'.
                            '<div class="findHead">Скидка<div><input type="hidden" id="skidka">'.
							_check('dolg', 'Должники').
						'</div>'.
			'</table>'.
	'</div>';
}//client_list()

function _clientLink($arr, $fio=0) {//Добавление имени и ссылки клиента в массив или возврат по id
	$clientArr = array(is_array($arr) ? 0 : $arr);
	if(is_array($arr)) {
		$ass = array();
		foreach($arr as $r) {
			$clientArr[$r['client_id']] = $r['client_id'];
			if($r['client_id'])
				$ass[$r['client_id']][] = $r['id'];
		}
		unset($clientArr[0]);
	}
	if(!empty($clientArr)) {
		$sql = "SELECT
					`id`,
					`fio`,
					`org_name`,
					`deleted`
		        FROM `gazeta_client`
				WHERE `id` IN (".implode(',', $clientArr).")";
		$q = query($sql);
		if(!is_array($arr)) {
			if($r = mysql_fetch_assoc($q)) {
				$name = $r['org_name'] ? $r['org_name'] : $r['fio'];
				return $fio ? $name : '<a'.($r['deleted'] ? ' class="deleted"' : '').' href="'.URL.'&p=gazeta&d=client&d1=info&id='.$r['id'].'">'.$name.'</a>';
			}
			return '';
		}
		while($r = mysql_fetch_assoc($q)) {
			$name = $r['org_name'] ? $r['org_name'] : $r['fio'];
			foreach($ass[$r['id']] as $id) {
				$arr[$id]['client_link'] = '<a'.($r['deleted'] ? ' class="deleted"' : '').' href="'.URL.'&p=gazeta&d=client&d1=info&id='.$r['id'].'">'.$name.'</a>';
				$arr[$id]['client_fio'] = $name;
			}
		}
	}
	return $arr;
}//_clientLink()
function clientBalansUpdate($client_id) {// установка баланса клиента
	$rashod = query_value("SELECT SUM(`summa`) FROM `gazeta_zayav` WHERE `deleted`=0 AND `client_id`=".$client_id);
	$prihod = query_value("SELECT SUM(`sum`) FROM `gazeta_money` WHERE `deleted`=0 AND `client_id`=".$client_id);
	$balans = $prihod - $rashod;
	$sql = "UPDATE `gazeta_client` SET `balans`=".$balans." WHERE `id`=".$client_id;
	query($sql);
	return $balans;
}
function clientInfoGet($client) {
	$name = $client['person'] == 1 ? $client['fio'] : _person($client['person']).' '.$client['org_name'];
	if(empty($name))
		$name = $client['org_name'];
	return
	'<div class="name">'.$name.'</div>'.
	'<table class="cinf">'.
		($client['person'] != 1 ? '<tr><td class="label">Контактное лицо:<td>'.$client['fio'] : '').
		($client['telefon'] ? '<tr><td class="label">Телефоны:<td>'.$client['telefon'] : '').
		($client['adres'] ?   '<tr><td class="label">Адрес:  <td>'.$client['adres'] : '').
		($client['inn'] ?     '<tr><td class="label">ИНН:    <td>'.$client['inn'] : '').
		($client['kpp'] ?     '<tr><td class="label">КПП:    <td>'.$client['kpp'] : '').
		($client['email'] ?   '<tr><td class="label">E-mail: <td>'.$client['email'] : '').
		($client['skidka'] ?  '<tr><td class="label">Скидка: <td>'.$client['skidka'].'%' : '').
		'<tr><td class="label">Баланс: <td><b class="'.($client['balans'] < 0 ? 'minus' : 'plus').'">'.round($client['balans'], 2).'</b>'.
	'</table>'.
	'<div class="dtime_add">Клиента вн'.(_viewer($client['viewer_id_add'], 'sex') == 1 ? 'есла' : 'ёс').' '
		._viewer($client['viewer_id_add'], 'name').' '.
		FullData($client['dtime_add'], 1).
	'</div>';
}
function client_info($client_id) {
	$sql = "SELECT * FROM `gazeta_client` WHERE `deleted`=0 AND `id`=".$client_id;
	if(!$client = mysql_fetch_assoc(query($sql)))
		return _noauth('Клиента не существует');

	$commCount = query_value("SELECT COUNT(`id`)
							  FROM `vk_comment`
							  WHERE `status`=1
								AND `parent_id`=0
								AND `table_name`='client'
								AND `table_id`=".$client_id);

	$money['all'] = 0;
//	$money = money_spisok(1, array('client_id'=>$client_id,'limit'=>15));

	$histCount = 0;
//	$histCount = query_value("SELECT COUNT(`id`) FROM `history` WHERE `client_id`=".$client_id);

	return
		'<script type="text/javascript">'.
			'var CLIENT={'.
				'id:'.$client_id.','.
				'person:'.$client['person'].','.
				'fio:"'.$client['fio'].'",'.
				'org_name:"'.$client['org_name'].'",'.
				'telefon:"'.$client['telefon'].'",'.
				'adres:"'.$client['adres'].'",'.
				'inn:"'.$client['inn'].'",'.
				'kpp:"'.$client['kpp'].'",'.
				'email:"'.$client['email'].'",'.
				'skidka:"'.$client['skidka'].'"'.
			'};'.
		'</script>'.
		'<div id="clientInfo">'.
			'<table class="tabLR">'.
				'<tr><td class="left">'.clientInfoGet($client).
					'<td class="right">'.
						'<div class="rightLink">'.
							'<a class="sel">Информация</a>'.
							'<a class="cedit">Редактировать</a>'.
							'<a href="'.URL.'&p=gazeta&d=zayav&d1=add&client_id='.$client_id.'"><b>Новая заявка</b></a>'.
							'<a>Внести платёж</a>'.
							'<a class="cdel">Удалить клиента</a>'.
						'</div>'.
			'</table>'.

			'<div id="dopLinks">'.
				'<a class="link sel" val="zayav">Заявки</a>'.
				'<a class="link" val="money">Платежи'.($money['all'] ? ' ('.$money['all'].')' : '').'</a>'.
				'<a class="link" val="comm">Заметки'.($commCount ? ' ('.$commCount.')' : '').'</a>'.
				'<a class="link" val="hist">История'.($histCount ? ' ('.$histCount.')' : '').'</a>'.
			'</div>'.

			'<table class="tabLR">'.
				'<tr><td class="left">'.
						//'<div id="zayav_spisok">'.($zayavSpisok ? $zayavSpisok : '<div class="_empty">Заявок нет</div>').'</div>'.
						//'<div id="money_spisok">'.$money['spisok'].'</div>'.
						'<div id="comments">'._vkComment('client', $client_id).'</div>'.
						//'<div id="histories">'.history_spisok(1, array('client_id'=>$client_id)).'</div>'.
					'<td class="right">'.
			'</table>'.
		'</div>';
}//client_info()


// ---===! zayav !===--- Секция заявок

function gnJson($year=0, $array=false) {//Получение списка номеров для select на указанный год
	if(!$year)
		$year = strftime('%Y', time());
	$sql = "SELECT * FROM `gazeta_nomer` WHERE SUBSTR(`day_public`,1,4)=".$year." ORDER BY general_nomer";
	$q = query($sql);
	$json = array();
	$arr = array();
	while($r = mysql_fetch_assoc($q)) {
		$lost = $r['general_nomer'] < GN_FIRST_ACTIVE ? ' lost' : '';
		$ex = explode('-', $r['day_print']);
		$public = abs($ex[2]).' '._monthCut($ex[1]);
		$json[] =
			'{'.
				'uid:'.$r['general_nomer'].','.
				'title:"'.$r['week_nomer'].' ('.$r['general_nomer'].') выход '.$public.'",'.
				'content:"<div class=\"gn_sel'.$lost.'\">'.
							'<b>'.$r['week_nomer'].'</b>'.
							'('.$r['general_nomer'].')<span> '.
							'выход '.$public.'</span>'.
						 '</div>"'.
			'}';
		$arr[] = array(
			'uid' => $r['general_nomer'],
			'title' => utf8($r['week_nomer'].' ('.$r['general_nomer'].') выход '.$public),
			'content' => utf8('<div class="gn_sel'.$lost.'">'.
								'<b>'.$r['week_nomer'].'</b>'.
								'('.$r['general_nomer'].')<span> '.
								'выход '.$public.'</span>'.
							'</div>')
		);
	}
	return $array ? $arr : implode(',', $json);
}
function zayavFilter($v=array()) {
	if(empty($v['find']))
		$v['find'] = '';
	if(empty($v['cat']) || !preg_match(REGEXP_NUMERIC, $v['cat']))
		$v['cat'] = 0;
	if(empty($v['nopublic']) || !preg_match(REGEXP_BOOL, $v['nopublic']))
		$v['nopublic'] = 0;
	if(empty($v['gnyear']) || !preg_match(REGEXP_YEAR, $v['gnyear']))
		$v['gnyear'] = strftime('%Y', time());
	if(!isset($v['nomer']) || !preg_match(REGEXP_NUMERIC, $v['nomer']))
		$v['nomer'] = GN_FIRST_ACTIVE;
	$filter = array(
		'find' => win1251(htmlspecialchars(trim($v['find']))),
		'cat' => intval($v['cat']),
		'gnyear' => intval($v['gnyear']),
		'nomer' => intval($v['nomer']),
		'nopublic' => intval($v['nopublic'])
	);
	return $filter;
}//zayavFilter()
function zayav_data($page=1, $filter=array(), $limit=20) {
	if(empty($filter))
		$filter = zayavFilter();
	$cond = "`deleted`=0";

	if(!empty($filter['find'])) {
		$find = preg_replace( '/\s+/', '', $filter['find']);
		$cond .= " AND (
			REPLACE(`txt`,' ','') LIKE '%".$find."%' OR
            REPLACE(`telefon`,' ','') LIKE '%".$find."%' OR
            REPLACE(`adres`,' ','') LIKE '%".$find."%' OR
			`txt` LIKE '%".$filter['find']."%' OR
            `telefon` LIKE '%".$filter['find']."%' OR
            `adres` LIKE '%".$filter['find']."%')";
		$reg = '/('.$filter['find'].')/i';
		$regNoSpace = '/('.$find.')/i';
		if($page ==1 && preg_match(REGEXP_NUMERIC, $filter['find']))
			$find_id = intval($filter['find']);
	} else {
		if(!empty($filter['cat']))
			$cond .= " AND `category`=".$filter['cat'];
		if($filter['nopublic'])
			$cond .= " AND `gn_count`=0";
		else {
			if($filter['nomer'])
				$ids = query_ids("SELECT DISTINCT `zayav_id` FROM `gazeta_nomer_pub` WHERE `general_nomer`=".$filter['nomer']);
			else {
				$ids = query_ids("SELECT `general_nomer` FROM `gazeta_nomer` WHERE SUBSTR(`day_public`,1,4)=".$filter['gnyear']);
				$ids = query_ids('SELECT DISTINCT `zayav_id` FROM `gazeta_nomer_pub` WHERE `general_nomer` IN ('.$ids.')');
			}
			$cond .= " AND `id` IN (".$ids.")";
		}
	}
	$all = query_value("SELECT COUNT(`id`) AS `all` FROM `gazeta_zayav` WHERE ".$cond." LIMIT 1");

	$zayav = array();
	if(isset($find_id)) {
		$sql = "SELECT * FROM `gazeta_zayav` WHERE `deleted`=0 AND `id`=".$find_id." LIMIT 1";
		if($r = mysql_fetch_assoc(query($sql))) {
			$all++;
			$limit--;
			$r['find_id'] = 1;
			$zayav[$r['id']] = $r;
		}
	}

	if(!$all)
		return array(
			'all' => 0,
			'result' => 'Заявок не найдено.',
			'spisok' => '<div class="_empty">Заявок не найдено.</div>'
		);

	$send['all'] = $all;
	$send['result'] = 'Показан'._end($all, '', 'о').' '.$all.' заяв'._end($all, 'ка', 'ки', 'ок');

	$start = ($page - 1) * $limit;
	$sql = "SELECT *
			FROM `gazeta_zayav`
			WHERE ".$cond."
			ORDER BY `id` DESC
			LIMIT ".$start.",".$limit;
	$q = query($sql);
	while($r = mysql_fetch_assoc($q)) {
		if(!empty($filter['find'])) {
			if(preg_match($reg, $r['txt']))
				$r['txt'] = preg_replace($reg, '<em>\\1</em>', $r['txt'], 1);

			if(preg_match($reg, $r['telefon']))
				$r['telefon_find'] = preg_replace($reg, '<em>\\1</em>', $r['telefon'], 1);
			elseif(preg_match($regNoSpace, preg_replace( '/\s+/', '', $r['telefon'])))
				$r['telefon_find'] = $r['telefon'];

			if(preg_match($reg, $r['adres']))
				$r['adres_find'] = preg_replace($reg, '<em>\\1</em>', $r['adres'], 1);
			elseif(preg_match($regNoSpace, preg_replace( '/\s+/', '', $r['adres'])))
				$r['adres_find'] = $r['adres'];
		}
		$zayav[$r['id']] = $r;
	}

	$zayav = _clientLink($zayav);

	$send['spisok'] = '';
	foreach($zayav as $id => $r) {
		$send['spisok'] .=
			'<div class="zayav_unit">'.
				'<div class="dtime">'.FullDataTime($r['dtime_add']).'</div>'.
				'<a href="'.URL.'&p=gazeta&d=zayav&d1=info&id='.$id.'" class="name">'._category($r['category']).' №'.(isset($r['find_id']) ? '<em>'.$id.'</em>' : $id).'</a>'.
				'<table class="values">'.
					($r['client_id'] ? '<tr><td class="label">Клиент:<td>'.$r['client_link'] : '').
					($r['category'] == 1 ?
						'<tr><td class="label">Рубрика:<td>'._rubric($r['rubric_id']).
							($r['rubric_sub_id'] ? '<span class="ug">»</span>'._rubricsub($r['rubric_sub_id']) : '').
						'<tr><td class="label top">Текст:<td><div class="txt">'.$r['txt'].'</div>'
					: '').
					($r['category'] == 2 ?
						'<tr><td class="label">Размер:<td>'.
							round($r['size_x'], 1).
							' x '.
							round($r['size_y'], 1).
							' = '.
							'<b>'.round($r['size_x']*$r['size_y']).'</b> см&sup2;'
					: '').
					(isset($r['telefon_find']) ? '<tr><td class="label">Телефон:<td>'.$r['telefon_find'] : '').
					(isset($r['adres_find']) ? '<tr><td class="label">Адрес:<td>'.$r['adres_find'] : '').
					'<tr><td class="label">Стоимость:<td><b>'.round($r['summa'], 2).'</b> руб.'.
						($r['summa_manual'] ? '<span class="manual">(указана вручную)</span>' : '').
				'</table>'.
			'</div>';
	}
	if($start + $limit < $all) {
		$c = $all - $start - $limit;
		$c = $c > $limit ? $limit : $c;
		$send['spisok'] .=
			'<div class="ajaxNext zayav_next" val="'.($page + 1).'">'.
				'<span>Показать ещё '.$c.' заяв'._end($c, 'ку', 'ки', 'ок').'</span>'.
			'</div>';
	}
	return $send;
}//zayav_data()
function zayav_list() {
	$data = zayav_data();
	$cat = array(0 => 'Любая категория') + _category();
	$cat[1] .= '<div class="img_word"></div>';
	return
	'<script type="text/javascript">var GN_SEL=['.gnJson().'];</script>'.
	'<div id="zayav">'.
		'<div class="result">'.$data['result'].'</div>'.
		'<table class="tabLR">'.
			'<tr><td class="left">'.$data['spisok'].
				'<td class="right">'.
					'<div id="buttonCreate"><a HREF="'.URL.'&p=gazeta&d=zayav&d1=add&back=zayav">Новая заявка</a></div>'.
					'<div id="find"></div>'.
					'<div class="filter">'.
						'<div class="findHead">Категория</div>'.
						_rightLink('cat', $cat, 0).
						_check('nopublic', 'Невыходившие заявки').
						'<div class="filter_nomer">'.
							'<div class="findHead">Номер газеты</div>'.
							'<input type="hidden" id="gnyear">'.
							'<input type="hidden" id="nomer" value="'.GN_FIRST_ACTIVE.'">'.
						'</div>'.
					'</div>'.
		'</table>'.
	'</div>';
}//zayav_list()


// ---===! report !===--- Секция отчётов

function report() {
	$def = 'history';
	$pages = array(
		'history' => 'История действий',
		'zayav' => 'Заявки',
		'money' => 'Деньги'
	);

	$d = empty($_GET['d1']) ? $def : $_GET['d1'];

	$links = '';
	foreach($pages as $p => $name)
		$links .= '<a href="'.URL.'&p=gazeta&d=report&d1='.$p.'"'.($d == $p ? ' class="sel"' : '').'>'.$name.'</a>';

	switch(@$_GET['d1']) {
		default:
		case 'history':
			$left = history_spisok();
			break;
		case 'zayav':
			$data = '';
			$left = '';
			break;
		case 'money':
			$data = '';
			$left = '<div class="headName">Список платежей</div>';
			break;
	}
	return
	'<table class="tabLR" id="report">'.
		'<tr><td class="left">'.$left.
		'<td class="right"><div class="rightLink">'.$links.'</div>'.
	'</table>';
}//report()
function history_insert($arr) {
	$sql = "INSERT INTO `gazeta_history` (
			   `type`,
			   `value`,
			   `value1`,
			   `value2`,
			   `client_id`,
			   `zayav_id`,
			   `viewer_id_add`
			) VALUES (
				".$arr['type'].",
				'".(isset($arr['value']) ? $arr['value'] : '')."',
				'".(isset($arr['value1']) ? $arr['value1'] : '')."',
				'".(isset($arr['value2']) ? $arr['value2'] : '')."',
				".(isset($arr['client_id']) ? $arr['client_id'] : 0).",
				".(isset($arr['zayav_id']) ? $arr['zayav_id'] : 0).",
				".VIEWER_ID."
			)";
	query($sql);
}//history_insert()
function history_types($v) {
	switch($v['type']) {
		case 51: return 'Внесение нового клиента '.$v['client_link'].'.';
		case 52: return 'Изменение данных клиента '.$v['client_link'].':<div class="changes">'.$v['value'].'</div>';
		case 53: return 'Удаление клиента '.$v['client_link'].'.';

		case 1011: return 'В установках добавлена новая категория клиентов <u>'.$v['value'].'</u>.';
	    case 1012: return 'В установках изменены данные категории клиентов <u>'.$v['value'].'</u>:<div class="changes">'.$v['value1'].'</div>';
        case 1013: return 'В установках удалена категория клиентов <u>'.$v['value'].'</u>.';

        case 1021: return 'В установках добавлена новая рубрика <u>'.$v['value'].'</u>.';
		case 1022: return 'В установках изменена рубрика <u>'.$v['value'].'</u>:<div class="changes">'.$v['value1'].'</div>';
		case 1023: return 'В установках удалена рубрика <u>'.$v['value'].'</u>.';

		case 1031: return 'В установках добавлен '.$v['value'].'-й номер газеты.';
		case 1032: return 'В установках изменены данные '.$v['value'].'-го номера газеты:<div class="changes">'.$v['value1'].'</div>';
		case 1033: return 'В установках удалён '.$v['value'].'-ый номер газеты.';
		case 1034: return 'В установках создан список номеров газет на '.$v['value'].' год.';

		case 1041: return 'В установках добавлено новое название полосы <u>'.$v['value'].'</u>.';
        case 1042: return 'В установках изменены данные полосы <u>'.$v['value'].'</u>:<div class="changes">'.$v['value1'].'</div>';

		case 1051: return 'В установках добавлена новая скидка <u>'.$v['value'].'%</u>.';
        case 1052: return 'В установках изменены данные скидки <u>'.$v['value'].'%</u>:<div class="changes">'.$v['value1'].'</div>';
        case 1053: return 'В установках удалена скидка <u>'.$v['value'].'%</u>.';

        case 1062: return 'В установках изменена стоимость доп. параметра объявления <u>'.$v['value'].'</u>:<div class="changes">'.$v['value1'].'</div>';

		case 1071: return 'В установках в рубрике <u>'.$v['value'].'</u> добавлена новая подрубрика <u>'.$v['value1'].'</u>.';
		case 1072: return 'В установках в рубрике <u>'.$v['value'].'</u> изменены данные подрубрики:<div class="changes">'.$v['value1'].'</div>';
		case 1073: return 'В установках в рубрике <u>'.$v['value'].'</u> удалена подрубрика <u>'.$v['value1'].'</u>.';

		case 1081: return 'В установках добавлен новый сотрудник '._viewer($v['value'], 'name').'.';
        case 1082: return 'В установках удалён сотрудник '._viewer($v['value'], 'name').'.';

		case 1091: return 'В установках изменена стоимость длины объявлений.';

		case 1101: return 'В установках добавлена новая категория расхода <u>'.$v['value'].'</u>.';
		case 1102: return 'В установках изменены данные категории расхода <u>'.$v['value'].'</u>:<div class="changes">'.$v['value1'].'</div>';
		case 1103: return 'В установках удалена категория расхода <u>'.$v['value'].'</u>.';

		case 1111: return 'В установках добавлен новый вид платежа <u>'.$v['value'].'</u>.';
		case 1112: return 'В установках изменён вид платежа <u>'.$v['value'].'</u>:<div class="changes">'.$v['value1'].'</div>';
		case 1113: return 'В установках удалён вид платежа <u>'.$v['value'].'</u>.';

		default: return $v['type'];
	}
}//history_types()
function history_spisok($page=1, $filter=array()) {
	$limit = 30;
	$cond = "`id`".
		(isset($filter['client_id']) ? ' AND `client_id`='.$filter['client_id'] : '').
		(isset($filter['zayav_id']) ? ' AND `zayav_id`='.$filter['zayav_id'] : '');
	$sql = "SELECT COUNT(`id`) AS `all`
			FROM `gazeta_history`
			WHERE ".$cond."
			LIMIT 1";
	$all = query_value($sql);
	if(!$all)
		return 'Истории по указанным условиям нет.';
	$start = ($page - 1) * $limit;

	$sql = "SELECT *
			FROM `gazeta_history`
			WHERE ".$cond."
			ORDER BY `id` DESC
			LIMIT ".$start.",".$limit;
	$q = query($sql);
	$history = array();
	while($r = mysql_fetch_assoc($q))
		$history[$r['id']] = $r;
	$history = _viewer($history);
	$history = _clientLink($history);

	$send = '';
	$txt = '';
	end($history);
	$keyEnd = key($history);
	reset($history);
	foreach($history as $r) {
		if(!$txt) {
			$time = strtotime($r['dtime_add']);
			$viewer_id = $r['viewer_id_add'];
		}
		$txt .= '<div class="txt">'.history_types($r).'</div>';
		$key = key($history);
		if(!$key ||
		   $key == $keyEnd ||
		   $time - strtotime($history[$key]['dtime_add']) > 900 ||
		   $viewer_id != $history[$key]['viewer_id_add']) {
			$send .=
				'<div class="history_unit">'.
					'<div class="head">'.FullDataTime($r['dtime_add']).$r['viewer_link'].'</div>'.
					$txt.
				'</div>';
			$txt = '';
		}
		next($history);
	}
	if($start + $limit < $all)
		$send .= '<div class="ajaxNext" id="history_next" val="'.($page + 1).'"><span>Показать более ранние записи...</span></div>';
	return $send;
}//history_spisok()


// ---===! setup !===--- Секция настроек

function setup() {
	$pageDef = 'worker';
	$pages = array(
		'worker' => 'Сотрудники',
		'gn' => 'Номера выпусков газеты',
		'person' => 'Категории клиентов',
		'rubric' => 'Рубрики объявлений',
		'oblen' => 'Стоимость длины объявления',
		'obdop' => 'Доп. параметры объявления',
		'polosa' => 'Стоимость см&sup2; рекламы',
		'money' => 'Виды платежей',
		'skidka' => 'Скидки',
		'rashod' => 'Категории расходов'
	);

	if(!GAZETA_ADMIN)
		unset($pages['worker']);

	$d = empty($_GET['d1']) ? $pageDef : $_GET['d1'];
	if(empty($_GET['d1']) && !empty($pages) && empty($pages[$d])) {
		foreach($pages as $p => $name) {
			$d = $p;
			break;
		}
	}

	switch($d) {
		default: $d = $pageDef;
		case 'worker':  $left = setup_worker(); break;
		case 'gn': $left = setup_gn(); break;
		case 'person': $left = setup_person(); break;
		case 'rubric':
			if(preg_match(REGEXP_NUMERIC, @$_GET['id'])) {
				$left = setup_rubric_sub(intval($_GET['id']));
				break;
			}
			$left = setup_rubric();
			break;
		case 'oblen': $left = setup_oblen(); break;
		case 'obdop': $left = setup_obdop(); break;
		case 'polosa': $left = setup_polosa(); break;
		case 'money': $left = setup_money(); break;
		case 'skidka': $left = setup_skidka(); break;
		case 'rashod': $left = setup_rashod(); break;
	}
	$links = '';
	if($pages)
		foreach($pages as $p => $name)
			$links .= '<a href="'.URL.'&p=gazeta&d=setup&d1='.$p.'"'.($d == $p ? ' class="sel"' : '').'>'.$name.'</a>';
	return
	'<div id="setup">'.
		'<table class="tabLR">'.
			'<tr><td class="left">'.$left.
				'<td class="right"><div class="rightLink">'.$links.'</div>'.
		'</table>'.
	'</div>';
}//setup()

function setup_worker() {
	return
	'<div id="setup_worker">'.
		'<div class="headName">Управление сотрудниками<a class="add">Новый сотрудник</a></div>'.
		'<div id="spisok">'.setup_worker_spisok().'</div>'.
	'</div>';
}//setup_worker()
function setup_worker_spisok() {
	$sql = "SELECT `viewer_id`,
				   CONCAT(`first_name`,' ',`last_name`) AS `name`,
				   `photo`,
				   `gazeta_admin`
			FROM `vk_user`
			WHERE `gazeta_worker`=1
			  AND `viewer_id`!=982006
			ORDER BY `dtime_add`";
	$q = query($sql);
	$send = '';
	while($r = mysql_fetch_assoc($q)) {
		$send .=
		'<table class="unit" val="'.$r['viewer_id'].'">'.
			'<tr><td class="photo"><img src="'.$r['photo'].'">'.
				'<td>'.($r['gazeta_admin'] ? '' : '<div class="img_del"></div>').
					'<a class="name">'.$r['name'].'</a>'.
//					($r['admin'] ? '' : '<a href="'.URL.'&p=setup&d=worker&id='.$r['viewer_id'].'" class="rules_set">Настроить права</a>').
			'</table>';
	}
	return $send;
}//setup_worker_spisok()

function setup_gn() {
	define('CURRENT_YEAR', strftime('%Y', time()));
	return
	'<script type="text/javascript">var GN_MAX="'.query_value("SELECT MAX(`general_nomer`)+1 FROM `gazeta_nomer`").'";</script>'.
	'<div id="setup_gn">'.
		'<div class="headName">Номера выпусков газеты<a class="add">Новый номер</a></div>'.
		'<div id="dopLinks">'.setup_gn_year().'</div>'.
		'<div id="spisok">'.setup_gn_spisok().'</div>'.
	'</div>';
}//setup_gn()
function setup_gn_year($y=CURRENT_YEAR) {
	$sql = "SELECT
            	SUBSTR(MIN(`day_public`),1,4) AS `begin`,
            	SUBSTR(MAX(`day_public`),1,4) AS `end`,
            	MAX(`general_nomer`) AS `max`
            FROM `gazeta_nomer`
            LIMIT 1";
	$r = mysql_fetch_assoc(query($sql));
	if(!$r['begin'])
		$r = array(
			'begin' => CURRENT_YEAR,
			'end' => CURRENT_YEAR
		);
	$send = '';
	for($n = $r['begin']; $n <= $r['end'] + 1; $n++)
		$send .= '<a class="link'.($n == $y ? ' sel' : '').'">'.$n.'</a>';
	return $send;
}//setup_gn_year()
function setup_gn_spisok($y=CURRENT_YEAR, $gnedit=0) {
	$sql = "SELECT
				`gn`.*,
				COUNT(`pub`.`id`) AS `count`
			FROM `gazeta_nomer` AS `gn`
				LEFT JOIN `gazeta_nomer_pub` AS `pub`
				ON `gn`.`general_nomer`=`pub`.`general_nomer`
			WHERE `day_public` LIKE '".$y."-%'
			GROUP BY `general_nomer`
			ORDER BY `general_nomer`";
	$q = query($sql);
	if(!mysql_num_rows($q))
		return 'Номера газет, которые будут выходить в '.$y.' году, не определены.'.
			'<div class="vkButton"><button>Создать список</button></div>';
	$send =
		'<table class="_spisok">'.
			'<tr><th>Номер<br />выпуска'.
				'<th>День отправки<br />в печать'.
				'<th>День выхода'.
				'<th>Заявки'.
				'<th>';
	$cur = time() - 86400;
	while($r = mysql_fetch_assoc($q)) {
		$grey = $cur > strtotime($r['day_print']) ? 'grey' : '';
		$edit = $gnedit == $r['general_nomer'] ? ' edit' : '';
		$class = $grey || $edit ? ' class="'.$grey.$edit.'"' : '';
		$send .= '<tr'.$class.'>'.
			'<td class="nomer"><b>'.$r['week_nomer'].'</b> (<span>'.$r['general_nomer'].'</span>)'.
			'<td class="print">'.FullData($r['day_print'], 0, 1, 1).'<s>'.$r['day_print'].'</s>'.
			'<td class="pub">'.FullData($r['day_public'], 0, 1, 1).'<s>'.$r['day_public'].'</s>'.
			'<td class="z">'.($r['count'] ? $r['count'] : '').
			'<td><div class="img_edit"></div><div class="img_del"></div>';
	}
	$send .= '</table>';
	return $send;
}//setup_gn_spisok()

function setup_person() {
	return
	'<div id="setup_person">'.
		'<div class="headName">Категории клиентов<a class="add">Новая категория</a></div>'.
		'<div id="spisok">'.setup_person_spisok().'</div>'.
	'</div>';
}//setup_person()
function setup_person_spisok() {
	$sql = "SELECT `p`.`id`,
				   `p`.`name`,
				   COUNT(`c`.`id`) AS `count`
			FROM `setup_person` AS `p`
			  LEFT JOIN `gazeta_client` AS `c`
			  ON `p`.`id`=`c`.`person` AND `c`.`deleted`=0
			GROUP BY `p`.`id`
			ORDER BY `p`.`sort`";
	$q = query($sql);
	if(!mysql_num_rows($q))
		return 'Список пуст.';

	$send =
		'<table class="_spisok">'.
			'<tr><th class="name">Наименование'.
				'<th class="cl">Кол-во<br />клиентов'.
				'<th class="set">'.
		'</table>'.
		'<dl class="_sort" val="setup_person">';
	while($r = mysql_fetch_assoc($q))
		$send .='<dd val="'.$r['id'].'">'.
			'<table class="_spisok">'.
				'<tr><td class="name">'.$r['name'].
					'<td class="cl">'.($r['count'] ? $r['count'] : '').
					'<td class="set">'.
						'<div class="img_edit"></div>'.
						(!$r['count'] ? '<div class="img_del"></div>' : '').
			'</table>';
	$send .= '</dl>';
	return $send;
}//setup_person_spisok()

function setup_rubric() {
	return
	'<div id="setup_rubric">'.
		'<div class="headName">Рубрики объявлений<a class="add">Новая рубрика</a></div>'.
		'<div id="spisok">'.setup_rubric_spisok().'</div>'.
	'</div>';
}//setup_rubric()
function setup_rubric_spisok() {
	$sql = "SELECT `r`.`id`,
				   `r`.`name`,
				   COUNT(`rs`.`id`) AS `sub`
			FROM `setup_rubric` AS `r`
			  LEFT JOIN `setup_rubric_sub` AS `rs`
			  ON `r`.`id`=`rs`.`rubric_id`
			GROUP BY `r`.`id`
			ORDER BY `r`.`sort`";
	$q = query($sql);
	if(!mysql_num_rows($q))
		return 'Список пуст.';

	$rubric = array();
	while($r = mysql_fetch_assoc($q))
		$rubric[$r['id']] = $r;

	$sql = "SELECT `r`.`id`,
				   COUNT(`z`.`id`) AS `ob`
			FROM `setup_rubric` AS `r`,
			  	 `gazeta_zayav` AS `z`
			WHERE `r`.`id`=`z`.`rubric_id`
			GROUP BY `r`.`id`";
	$q = query($sql);
	while($r = mysql_fetch_assoc($q))
		$rubric[$r['id']]['ob'] = $r['ob'];

	$send =
		'<table class="_spisok">'.
			'<tr><th class="name">Наименование'.
				'<th class="sub">Подрубрики'.
				'<th class="ob">Кол-во<br />объявлений'.
				'<th class="set">'.
		'</table>'.
		'<dl class="_sort" val="setup_rubric">';
	foreach($rubric as $id => $r)
		$send .='<dd val="'.$id.'">'.
			'<table class="_spisok">'.
				'<tr><td class="name"><a href="'.URL.'&p=gazeta&d=setup&d1=rubric&id='.$id.'">'.$r['name'].'</a>'.
					'<td class="sub">'.($r['sub'] ? $r['sub'] : '').
					'<td class="ob">'.(isset($r['ob']) ? $r['ob'] : '').
					'<td class="set">'.
						'<div class="img_edit"></div>'.
						(!$r['sub'] && empty($r['ob']) ? '<div class="img_del"></div>' : '').
			'</table>';

	$send .= '</dl>';
	return $send;
}//setup_rubric_spisok()

function setup_rubric_sub($id) {
	$sql = "SELECT * FROM `setup_rubric` WHERE `id`=".$id." LIMIT 1";
	if(!$rub = mysql_fetch_assoc(query($sql)))
		return 'Рубрики id = '.$id.' не существует. <a href="'.URL.'&p=gazeta&d=setup&d1=rubric">Назад</a>';
	return
	'<script type="text/javascript">var RUBRIC_ID='.$id.';</script>'.
	'<a href="'.URL.'&p=gazeta&d=setup&d1=rubric"><< назад к списку рубрик</a>'.
	'<div id="setup_rubric_sub">'.
		'<div class="headName">Список подрубрик для "'.$rub['name'].'"<a class="add">Новая подрубрика</a></div>'.
		'<div id="spisok">'.setup_rubric_sub_spisok($id).'</div>'.
	'</div>';
}//setup_rubric_sub()
function setup_rubric_sub_spisok($rubric_id) {
	$sql = "SELECT `rs`.`id`,
				   `rs`.`name`,
				   COUNT(`z`.`id`) AS `count`
			FROM `setup_rubric_sub` AS `rs`
			  LEFT JOIN `gazeta_zayav` AS `z`
			  ON `rs`.`id`=`z`.`rubric_sub_id`
			WHERE `rs`.`rubric_id`=".$rubric_id."
			GROUP BY `rs`.`id`
			ORDER BY `rs`.`sort`";
	$q = query($sql);
	if(!mysql_num_rows($q))
		return 'Список пуст.';

	$send =
		'<table class="_spisok">'.
			'<tr><th class="name">Наименование'.
				'<th class="ob">Кол-во<br />объявлений'.
				'<th class="set">'.
		'</table>'.
		'<dl class="_sort" val="setup_rubric_sub">';
	while($r = mysql_fetch_assoc($q))
		$send .='<dd val="'.$r['id'].'">'.
			'<table class="_spisok">'.
				'<tr><td class="name">'.$r['name'].
					'<td class="ob">'.($r['count'] ? $r['count'] : '').
					'<td class="set">'.
						'<div class="img_edit"></div>'.
						(!$r['count'] ? '<div class="img_del"></div>' : '').
			'</table>';

	$send .= '</dl>';
	return $send;
}//setup_rubric_sub_spisok()

function setup_oblen() {
	$sql = "SELECT * FROM `setup_global` LIMIT 1";
	$r = mysql_fetch_assoc(query($sql));
	return
	'<div id="setup_oblen">'.
		'<div class="headName">Настройка стоимости длины объявления</div>'.
		'<table>'.
            '<tr><td>Первые'.
				'<td><input type="text" maxlength="3" value="'.$r['txt_len_first'].'" id="txt_len_first" />'.
				'<td>символов:'.
                '<td><input type="text" maxlength="3" value="'.$r['txt_cena_first'].'" id="txt_cena_first" /> руб.'.
            '<tr><td>Последующие'.
				'<td><input type="text" maxlength="3" value="'.$r['txt_len_next'].'" id="txt_len_next" />'.
				'<td>символов:'.
                '<td><input type="text" maxlength="3" value="'.$r['txt_cena_next'].'" id="txt_cena_next" /> руб.'.
        '</table>'.
		'<div class="vkButton"><button>Сохранить</button></div>'.
	'</div>';
}//setup_oblen()

function setup_obdop() {
	return
	'<div id="setup_obdop">'.
		'<div class="headName">Дополнительные параметры объявлений</div>'.
		'<div id="spisok">'.setup_obdop_spisok().'</div>'.
	'</div>';
}//setup_obdop()
function setup_obdop_spisok() {
	$sql = "SELECT * FROM `setup_ob_dop` ORDER BY `id`";
	$q = query($sql);
	if(!mysql_num_rows($q))
		return 'Список пуст.';

	$send =
		'<table class="_spisok">'.
			'<tr><th>Наименование'.
				'<th>Стоимость<br />руб.'.
				'<th>';
	while($r = mysql_fetch_assoc($q))
		$send .= '<tr val="'.$r['id'].'">'.
			'<td class="name">'.$r['name'].
			'<td class="cena">'.round($r['cena'], 2).
			'<td><div class="img_edit"></div>';
	$send .= '</table>';
	return $send;
}//setup_obdop_spisok()

function setup_polosa() {
	return
	'<div id="setup_polosa">'.
		'<div class="headName">Стоимость см&sup2; рекламы для каждой полосы<a class="add">Новая полоса</a></div>'.
		'<div id="spisok">'.setup_polosa_spisok().'</div>'.
	'</div>';
}//setup_polosa()
function setup_polosa_spisok() {
	$sql = "SELECT * FROM `setup_polosa_cost` ORDER BY `sort`";
	$q = query($sql);
	if(!mysql_num_rows($q))
		return 'Список пуст.';

	$send =
		'<table class="_spisok">'.
			'<tr><th class="name">Полоса'.
				'<th class="cena">Цена за см&sup2;<br />руб.'.
				'<th class="set">'.
		'</table>'.
		'<dl class="_sort" val="setup_polosa_cost">';
	while($r = mysql_fetch_assoc($q))
		$send .='<dd val="'.$r['id'].'">'.
			'<table class="_spisok">'.
				'<tr><td class="name">'.$r['name'].
					'<td class="cena">'.round($r['cena'], 2).
					'<td class="set"><div class="img_edit"></div>'.
			'</table>';
	$send .= '</dl>';
	return $send;
}//setup_polosa_spisok()

function setup_money() {
	return
	'<div id="setup_money">'.
		'<div class="headName">Виды платежей<a class="add">Добавить</a></div>'.
		'<div id="spisok">'.setup_money_spisok().'</div>'.
	'</div>';
}//setup_money()
function setup_money_spisok() {
	$sql = "SELECT `s`.`id`,
				   `s`.`name`,
				   COUNT(`g`.`id`) AS `count`
			FROM `setup_money_type` AS `s`
			  LEFT JOIN `gazeta_money` AS `g`
			  ON `s`.`id`=`g`.`type`
			GROUP BY `s`.`id`
			ORDER BY `s`.`sort`";
	$q = query($sql);
	if(!mysql_num_rows($q))
		return 'Список пуст.';

	$send =
		'<table class="_spisok">'.
		'<tr><th class="name">Наименование'.
		'<th class="opl">Кол-во<br />платежей'.
		'<th class="set">'.
		'</table>'.
		'<dl class="_sort" val="setup_money_type">';
	while($r = mysql_fetch_assoc($q))
		$send .='<dd val="'.$r['id'].'">'.
			'<table class="_spisok">'.
				'<tr><td class="name">'.$r['name'].
					'<td class="opl">'.($r['count'] ? $r['count'] : '').
					'<td class="set">'.
						'<div class="img_edit"></div>'.
						(!$r['count'] ? '<div class="img_del"></div>' : '').
			'</table>';

	$send .= '</dl>';
	return $send;
}//setup_money_spisok()

function setup_skidka() {
	return
	'<div id="setup_skidka">'.
		'<div class="headName">Настройка скидок<a class="add">Добавить</a></div>'.
		'<div id="spisok">'.setup_skidka_spisok().'</div>'.
	'</div>';
}//setup_skidka()
function setup_skidka_spisok() {
	$sql = "SELECT `s`.*,
				   COUNT(`c`.`id`) AS `count`
			FROM `setup_skidka` AS `s`
			  LEFT JOIN `gazeta_client` AS `c`
			  ON `s`.`razmer`=`c`.`skidka` AND `c`.`deleted`=0
			GROUP BY `s`.`razmer`
			ORDER BY `s`.`razmer`";
	$q = query($sql);
	if(!mysql_num_rows($q))
		return 'Список пуст.';

	$send = '<table class="_spisok">'.
		'<tr><th>Размер скидки'.
			'<th>Описание'.
			'<th>Клиенты'.
			'<th>';
	while($r = mysql_fetch_assoc($q))
		$send .=
		'<tr><td class="razmer"><b>'.$r['razmer'].'</b>%'.
			'<td class="about">'.$r['about'].
			'<td class="cl">'.($r['count'] ? $r['count'] : '').
			'<td><div class="img_edit"></div>'.
				 (!$r['count'] ? '<div class="img_del"></div>' : '');
	$send .= '</table>';
	return $send;
}//setup_skidka_spisok()

function setup_rashod() {
	return
	'<div id="setup_rashod">'.
		'<div class="headName">Категории расходов<a class="add">Добавить</a></div>'.
		'<div id="spisok">'.setup_rashod_spisok().'</div>'.
	'</div>';
}//setup_rashod()
function setup_rashod_spisok() {
	$sql = "SELECT `r`.*,
				   COUNT(`m`.`id`) AS `count`
			FROM `setup_rashod_category` AS `r`
			  LEFT JOIN `gazeta_money` AS `m`
			  ON `r`.`id`=`m`.`rashod_category` AND `m`.`deleted`=0
			GROUP BY `r`.`id`
			ORDER BY `r`.`sort`";
	$q = query($sql);
	if(!mysql_num_rows($q))
		return 'Список пуст.';

	$send =
	'<table class="_spisok">'.
		'<tr><th class="name">Наименование'.
			'<th class="opl">Кол-во<br />платежей'.
			'<th class="set">'.
	'</table>'.
	'<dl class="_sort" val="setup_rashod_category">';
	while($r = mysql_fetch_assoc($q))
		$send .='<dd val="'.$r['id'].'">'.
			'<table class="_spisok">'.
				'<tr><td class="name">'.$r['name'].
					'<td class="opl">'.($r['count'] ? $r['count'] : '').
					'<td class="set">'.
						'<div class="img_edit"></div>'.
						(!$r['count'] ? '<div class="img_del"></div>' : '').
			'</table>';
	$send .= '</dl>';
	return $send;
}//setup_rashod_spisok()
