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
			'name' => 'Настройки',
			'page' => 'setup',
			'show' => 1
		),
		array(
			'name' => 'Выход',
			'page' => 'ob',
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
function _gn($nomer=false, $i='') {//Получение информации о всех номерах газеты из кеша
	if(!defined('GN_LOADED') || $nomer === false) {
		$key = CACHE_PREFIX.'gn';
		$send = xcache_get($key);
		if(empty($send)) {
			$send = array();
			$sql = "SELECT * FROM `gazeta_nomer`";
			$q = query($sql);
			while($r = mysql_fetch_assoc($q))
				$send[$r['general_nomer']] = array(
					'week' => $r['week_nomer'],
					'day_print' => $r['day_print'],
					'day_public' => $r['day_public'],
					'pub' => FullData($r['day_public'], 1, 1, 1),
					'pc' => $r['polosa_count']
				);
			xcache_set($key, $send, 86400);
		}
		if(!defined('GN_LOADED')) {
			foreach($send as $n => $r) {
				define('GN_WEEK_'.$n, $r['week']);
				define('GN_DAY_PRINT_'.$n, $r['day_print']);
				define('GN_DAY_PUBLIC_'.$n, $r['day_public']);
				define('GN_PUB_'.$n, $r['pub']);
				define('GN_PC_'.$n, $r['pc']);
			}
			define('GN_LOADED', true);
		}
	}
	if($nomer !== false)
		switch($i) {
			case 'week': return constant('GN_WEEK_'.$nomer);    //номер недели
			case 'day_print': return constant('GN_DAY_PRINT_'.$nomer);
			case 'day_public': return constant('GN_DAY_PUBLIC_'.$nomer);
			case 'pub': return constant('GN_PUB_'.$nomer);
			case 'pc': return constant('GN_PC_'.$nomer);        //количество полос
		}
	return $send;
}//_gn()
function _obDop($item_id=false) {//Дополнительные параметры для объявлений
	if(!defined('OBDOP_LOADED') || $item_id === false) {
		$key = CACHE_PREFIX.'obdop';
		$arr = xcache_get($key);
		if(empty($arr)) {
			$sql = "SELECT * FROM `setup_ob_dop`";
			$q = query($sql);
			while($r = mysql_fetch_assoc($q))
				$arr[$r['id']] = $r['name'];
			xcache_set($key, $arr, 86400);
		}
		if(!defined('OBDOP_LOADED')) {
			foreach($arr as $id => $name)
				define('OBDOP_'.$id, $name);
			define('OBDOP_0', '');
			define('OBDOP_LOADED', true);
		}
	}
	return $item_id !== false ? constant('OBDOP_'.$item_id) : $arr;
}//_obDop()
function _polosa($item_id=false) {//Название полосы для рекламы
	if(!defined('POLOSA_LOADED') || $item_id === false) {
		$key = CACHE_PREFIX.'polosa';
		$arr = xcache_get($key);
		if(empty($arr)) {
			$sql = "SELECT * FROM `setup_polosa_cost`";
			$q = query($sql);
			while($r = mysql_fetch_assoc($q))
				$arr[$r['id']] = $r['name'];
			xcache_set($key, $arr, 86400);
		}
		if(!defined('POLOSA_LOADED')) {
			foreach($arr as $id => $name)
				define('POLOSA_'.$id, $name);
			define('POLOSA_0', '');
			define('POLOSA_LOADED', true);
		}
	}
	return $item_id !== false ? constant('POLOSA_'.$item_id) : $arr;
}//_polosa()
function _invoice($type_id=false, $i='name') {//Список изделий для заявок
	if(!defined('INVOICE_LOADED') || $type_id === false) {
		$key = CACHE_PREFIX.'invoice';
		$arr = xcache_get($key);
		if(empty($arr)) {
			$sql = "SELECT * FROM `gazeta_invoice` ORDER BY `id`";
			$q = query($sql);
			while($r = mysql_fetch_assoc($q)) {
				$r['start'] = round($r['start'], 2);
				$arr[$r['id']] = $r;
			}
			xcache_set($key, $arr, 86400);
		}
		if(!defined('INVOICE_LOADED')) {
			foreach($arr as $id => $r) {
				define('INVOICE_'.$id, $r['name']);
				define('INVOICE_START_'.$id, $r['start']);
			}
			define('INVOICE_0', '');
			define('INVOICE_START_0', 0);
			define('INVOICE_LOADED', true);
		}
	}
	if($type_id === false)
		return $arr;
	if($i == 'start')
		return constant('INVOICE_START_'.$type_id);
	return constant('INVOICE_'.$type_id);
}//_invoice()
function _expense($type_id=false, $i='name') {//Список изделий для заявок
	if(!defined('EXPENSE_LOADED') || $type_id === false) {
		$key = CACHE_PREFIX.'expense';
		$arr = xcache_get($key);
		if(empty($arr)) {
			$sql = "SELECT * FROM `setup_expense` ORDER BY `sort`";
			$q = query($sql);
			while($r = mysql_fetch_assoc($q))
				$arr[$r['id']] = array(
					'name' => $r['name'],
					'worker' => $r['show_worker']
				);
			xcache_set($key, $arr, 86400);
		}
		if(!defined('EXPENSE_LOADED')) {
			foreach($arr as $id => $r) {
				define('EXPENSE_'.$id, $r['name']);
				define('EXPENSE_WORKER_'.$id, $r['worker']);
			}
			define('EXPENSE_0', '');
			define('EXPENSE_WORKER_0', 0);
			define('EXPENSE_LOADED', true);
		}
	}
	if($type_id === false)
		return $arr;
	if($i == 'worker')
		return constant('EXPENSE_WORKER_'.$type_id);
	return constant('EXPENSE_'.$type_id);
}//_expense()

function viewerAdded($viewer_id) {//Вывод сотрудника, который вносил запись с учётом пола
	return 'вн'.(_viewer($viewer_id, 'sex') == 1 ? 'есла' : 'ёс').' '._viewer($viewer_id, 'name');
}

// ---===! client !===--- Секция клиентов

function clientFilter($v=array()) {
	$filter = array(
		'page' => _isnum(@$v['page']) ? $v['page'] : 1,
		'limit' => _isnum(@$v['limit']) ? $v['limit'] : 20,
		'fast' => win1251(htmlspecialchars(trim(@$v['fast']))),
		'person' => _isnum(@$v['person']),
		'skidka' => _isnum(@$v['skidka']),
		'dolg' => _isbool(@$v['dolg']),
		'gnyear' => _isnum(@$v['gnyear']),
		'nomer' => _isnum(@$v['nomer'])
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
function client_data($v=array()) {
	$filter = clientFilter($v);

	$cond = "!`deleted`";
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
		if($filter['person'])
			$cond .= " AND `person`=".$filter['person'];
		if($filter['skidka'])
			$cond .= " AND `skidka`=".$filter['skidka'];
		if($filter['dolg'])
			$cond .= " AND `balans`<0";
		if($filter['gnyear']) {
			if($filter['nomer']) {
				$sql = "SELECT `zayav_id` FROM `gazeta_nomer_pub` WHERE `general_nomer`=".$filter['nomer'];
				$ids = query_ids($sql);
				$sql = "SELECT `client_id` FROM `gazeta_zayav` WHERE `id` IN (".$ids.")";
				$ids = query_ids($sql);
				$cond .= " AND `id` IN (".$ids.")";
			}
		}

	}
	$all = query_value("SELECT COUNT(`id`) AS `all` FROM `gazeta_client` WHERE ".$cond." LIMIT 1");
	if($all == 0)
		return array(
			'all' => 0,
			'result' => 'Клиентов не найдено',
			'spisok' => '<div class="_empty">Клиентов не найдено.</div>',
			'filter' => $filter
		);

	$send['all'] = $all;
	$send['filter'] = $filter;
	$dolg = empty($filter['dolg']) ? 0 : abs(query_value("SELECT SUM(`balans`) FROM `gazeta_client` WHERE ".$cond." LIMIT 1"));
	$send['result'] =
		'Найден'._end($all, ' ', 'о ').$all.' клиент'._end($all, '', 'а', 'ов').
		($dolg ? '<span class="dolg">(Общая сумма долга = '.$dolg.' руб.)</span>' : '');

	$page = $filter['page'];
	$limit = $filter['limit'];
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
			WHERE !`deleted`
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
			'<table class="cltab">'.
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
			'<div class="_next" val="'.($page + 1).'">'.
				'<span>Показать ещё '.$c.' клиент'._end($c, 'а', 'а', 'ов').'</span>'.
			'</div>';
	}
	return $send;
}//client_data()
function client_list() {
	$data = client_data();

	$sql = "SELECT `day_print` FROM `gazeta_nomer` ORDER BY `day_public` LIMIT 1";
	$years = array();
	for($y = substr(query_value($sql), 0, 4); $y <= strftime('%Y'); $y++)
		$years[$y] = $y;

	return
	'<script type="text/javascript">'.
		'var GN_YEAR='._selJson($years).';'.
	'</script>'.
	'<div id="client">'.
		'<div id="find"></div>'.
		'<div class="result">'.$data['result'].'</div>'.
			'<table class="tabLR">'.
				'<tr><td class="left">'.$data['spisok'].
					'<td class="right">'.
						'<div id="buttonCreate"><a>Новый клиент</a></div>'.
						'<div class="findHead">Сортировка</div>'.
						'<input type="hidden" id="order" value="1" />'.
						'<div class="filter">'.
							'<div class="findHead">Категория</div>'.
							'<input type="hidden" id="person" />'.
                            '<div class="findHead">Скидка</div>'.
							'<input type="hidden" id="skidka" />'.
							_check('dolg', 'Должники').
							'<div class="findHead">Номер газеты</div>'.
							'<input type="hidden" id="gnyear" />'.
							'<input type="hidden" id="nomer" />'.
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
function clientBalansUpdate($client_id, $ret='balans') {// установка баланса клиента
	$expense = query_value("SELECT SUM(`summa`) FROM `gazeta_zayav` WHERE !`deleted` AND `client_id`=".$client_id);
	$prihod = query_value("SELECT SUM(`sum`) FROM `gazeta_money` WHERE !`deleted` AND `client_id`=".$client_id);
	$balans = $prihod - $expense;

	//Определение последней даты активности клиента по выходу заявок
	$sql = "SELECT MAX(`gn`.`day_public`)
			FROM `gazeta_zayav` `z`
				LEFT JOIN `gazeta_nomer_pub` `pub`
				ON `z`.`id`=`pub`.`zayav_id`
				LEFT JOIN `gazeta_nomer` `gn`
				ON `gn`.`general_nomer`=`pub`.`general_nomer`
			WHERE `z`.`client_id`=".$client_id."
			GROUP BY `z`.`id`
			ORDER BY `gn`.`day_public` DESC
			LIMIT 1";
	$activity = query_value($sql);

	$sql = "UPDATE `gazeta_client`
			SET `balans`=".$balans.",
				`activity`='".($activity ? $activity : '0000-00-00')."'
			WHERE `id`=".$client_id;
	query($sql);
	if($ret == 'activity')
		return $activity;
	return $balans;
}//clientBalansUpdate()
function clientInfoGet($client) {
	$name = $client['person'] == 1 ? $client['fio'] : _person($client['person']).' '.$client['org_name'];
	if(empty($name))
		$name = $client['org_name'];
	return
	($client['deleted'] ? '<div class="_info">Клиент удалён</div>' : '').
	'<div class="cname">'.$name.'</div>'.
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
	'<div class="dtime_add">'.
		'Клиента вн'.(_viewer($client['viewer_id_add'], 'sex') == 1 ? 'есла' : 'ёс').' '.
		_viewer($client['viewer_id_add'], 'name').' '.
		FullData($client['dtime_add'], 1).
	($client['deleted'] ?
		'<br />Удалил'.(_viewer($client['viewer_id_del'], 'sex') == 1 ? 'а' : '').' '.
		_viewer($client['viewer_id_del'], 'name').' '.
		FullData($client['dtime_del'], 1)
	: '').
	'</div>';
}//clientInfoGet()
function client_info($client_id) {
	$sql = "SELECT * FROM `gazeta_client` WHERE `id`=".$client_id;
	if(!$client = mysql_fetch_assoc(query($sql)))
		return _noauth('Клиента не существует');

	if(!VIEWER_ADMIN && $client['deleted'])
		return _noauth('Клиент удалён');

	$commCount = query_value("SELECT COUNT(`id`)
							  FROM `vk_comment`
							  WHERE `status`=1
								AND `parent_id`=0
								AND `table_name`='client'
								AND `table_id`=".$client_id);

	$zayav = zayav_data(array('client_id'=>$client_id));
	$income = income_spisok(array('client_id'=>$client_id));
	$history = gazeta_history(array('client_id'=>$client_id,'limit'=>15));

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
	  (!$client['deleted'] ? '<a class="cedit">Редактировать</a>'.
							'<a href="'.URL.'&p=gazeta&d=zayav&d1=add&client_id='.$client_id.'"><b>Новая заявка</b></a>'.
							//'<a>Внести платёж</a>'.
							'<a class="'.($zayav['all'] ? 'off' : 'cdel').'">Удалить клиента</a>'
	  : '').
						'</div>'.
			'</table>'.

			'<div id="dopLinks">'.
				'<a class="link sel" val="zayav">Заявки'.($zayav['all'] ? ' ('.$zayav['all'].')' : '').'</a>'.
				'<a class="link" val="inc">Платежи'.($income['all'] ? ' ('.$income['all'].')' : '').'</a>'.
				'<a class="link" val="note">Заметки'.($commCount ? ' ('.$commCount.')' : '').'</a>'.
				'<a class="link" val="hist">История'.($history['all'] ? ' ('.$history['all'].')' : '').'</a>'.
			'</div>'.

			'<table class="tabLR">'.
				'<tr><td class="left">'.
						'<div id="zayav_spisok">'.$zayav['spisok'].'</div>'.
						'<div id="income_spisok">'.$income['spisok'].'</div>'.
						'<div id="notes">'._vkComment('client', $client_id).'</div>'.
						'<div id="histories">'.$history['spisok'].'</div>'.
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
		$lost = $r['general_nomer'] < GN_FIRST_ACTIVE ? ' class=lost' : '';
		$ex = explode('-', $r['day_print']);
		$public = abs($ex[2]).' '._monthCut($ex[1]);
		$json[] =
			'{'.
				'uid:'.$r['general_nomer'].','.
				'title:"'.$r['week_nomer'].' ('.$r['general_nomer'].') выход '.$public.'",'.
				'content:"<div'.$lost.'>'.
							'<b>'.$r['week_nomer'].'</b>'.
							'('.$r['general_nomer'].')'.
							'<span> выход '.$public.'</span>'.
						 '</div>"'.
			'}';
		$arr[] = array(
			'uid' => $r['general_nomer'],
			'title' => utf8($r['week_nomer'].' ('.$r['general_nomer'].') выход '.$public),
			'content' => utf8('<div'.$lost.'>'.
								'<b>'.$r['week_nomer'].'</b>'.
								'('.$r['general_nomer'].')<span> '.
								'выход '.$public.'</span>'.
							'</div>')
		);
	}
	return $array ? $arr : implode(',', $json);
}
function _zayavLink($arr) {//Добавление в массив информации о заявках
	$ids = array(); // идешники заявок
	$arrIds = array();
	foreach($arr as $r)
		if($r['zayav_id']) {
			$ids[$r['zayav_id']] = 1;
			$arrIds[$r['zayav_id']][] = $r['id'];
		}
	if(empty($ids))
		return $arr;
	$sql = "SELECT * FROM `gazeta_zayav` WHERE `id` IN (".implode(',', array_keys($ids)).")";
	$q = query($sql);
	while($r = mysql_fetch_assoc($q))
		foreach($arrIds[$r['id']] as $id) {
			$arr[$id]['zayav_link'] = '<a '.($r['deleted'] ? ' class="deleted" title="Заявка удалена"' : '').
											'href="'.URL.'&p=gazeta&d=zayav&d1=info&id='.$r['id'].'">'.
										'№'.$r['id'].
									  '</a>';
			$arr[$id]['zayav_type'] = _category($r['category']);
		}
	return $arr;
}//_zayavLink()

function gns_control($post, $category, $zayav_id='{zayav_id}') {//проверка правильности заполнения номеров при внесении и редактировании заявки
	$send['summa'] = 0;
	$send['count'] = 0;
	$send['array'] = array();
	if(empty($post) && preg_match(REGEXP_NUMERIC, $zayav_id))
		return $send;
	$gns = array();
	foreach(explode(',', $post) as $r) {
		$ex = explode(':', $r);
		if(!preg_match(REGEXP_NUMERIC, $ex[0]))
			return false;
		if(!isset($ex[1]) || !preg_match('/^[0-9]{1,10}(.[0-9]{1,6})?(,[0-9]{1,6})?$/i', $ex[0]))
			return false;
		if(!isset($ex[2]) || !preg_match(REGEXP_NUMERIC, $ex[2]))
			return false;
		if(!isset($ex[3]) || !preg_match(REGEXP_NUMERIC, $ex[3]))
			return false;
		if($category == 2 && !$ex[2])
			return false;
		$cena = round(str_replace(',', '.', $ex[1]), 6);
		$send['summa'] += $cena;
		$send['count']++;
		$gns[] = '('.$zayav_id.','.intval($ex[0]).','.intval($ex[2]).','.$cena.','.intval($ex[3]).')';
		$send['array'][$ex[0]] = array(
			'cena' => round($cena, 2),
			'dop' => $ex[2],
			'polosa' => $ex[3]
		);
	}
	$send['summa'] = round($send['summa'], 2);
	$send['insert'] = implode(',', $gns);
	return $send;
}
function zayav_add() {
	query("UPDATE `images` SET `deleted`=1 WHERE `owner`='".VIEWER_ID."'");
	$client_id = 0;
	$skidka = 0;
	if(!empty($_GET['client_id']) && $id = _isnum($_GET['client_id']))
		if($r = query_assoc("SELECT * FROM `gazeta_client` WHERE !`deleted` AND `id`=".$id)) {
			$client_id = $id;
			$skidka = $r['skidka'];
		}
	return
	'<script type="text/javascript">'.
		'var ZA={'.
			'back:"'.($client_id ? 'client&d1=info&id='.$client_id : 'zayav').'",'.
			'skidka:'.$skidka.
		'};'.
	'</script>'.
	'<div id="zayav-add">'.
		'<div class="headName">Внесение новой заявки</div>'.
		'<table class="zatab">'.
            '<tr><td class="label">Клиент:<td><input type="hidden" id="client_id" value="'.$client_id.'" />'.
            '<tr><td class="label"><b>Категория:</b><td><input type="hidden" id="category" value="1" />'.
        '</table>'.
		'<table class="zatab ob">'.
            '<tr><td class="label">Рубрика:<td><input type="hidden" id="rubric_id" /><input type="hidden" id="rubric_sub_id" />'.
            '<tr><td class="label top">Текст:<td><textarea id="ztxt"></textarea><div id="txt-count"></div>'.
            '<tr><td class="label">Контактный телефон:<td><input type="text" id="telefon" maxlength="200" />'.
            '<tr><td class="label">Адрес:<td><input type="text" id="adres" maxlength="200" />'.
		'</table>'.
		'<table class="zatab rek dn">'.
            '<tr><td class="label">Размер блока:'.
                '<td><input type="text" id="size_x" maxlength="5" />'.
                    '<b class="xb">x</b>'.
                    '<input type="text" id="size_y" maxlength="5" />'.
                    ' = '.
					'<input type="text" id="kv_sm" readonly> см<sup>2</sup>'.
		'</table>'.
		'<table class="zatab">'.
			'<tr><td><td>'._imageAdd(array('owner'=>VIEWER_ID,'max'=>4)).
			'<tr><td class="label top">Номера выпуска:<td id="gn_spisok">'.
		'</table>'.
		'<table class="zatab skd dn">'.
            '<tr><td class="label">Скидка:<td><input type="hidden" id="skidka" value="'.$skidka.'" />'.
        '</table>'.
        '<table class="zatab manual">'.
            '<tr><td class="label">Указать стоимость вручную:<td>'._check('summa_manual').
        '</table>'.
        '<table class="zatab">'.
            '<tr><td class="label">Итоговая стоимость:'.
				'<td><input type="text" id="summa" readonly value="0" /> руб.'.
					'<span id="skidka-txt"></span><input type="hidden" id="skidka_sum" value="0" />'.
            '<tr><td class="label top">Заметка:<td><textarea id="note"></textarea>'.
            '<tr><td><td><div class="vkButton"><button>Внести</button></div>'.
                        '<div class="vkCancel"><button>Отмена</button></div>'.
        '</table>'.
	'</div>';
}//zayav_add()

function zayavFilter($v=array()) {
	$default = array(
		'page' => 1,
		'limit' => 20,
		'client_id' => 0,
		'find' => '',
		'cat' => 0,
		'nopublic' => 0,
		'gnyear' => strftime('%Y'),
		'nomer' => GN_FIRST_ACTIVE,
		'polosa' => 0,
		'polosa_color' => 0
	);
	$filter = array(
		'page' => _isnum(@$v['page']) ? $v['page'] : 1,
		'limit' => _isnum(@$v['limit']) ? $v['limit'] : 20,
		'client_id' => _isnum(@$v['client_id']),
		'find' => trim(@$v['find']),
		'cat' => _isnum(@$v['cat']),
		'gnyear' => isset($v['gnyear']) && preg_match(REGEXP_YEAR, $v['gnyear']) ? intval($v['gnyear']) : $default['gnyear'],
		'nomer' => isset($v['nomer']) && preg_match(REGEXP_NUMERIC, $v['nomer']) ? intval($v['nomer']) : $default['nomer'],
		'nopublic' => _isbool(@$v['nopublic']),
		'polosa' => _isnum(@$v['polosa']),
		'polosa_color' => _isnum(@$v['polosa_color']),
		'clear' => ''
	);
	if(!$filter['client_id'])
		foreach($default as $k => $r)
			if($r != $filter[$k]) {
				$filter['clear'] = '<a class="clear">Очистить фильтр</a>';
				break;
			}
	return $filter;
}//zayavFilter()
function zayav_data($v=array()) {
	$filter = zayavFilter($v);

	$limit = $filter['limit'];
	$page = $filter['page'];

	$cond = "!`deleted`";
	$pub = array();

	if($filter['find']) {
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
		if($page == 1)
			$find_id = _isnum($filter['find']);
	} else {
		if($filter['cat'])
			$cond .= " AND `category`=".$filter['cat'];
		if($filter['client_id'])
			$cond .= " AND `client_id`=".$filter['client_id'];
		elseif($filter['nopublic'])
			$cond .= " AND !`gn_count`";
		else {
			if($filter['nomer']) {
				$ids = array();
				$polosa = '';
				if($filter['polosa']) {
					if($filter['polosa'] == 1) {
						$idsPolosa = query_ids("SELECT `zayav_id` FROM `gazeta_nomer_pub` WHERE `general_nomer`=".$filter['nomer']." AND `dop`=1");
						$zayavRek = query_ids("SELECT `id` FROM `gazeta_zayav` WHERE !`deleted` AND `category`=2 AND `id` IN (".$idsPolosa.")");
						$polosa = " AND `zayav_id` IN (".$zayavRek.")";
					} elseif($filter['polosa'] == _gn($filter['nomer'], 'pc')) {
						$idsPolosa = query_ids("SELECT `zayav_id` FROM `gazeta_nomer_pub` WHERE `general_nomer`=".$filter['nomer']." AND `dop`=2");
						$zayavRek = query_ids("SELECT `id` FROM `gazeta_zayav` WHERE !`deleted` AND `category`=2 AND `id` IN (".$idsPolosa.")");
						$polosa = " AND `zayav_id` IN (".$zayavRek.")";
					} elseif($filter['polosa'] > 1 && $filter['polosa'] < _gn($filter['nomer'], 'pc')) {
						$polosa = " AND `polosa`=" . $filter['polosa'];
						if($filter['polosa_color'])
							$polosa .= " AND `dop`=" . $filter['polosa_color'];
					} elseif($filter['polosa'] == 105) {
						$idsPolosa = query_ids("SELECT `zayav_id` FROM `gazeta_nomer_pub` WHERE `general_nomer`=".$filter['nomer']." AND `dop` IN (3,4) AND !`polosa`");
						$zayavRek = query_ids("SELECT `id` FROM `gazeta_zayav` WHERE !`deleted` AND `category`=2 AND `id` IN (".$idsPolosa.")");
						$polosa = " AND `zayav_id` IN (".$zayavRek.")";
					} else {
						$idsPolosa = query_ids("SELECT `zayav_id` FROM `gazeta_nomer_pub` WHERE `general_nomer`=".$filter['nomer']." AND `dop`=".($filter['polosa'] - 100));
						$zayavRek = query_ids("SELECT `id` FROM `gazeta_zayav` WHERE !`deleted` AND `category`=2 AND `id` IN (".$idsPolosa.")");
						$polosa = " AND `zayav_id` IN (".$zayavRek.")";
					}
				}
				$sql = "SELECT *
						FROM `gazeta_nomer_pub`
						WHERE `general_nomer`=".$filter['nomer'].$polosa;
				$q = query($sql);
				while($r = mysql_fetch_assoc($q)) {
					$ids[] = $r['zayav_id'];
					$pub[$r['zayav_id']] = $r;
				}
				$ids = empty($ids) ? 0 : implode(',', $ids);
			} else {
				$ids = query_ids("SELECT `general_nomer` FROM `gazeta_nomer` WHERE SUBSTR(`day_public`,1,4)=".$filter['gnyear']);
				$ids = query_ids('SELECT DISTINCT `zayav_id` FROM `gazeta_nomer_pub` WHERE `general_nomer` IN ('.$ids.')');
			}
			$cond .= " AND `id` IN (".$ids.")";
		}
	}

	$all = query_value("SELECT COUNT(`id`) AS `all` FROM `gazeta_zayav` WHERE ".$cond);

	$zayav = array();
	if(isset($find_id)) {
		$sql = "SELECT * FROM `gazeta_zayav` WHERE !`deleted` AND `id`=".$find_id;
		if($r = query_assoc($sql)) {
			$all++;
			$r['find_id'] = 1;
			$zayav[$r['id']] = $r;
		}
	}

	if(!$all)
		return array(
			'all' => 0,
			'result' => 'Заявок не найдено.'.$filter['clear'],
			'spisok' => '<div class="_empty">Заявок не найдено.</div>',
			'filter' => $filter
		);

	$send = array(
		'all' => $all,
		'result' => 'Показан'._end($all, 'а', 'о').' '.$all.' заяв'._end($all, 'ка', 'ки', 'ок').$filter['clear'],
		'filter' => $filter,
		'spisok' => ''
	);

	$start = ($page - 1) * $limit;
	$sql = "SELECT *
			FROM `gazeta_zayav`
			WHERE ".$cond."
			ORDER BY `id` DESC
			LIMIT ".$start.",".$limit;
	$q = query($sql);
	while($r = mysql_fetch_assoc($q)) {
		if($filter['find']) {
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

//_pre($pub);

	foreach($zayav as $id => $r) {
		$gn = '';
		if(!empty($pub[$id]) && $r['category'] == 2) {
			$p = $pub[$id];
			$gn = '<div class="pub'.($p['general_nomer'] < GN_FIRST_ACTIVE ? ' lost' : '').'">'.
					'<input type="hidden" class="topub" id="p'.$p['id'].'" value="'.$p['dop'].'" />'.
					'<input type="hidden" id="np'.$p['id'].'" value="'.$p['polosa'].'" />'.
				  '</div>';
		}
		$cena = !empty($pub[$id]) ? round($pub[$id]['cena'], 2) : 0;
		$send['spisok'] .=
			'<div class="zayav_unit">'.
				'<div class="dtime">'.FullDataTime($r['dtime_add']).'</div>'.
				'<a href="'.URL.'&p=gazeta&d=zayav&d1=info&id='.$id.'" class="name">'._category($r['category']).' №'.(isset($r['find_id']) ? '<em>'.$id.'</em>' : $id).'</a>'.
				'<table class="tab"><tr>'.
					'<td class="td-values">'.
						'<table class="values">'.
							($r['client_id'] && !$filter['client_id'] ? '<tr><td class="label">Клиент:<td>'.$r['client_link'] : '').
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
							'<tr><td class="label">Стоимость:'.
								'<td><div class="sum">'.
										$gn.
										'<b>'.round($r['summa'], 2).'</b> руб.'.
										($r['summa_manual'] ? '<span class="manual">(указана вручную)</span>' : '').
									'</div>'.
						($filter['nomer'] ?
							'<tr><td class="label">Цена в '._gn($filter['nomer'], 'week').' ('.$filter['nomer'].'):'.
								'<td>'.$cena.' руб.'
						: '').

						'</table>'.
 ($r['image_id'] ? '<td class="image"><img src="'.$r['image_link'].'" class="_iview" val="'.$r['image_id'].'" />' : '').
				'</table>'.
			'</div>';
	}
	if($start + $limit < $all) {
		$c = $all - $start - $limit;
		$c = $c > $limit ? $limit : $c;
		$send['spisok'] .=
			'<div class="_next" val="'.($page + 1).'">'.
				'<span>Показать ещё '.$c.' заяв'._end($c, 'ку', 'ки', 'ок').'</span>'.
			'</div>';
	}
	return $send;
}//zayav_data()
function zayav_list($v) {
	$data = zayav_data($v);
	$v = $data['filter'];

	$cat = array(0 => 'Любая категория') + _category();
	$cat[1] .= '<div class="img_word"></div>';
	$polosa_color =  _radio('polosa_color', array(
		0 => 'Любая цветность',
		3 => 'Чёрно-белая',
		4 => 'Цветная'
	), $v['polosa_color'], 1);
	return
	'<script type="text/javascript">'.
		'var GN_SEL=['.gnJson().'];'.
	'</script>'.
	'<div id="zayav">'.
		'<div class="result">'.$data['result'].'</div>'.
		'<table class="tabLR">'.
			'<tr><td class="left">'.$data['spisok'].
				'<td class="right">'.
					'<div id="buttonCreate"><a HREF="'.URL.'&p=gazeta&d=zayav&d1=add&back=zayav">Новая заявка</a></div>'.
					'<div id="find"></div>'.
					'<div class="filter'.(!empty($v['find']) ? ' dn' : '').'">'.
						'<div class="findHead">Категория</div>'.
						_rightLink('cat', $cat, $v['cat']).
						_check('nopublic', 'Невыходившие заявки', $v['nopublic']).
						'<div class="filter_nomer'.($v['nopublic'] ? ' dn' : '').'">'.
							'<div class="findHead">Номер газеты</div>'.
							'<input type="hidden" id="gnyear" />'.
							'<input type="hidden" id="nomer" value="'.$v['nomer'].'" />'.
							'<div id="polosa_filter"'.($v['nomer'] ? '' : ' class="dn"').'>'.
								'<div class="findHead">Полоса</div>'.
								'<input type="hidden" id="polosa" value="'.$v['polosa'].'" />'.
								'<div id="polosa_color_filter"'.($v['nomer'] && $v['polosa'] > 1 && $v['polosa'] < _gn($v['nomer'], 'pc') ? '' : ' class="dn"').'>'.
									$polosa_color.
								'</div>'.
							'</div>'.
						'</div>'.
					'</div>'.
		'</table>'.
		'<script type="text/javascript">'.
			'var Z={'.
				'find:"'.$v['find'].'"'.
				'};'.
		'</script>'.
	'</div>';
}//zayav_list()
function zayav_info($zayav_id) {
	if(!_isnum($zayav_id))
		return _noauth('Страницы не существует');
	$sql = "SELECT * FROM `gazeta_zayav` WHERE `id`=".$zayav_id;
	if(!$z = query_assoc($sql))
		return _noauth('Заявки не существует');

	if(!VIEWER_ADMIN && $z['deleted'])
		return _noauth('Заявка удалена');

	define('OB', $z['category'] == 1);
	define('REK', $z['category'] == 2);

	//Список выходов
	$sql = "SELECT * FROM `gazeta_nomer_pub` WHERE `zayav_id`=".$zayav_id." ORDER BY `general_nomer`";
	$q = query($sql);
	$public = '';
	$public_lost = 0;
	if(mysql_num_rows($q)) {
		$tr = '';
		while($r = mysql_fetch_assoc($q)) {
			$tr .=
			'<tr'.($r['general_nomer'] < GN_FIRST_ACTIVE ? ' class="lost"' : '').'>'.
				'<td class="nomer"><b>'._gn($r['general_nomer'], 'week').'</b><em>('.$r['general_nomer'].')</em>'.
				'<td class="public">'._gn($r['general_nomer'], 'pub').
				'<td class="cena">'.round($r['cena'], 2).
			(OB || REK ?
				'<td class="dop">'.(OB ?
					_obDop($r['dop']) :
					_polosa($r['dop']).($r['polosa'] ? ' '.$r['polosa'].'-я' : ''))
			: '');
			if($r['general_nomer'] < GN_FIRST_ACTIVE)
				$public_lost++;
		}
		$public =
			'<table class="_spisok gn">'.
				'<tr><th>Номер'.
					'<th>Выход'.
					'<th>Цена'.
			  (OB ? '<th>Дополнительно' : '').
			 (REK ? '<th>Полоса' : '').
($public_lost ? '<tr id="lost-count"><td colspan="4">Показать прошедшие выходы ('.$public_lost.')' : '').
				$tr.
			'</table>';
	}

	$income = income_spisok(array('zayav_id'=>$zayav_id));
	$history = gazeta_history(array('zayav_id'=>$zayav_id));

	return
	'<script type="text/javascript">'.
		'var OPL={'.
			'from:"zayav",'.
			'client_id:'.$z['client_id'].','.
			'client_fio:"'.addslashes(_clientLink($z['client_id'], 1)).'",'.
			'zayav_id:'.$zayav_id.','.
			'zayav_name:"'._category($z['category']).' №'.$zayav_id.'"'.
		'};'.
	'</script>'.
	'<div id="zayav-info">'.
		'<div id="dopLinks">'.
			'<a class="link zinfo sel">Просмотр</a>'.
		(!$z['deleted'] ?
			'<a class="link" href="'.URL.'&p=gazeta&d=zayav&d1=edit&id='.$zayav_id.'">Редактирование</a>'.
			'<a class="link income-add">Внести платёж</a>'
		: '').
			'<a class="link hist">История</a>'.
		(!$public_lost && !$z['deleted'] ?
			($income['all'] && !$z['client_id']? '<span class="off">Удалить заявку</span>' : '<a class="zdel">Удалить заявку</a>')
		: '').
		'</div>'.
		($z['deleted'] ? '<div class="_info">Заявка удалёна</div>' : '').
		'<div class="headName">'._category($z['category']).' №'.$zayav_id.'</div>'.
		'<div class="content">'.
			'<table class="ztab">'.
				($z['client_id'] ? '<tr><td class="label">Клиент:<td>'._clientLink($z['client_id']) : '').
		(OB ?	'<tr><td class="label">Рубрика:'.
					'<td>'._rubric($z['rubric_id']).
						   ($z['rubric_sub_id'] ? '<span class="ug">»</span>'._rubricsub($z['rubric_sub_id']) : '').
				'<tr><td class="label top">Текст:'.
					'<td><div class="ztxt">'.
							$z['txt'].
							($z['telefon'] ? '<span class="tel">Тел.: '.$z['telefon'].'</span>' : '').
							($z['adres'] ? '<span class="tel">Адрес.: '.$z['adres'].'</span>' : '').
						'</div>'
		: '').
		(REK ?	'<tr><td class="label">Размер:'.
					'<td>'.round($z['size_x'], 1).' x '.
						   round($z['size_y'], 1).' = '.
						   '<b>'.round($z['size_x'] * $z['size_y']).'</b> см&sup2;'
		: '').
				zayav_images($zayav_id).
				'<tr><td class="label">Общая стоимость:'.
					'<td><b>'.round($z['summa'], 2).'</b> руб.'.
						 ((OB || REK) && $z['summa_manual'] ? '<span class="manual">(указана вручную)</span>' : '').
						 ($z['skidka'] ? '<span class="skidka">Скидка <b>'.$z['skidka'].'</b>% ('.round($z['skidka_sum'], 2).' руб.)</span>' : '').
				(!$z['client_id'] ? '<tr><td class="label">Оплачено:<td>'.round(query_value("SELECT SUM(`sum`) FROM `gazeta_money` WHERE `zayav_id`=".$zayav_id), 2).' руб.' : '').
				($public ? '<tr><td class="label top">Номера выпуска:<td>'.$public : '').
			'</table>'.
			'<div class="added">'.
				'Заявку '.viewerAdded($z['viewer_id_add']).' '.FullDataTime($z['dtime_add']).
				($z['deleted'] ?
					'<br />Удалил'.(_viewer($z['viewer_id_del'], 'sex') == 1 ? 'а' : '').' '.
					_viewer($z['viewer_id_del'], 'name').' '.
					FullDataTime($z['dtime_del'])
				: '').
			'</div>'.
			'<div class="headBlue">Платежи<a class="add income-add">Внести платёж</a></div>'.
			'<div id="income_spisok">'.($income['all'] ? $income['spisok'] : '').'</div>'.
			_vkComment('zayav', $zayav_id).
		'</div>'.
		'<div class="histories">'.$history['spisok'].'</div>'.
	'</div>';
}//zayav_info()
function zayav_edit($zayav_id) {
	if(!preg_match(REGEXP_NUMERIC, $zayav_id))
		return _noauth('Страницы не существует');
	$sql = "SELECT * FROM `gazeta_zayav` WHERE `id`=".$zayav_id;
	if(!$z = mysql_fetch_assoc(query($sql)))
		return _noauth('Заявки не существует');
	if($z['deleted'])
		return _noauth('Заявка удалена');

	define('OB', $z['category'] == 1);
	define('REK', $z['category'] == 2);

	$sql = "SELECT * FROM `gazeta_nomer_pub` WHERE `zayav_id`=".$zayav_id." AND `general_nomer`>=".GN_FIRST_ACTIVE." ORDER BY `general_nomer` ASC";
	$q = query($sql);
	$gns = array();
	while($r = mysql_fetch_assoc($q))
		$gns[] = $r['general_nomer'].':['.round($r['cena'], 6).','.$r['dop'].','.$r['polosa'].']';

	return
	'<script type="text/javascript">'.
		'var ZAYAV={'.
			'id:'.$zayav_id.','.
			'category:'.$z['category'].','.
			(!empty($gns) ? 'gns:{'.implode(',', $gns).'}'.',' : '').
			'kv_sm:'.round($z['size_x'] * $z['size_y']).','.
			'skidka:'.$z['skidka'].','.
			'manual:'.$z['summa_manual'].
		'};'.
	'</script>'.
	'<div id="zayav-edit">'.
		'<div id="dopLinks">'.
			'<a class="link" href="'.URL.'&p=gazeta&d=zayav&d1=info&id='.$zayav_id.'">Просмотр</a>'.
			'<a class="link sel">Редактирование</a>'.
		'</div>'.
		'<div class="headName">'._category($z['category']).' №'.$zayav_id.' - редактирование</div>'.
		'<table class="zetab">'.
			'<tr><td class="label">Клиент:'.
				'<td><input type="hidden" id="client_id" value="'.$z['client_id'].'" />'.
					($z['client_id'] ? _clientLink($z['client_id']) : '').
	(OB ?	'<tr><td class="label">Рубрика:'.
				'<td><input type="hidden" id="rubric_id" value="'.$z['rubric_id'].'" />'.
					'<input type="hidden" id="rubric_sub_id" value="'.$z['rubric_sub_id'].'" />'.
			'<tr><td class="label top">Текст:<td><textarea id="ztxt">'.$z['txt'].'</textarea><div id="txt-count"></div>'.
			'<tr><td class="label">Контактный телефон:<td><input type="text" id="telefon" maxlength="200" value="'.$z['telefon'].'" />'.
			'<tr><td class="label">Адрес:<td><input type="text" id="adres" maxlength="200" value="'.$z['adres'].'" />'
	: '').
	(REK ?  '<tr><td class="label">Размер блока:'.
				'<td><input type="text" id="size_x" maxlength="5" value="'.round($z['size_x'], 1).'" />'.
					'<b class="xb">x</b>'.
					'<input type="text" id="size_y" maxlength="5" value="'.round($z['size_y'], 1).'" />'.
					' = '.
					'<input type="text" id="kv_sm" readonly value="'.round($z['size_x'] * $z['size_y']).'" /> см<sup>2</sup>'
	: '').
			'<tr><td><td>'._imageAdd(array('owner'=>'zayav'.$zayav_id,'max'=>4)).
			'<tr><td class="label top">Номера выпуска:<td id="gn_spisok">'.
	 (REK ? '<tr><td class="label">Скидка:<td><input type="hidden" id="skidka" value="'.$z['skidka'].'" />' : '').
(OB || REK ? '<tr><td class="label">Указать стоимость вручную:<td>'._check('summa_manual', '', $z['summa_manual']) : '').
			'<tr><td class="label">Итоговая стоимость:'.
				'<td><input type="text" id="summa" '.($z['summa_manual'] || !OB && !REK ? '' : 'readonly').' value="0" /> руб.'.
					'<span id="skidka-txt"></span>'.
					'<input type="hidden" id="skidka_sum" />'.
			'<tr><td><td><div class="vkButton"><button>Сохранить</button></div>'.
						'<div class="vkCancel"><button>Отмена</button></div>'.
	'</table>'.
	'</div>';
}//zayav_edit()
function zayav_images($zayav_id) {
	$sql = "SELECT * FROM `images` WHERE !`deleted` AND `owner`='zayav".$zayav_id."' ORDER BY `sort`";
	$q = query($sql);
	if(!mysql_num_rows($q))
		return '';
	$images = '<table class="images"><tr>';
	while($r = mysql_fetch_assoc($q))
		$images .= '<td><img src="'.$r['path'].$r['small_name'].'" class="_iview" val="'.$r['id'].'" />';
	$images .= '</table>';
	return '<tr><td class="label top">Изображения:<td>'.$images;
}//zayav_images()


// ---===! report !===--- Секция отчётов

function report() {
	$def = 'history';
	$pages = array(
		'history' => 'История действий',
		'zayav' => 'Заявки',
		'money' => 'Деньги'
	);

	$d1 = '';
	if(!empty($_GET['d1']))
		foreach($pages as $p => $name)
			if(isset($pages[$_GET['d1']])) {
				$d1 = $_GET['d1'];
				break;
			}
	if(!$d1)
		$d1 = $def;

	$links = '';
	foreach($pages as $p => $name)
		$links .= '<a href="'.URL.'&p=gazeta&d=report&d1='.$p.'"'.($d1 == $p ? ' class="sel"' : '').'>'.$name.'</a>';

	$d2 = '';
	$right = '';
	switch(@$_GET['d1']) {
		default:
		case 'history':
			$data = gazeta_history();
			$left = $data['spisok'];
		break;
		case 'zayav':
			$left = '<div id="spisok">'.report_zayav_nomer().'</div>';
			$right =
				'<script type="text/javascript">'.
					'var GN_SEL=['.gnJson().'];'.
				'</script>'.
				'<div class="findHead">Номер газеты</div>'.
				'<input type="hidden" id="gnyear" />'.
				'<input type="hidden" id="nomer" value="'.GN_FIRST_ACTIVE.'" />';
			break;
		case 'money':
			$d2 = empty($_GET['d2']) ? 'income' : $_GET['d2'];
			switch($d2) {
				default: $d2 = 'income';
				case 'income':
					switch(@$_GET['d3']) {
						case 'all': $left = income_all(); break;
						case 'year':
							if(empty($_GET['year']) || !preg_match(REGEXP_YEAR, $_GET['year'])) {
								$left = 'Указан некорректный год.';
								break;
							}
							$left = income_year(intval($_GET['year']));
							break;
						case 'month':
							if(empty($_GET['mon']) || !preg_match(REGEXP_YEARMONTH, $_GET['mon'])) {
								$left = 'Указан некорректный месяц.';
								break;
							}
							$left = income_month($_GET['mon']);
							break;
						default:
							if(!_calendarDataCheck(@$_GET['day']))
								$_GET['day'] = strftime('%Y-%m-%d');
							$left = income_day($_GET['day']);
							$right = income_right($_GET['day']);
					}
					break;
				case 'expense':
					$left = expense();
					$right = expense_right();
					break;
				case 'invoice': $left = invoice(); break;
			}
			$left =
				'<div id="dopLinks">'.
					'<a class="link'.($d2 == 'income' ? ' sel' : '').'" href="'.URL.'&p=gazeta&d=report&d1=money&d2=income">Платежи</a>'.
					'<a class="link'.($d2 == 'expense' ? ' sel' : '').'" href="'.URL.'&p=gazeta&d=report&d1=money&d2=expense">Расходы</a>'.
					'<a class="link'.($d2 == 'invoice' ? ' sel' : '').'" href="'.URL.'&p=gazeta&d=report&d1=money&d2=invoice">Счета</a>'.
				'</div>'.
				$left;
			break;
	}
	return
	'<table class="tabLR '.($d2 ? $d2 : $d1).'" id="report">'.
		'<tr><td class="left">'.$left.
			'<td class="right">'.
				'<div class="rightLink">'.$links.'</div>'.
				$right.
	'</table>';
}//report()
function gazeta_history_types($v, $filter) {
	switch($v['type']) {
		case 11: return 'Создание новой заявки '.$v['zayav_link'].' - <u>'.$v['zayav_type'].'</u>'.
						($v['client_id'] ? ' для клиента '.$v['client_link'] : '').'.';
		case 31: return 'Редактирование заявки '.$v['zayav_link'].' - <u>'.$v['zayav_type'].'</u>'.
						($v['value'] ? ':<div class="changes">'.$v['value'].'</div>' : '.');
		case 32: return 'Изменение полосы у '.$v['value'].'-го номера для Рекламы '.$v['zayav_link'].':'.
						'<div class="changes">'.$v['value1'].'</div>';

		case 45: return
			'Платёж <span class="oplata">'._invoice($v['value2']).'</span> '.
			'на сумму <b>'.$v['value'].'</b> руб.'.
			($v['value1'] ? ' <em>('.$v['value1'].')</em>' : '').
			($v['zayav_id'] ? ' по заявке '.$v['zayav_link'].' - <u>'.$v['zayav_type'].'</u>' : '').
			'.';
		case 47: return
			'Удаление платежа <span class="oplata">'._invoice($v['value2']).'</span> '.
			'на сумму <b>'.$v['value'].'</b> руб.'.
			($v['value1'] ? ' <em>('.$v['value1'].')</em>' : '').
			($v['zayav_id'] ? ' у заявки '.$v['zayav_link'].' - <u>'.$v['zayav_type'].'</u>' : '').
			'.';

		case 51: return 'Внесение нового клиента '.$v['client_link'].'.';
		case 52: return 'Изменение данных клиента '.$v['client_link'].':<div class="changes">'.$v['value'].'</div>';
		case 53: return 'Удаление клиента '.$v['client_link'].'.';

		case 61: return 'Удаление заявки '.$v['zayav_link'].' - <u>'.$v['zayav_type'].'</u>.';

		case 81: return 'Внесение расхода: '.
			($v['value1'] ? '<span class="oplata">'._expense($v['value1']).'</span> ' : '').
			($v['value2'] ? '<em>('.$v['value2'].')</em> ' : '').
			($v['value3'] ? _viewer($v['value3'], 'link').' ' : '').
			'на сумму <b>'.$v['value'].'</b> руб.';
		case 82: return 'Удаление расхода: '.
		($v['value1'] ? '<span class="oplata">'._expense($v['value1']).'</span> ' : '').
		($v['value2'] ? '<em>('.$v['value2'].')</em> ' : '').
		($v['value3'] ? 'для сотрудника '._viewer($v['value3'], 'link').' ' : '').
		'на сумму <b>'.$v['value'].'</b> руб.';

		case 91: return 'Установка текущей суммы для счёта <span class="oplata">'._invoice($v['value1']).'</span>: <b>'.$v['value'].'</b> руб.';
		case 92:
			return 'Перевод со счёта <span class="oplata">'._invoice($v['value1'] > 100 ? 1 : $v['value1']).'</span> '.
			($v['value1'] > 100 ? '<u>'._viewer($v['value1'], 'name').'</u> ' : '').
			'на счёт <span class="oplata">'._invoice($v['value2'] > 100 ? 1 : $v['value2']).'</span> '.
			($v['value2'] > 100 ? '<u>'._viewer($v['value2'], 'name').'</u> ' : '').
			'в сумме <b>'.$v['value'].'</b> руб.'.
			($v['value3'] ? ' <em>('.$v['value3'].')</em> ' : '');


		case 1011: return 'В настройках добавлена новая категория клиентов <u>'.$v['value'].'</u>.';
	    case 1012: return 'В настройках изменены данные категории клиентов <u>'.$v['value'].'</u>:<div class="changes">'.$v['value1'].'</div>';
        case 1013: return 'В настройках удалена категория клиентов <u>'.$v['value'].'</u>.';

        case 1021: return 'В настройках добавлена новая рубрика <u>'.$v['value'].'</u>.';
		case 1022: return 'В настройках изменена рубрика <u>'.$v['value'].'</u>:<div class="changes">'.$v['value1'].'</div>';
		case 1023: return 'В настройках удалена рубрика <u>'.$v['value'].'</u>.';

		case 1031: return 'В настройках добавлен '.$v['value'].'-й номер газеты.';
		case 1032: return 'В настройках изменены данные '.$v['value'].'-го номера газеты:<div class="changes">'.$v['value1'].'</div>';
		case 1033: return 'В настройках удалён '.$v['value'].'-й номер газеты.';
		case 1034: return 'В настройках создан список номеров газет на '.$v['value'].' год.';
		case 1035: return 'В настройках очищен список номеров газет на '.$v['value'].' год.';

		case 1041: return 'В настройках добавлено новое название полосы <u>'.$v['value'].'</u>.';
        case 1042: return 'В настройках изменены данные полосы <u>'.$v['value'].'</u>:<div class="changes">'.$v['value1'].'</div>';

		case 1051: return 'В настройках добавлена новая скидка <u>'.$v['value'].'%</u>.';
        case 1052: return 'В настройках изменены данные скидки <u>'.$v['value'].'%</u>:<div class="changes">'.$v['value1'].'</div>';
        case 1053: return 'В настройках удалена скидка <u>'.$v['value'].'%</u>.';

        case 1062: return 'В настройках изменена стоимость доп. параметра объявления <u>'.$v['value'].'</u>:<div class="changes">'.$v['value1'].'</div>';

		case 1071: return 'В настройках в рубрике <u>'.$v['value'].'</u> добавлена новая подрубрика <u>'.$v['value1'].'</u>.';
		case 1072: return 'В настройках в рубрике <u>'.$v['value'].'</u> изменены данные подрубрики:<div class="changes">'.$v['value1'].'</div>';
		case 1073: return 'В настройках в рубрике <u>'.$v['value'].'</u> удалена подрубрика <u>'.$v['value1'].'</u>.';

		case 1081: return 'В настройках добавлен новый сотрудник '._viewer($v['value'], 'name').'.';
        case 1082: return 'В настройках удалён сотрудник '._viewer($v['value'], 'name').'.';

		case 1091: return 'В настройках изменена стоимость длины объявлений.';

		case 1101: return 'В настройках добавлена новая категория расхода <u>'.$v['value'].'</u>.';
		case 1102: return 'В настройках изменены данные категории расхода <u>'.$v['value'].'</u>:<div class="changes">'.$v['value1'].'</div>';
		case 1103: return 'В настройках удалена категория расхода <u>'.$v['value'].'</u>.';

		case 1111: return 'В настройках добавлен новый вид платежа <u>'.$v['value'].'</u>.';
		case 1112: return 'В настройках изменён вид платежа <u>'.$v['value'].'</u>:<div class="changes">'.$v['value1'].'</div>';
		case 1113: return 'В настройках удалён вид платежа <u>'.$v['value'].'</u>.';

		case 1121: return 'В настройках: внесение нового счёта <u>'.$v['value'].'</u>.';
		case 1122: return 'В настройках: изменение данных счёта <u>'.$v['value'].'</u>:<div class="changes">'.$v['value1'].'</div>';
		case 1123: return 'В настройках: удаление счёта <u>'.$v['value'].'</u>.';

		default: return $v['type'];
	}
}//gazeta_history_types()
function gazeta_history($v=array()) {
	return _history(
		'gazeta_history_types',
		array('_clientLink', '_zayavLink'),
		$v,
		array(
			'table' => 'gazeta_history',
			'client_id' => !empty($v['client_id']) && _isnum($v['client_id']) ? intval($v['client_id']) : 0,
			'zayav_id' => !empty($v['zayav_id']) && _isnum($v['zayav_id']) ? intval($v['zayav_id']) : 0
		)
	);
}//gazeta_history()

function income_path($data) {
	$ex = explode(':', $data);
	$d = explode('-', $ex[0]);
	define('YEAR', $d[0]);
	define('MON', @$d[1]);
	define('DAY', @$d[2]);
	$to = '';
	if(!empty($ex[1])) {
		$d = explode('-', $ex[1]);
		$to = ' - '.intval($d[2]).
			($d[1] != MON ? ' '._monthDef($d[1]) : '').
			($d[0] != YEAR ? ' '.$d[0] : '');
	}
	return
		'<a href="'.URL.'&p=gazeta&d=report&d1=money&d2=income&d3=all">Год</a> » '.(YEAR ? '' : '<b>За всё время</b>').
		(MON ? '<a href="'.URL.'&p=gazeta&d=report&d1=money&d2=income&d3=year&year='.YEAR.'">'.YEAR.'</a> » ' : '<b>'.YEAR.'</b>').
		(DAY ? '<a href="'.URL.'&p=gazeta&d=report&d1=money&d2=income&d3=month&mon='.YEAR.'-'.MON.'">'._monthDef(MON, 1).'</a> » ' : (MON ? '<b>'._monthDef(MON, 1).'</b>' : '')).
		(DAY ? '<b>'.intval(DAY).$to.'</b>' : '');

}//income_path()
function income_all() {
	$sql = "SELECT DATE_FORMAT(`dtime_add`,'%Y') AS `year`,
				   SUM(`sum`) AS `sum`
			FROM `gazeta_money`
			WHERE !`deleted`
			  AND `sum`>0
			GROUP BY DATE_FORMAT(`dtime_add`,'%Y')
			ORDER BY `dtime_add` ASC";
	$q = query($sql);
	$spisok = array();
	while($r = mysql_fetch_assoc($q))
		$spisok[$r['year']] = '<tr>'.
			'<td><a href="'.URL.'&p=gazeta&d=report&d1=money&d2=income&d3=year&year='.$r['year'].'">'.$r['year'].'</a>'.
			'<td class="r"><b>'._sumSpace($r['sum']).'</b>';

	$th = '';
	foreach(_invoice() as $invoice_id => $i) {
		$th .= '<th>'.wordwrap($i['name'], 7, '<br />', 1);
		foreach($spisok as $y => $r)
			$spisok[$y] .= '<td class="r">';
		$sql = "SELECT DATE_FORMAT(`dtime_add`,'%Y') AS `year`,
					   SUM(`sum`) AS `sum`
				FROM `gazeta_money`
				WHERE !`deleted`
				  AND `sum`>0
				  AND `invoice_id`=".$invoice_id."
				GROUP BY DATE_FORMAT(`dtime_add`,'%Y')
				ORDER BY `dtime_add` ASC";
		$q = query($sql);
		while($r = mysql_fetch_assoc($q))
			$spisok[$r['year']] .= _sumSpace($r['sum']);
	}

	return
		'<div class="headName">Суммы платежей по годам</div>'.
		'<table class="_spisok sums">'.
			'<tr><th>Год'.
				'<th>Всего'.
				$th.
				implode('', $spisok).
		'</table>';
}//income_all()
function income_year($year) {
	$spisok = array();
	for($n = 1; $n <= (strftime('%Y', time()) == $year ? intval(strftime('%m', time())) : 12); $n++)
		$spisok[$n] =
			'<tr><td class="r grey">'._monthDef($n, 1).
			'<td class="r">';
	$sql = "SELECT DATE_FORMAT(`dtime_add`,'%m') AS `mon`,
				   SUM(`sum`) AS `sum`
			FROM `gazeta_money`
			WHERE !`deleted`
			  AND `sum`>0
			  AND `dtime_add` LIKE '".$year."%'
			GROUP BY DATE_FORMAT(`dtime_add`,'%m')
			ORDER BY `dtime_add` ASC";
	$q = query($sql);
	while($r = mysql_fetch_assoc($q))
		$spisok[intval($r['mon'])] =
			'<tr><td class="r"><a href="'.URL.'&p=gazeta&d=report&d1=money&d2=income&d3=month&mon='.$year.'-'.$r['mon'].'">'._monthDef($r['mon'], 1).'</a>'.
				'<td class="r"><b>'._sumSpace($r['sum']).'</b>';

	$th = '';
	foreach(_invoice() as $invoice_id => $i) {
		$th .= '<th>'.wordwrap($i['name'], 7, '<br />', 1);
		foreach($spisok as $y => $r)
			$spisok[$y] .= '<td class="r">';
		$sql = "SELECT DATE_FORMAT(`dtime_add`,'%m') AS `mon`,
					   SUM(`sum`) AS `sum`
				FROM `gazeta_money`
				WHERE !`deleted`
				  AND `sum`>0
				  AND `dtime_add` LIKE '".$year."%'
				  AND `invoice_id`=".$invoice_id."
				GROUP BY DATE_FORMAT(`dtime_add`,'%m')
				ORDER BY `dtime_add` ASC";
		$q = query($sql);
		while($r = mysql_fetch_assoc($q))
			$spisok[intval($r['mon'])] .= _sumSpace($r['sum']);
	}
	return
		'<div class="headName">Суммы платежей по месяцам за '.$year.' год</div>'.
		'<div class="inc-path">'.income_path($year).'</div>'.
		'<table class="_spisok sums">'.
			'<tr><th>Месяц'.
			'<th>Всего'.
			$th.
			implode('', $spisok).
		'</table>';
}//income_year()
function income_month($mon) {
	$path = income_path($mon);
	$spisok = array();
	for($n = 1; $n <= (strftime('%Y%m', time()) == YEAR.MON ? intval(strftime('%d', time())) : date('t', strtotime($mon.'-01'))); $n++)
		$spisok[$n] =
			'<tr><td class="r grey">'.$n.'.'.MON.'.'.YEAR.
			'<td class="r">';
	$sql = "SELECT DATE_FORMAT(`dtime_add`,'%d') AS `day`,
				   SUM(`sum`) AS `sum`
			FROM `gazeta_money`
			WHERE !`deleted`
			  AND `sum`>0
			  AND `dtime_add` LIKE '".$mon."%'
			GROUP BY DATE_FORMAT(`dtime_add`,'%d')
			ORDER BY `dtime_add` ASC";
	$q = query($sql);
	while($r = mysql_fetch_assoc($q))
		$spisok[intval($r['day'])] =
			'<tr><td class="r"><a href="'.URL.'&p=gazeta&d=report&d1=money&d2=income&day='.$mon.'-'.$r['day'].'">'.intval($r['day']).'.'.MON.'.'.YEAR.'</a>'.
				'<td class="r"><b>'._sumSpace($r['sum']).'</b>';

	$th = '';
	foreach(_invoice() as $invoice_id => $i) {
		$th .= '<th>'.wordwrap($i['name'], 7, '<br />', 1);
		foreach($spisok as $y => $r)
			$spisok[$y] .= '<td class="r">';
		$sql = "SELECT DATE_FORMAT(`dtime_add`,'%d') AS `day`,
					   SUM(`sum`) AS `sum`
				FROM `gazeta_money`
				WHERE !`deleted`
				  AND `sum`>0
				  AND `dtime_add` LIKE '".$mon."%'
				  AND `invoice_id`=".$invoice_id."
				GROUP BY DATE_FORMAT(`dtime_add`,'%d')
				ORDER BY `dtime_add` ASC";
		$q = query($sql);
		while($r = mysql_fetch_assoc($q))
			$spisok[intval($r['day'])] .= _sumSpace($r['sum']);
	}
	return
		'<div class="headName">Суммы платежей по дням за '._monthDef(MON, 1).' '.YEAR.'</div>'.
		'<div class="inc-path">'.$path.'</div>'.
		'<table class="_spisok sums">'.
			'<tr><th>Месяц'.
				'<th>Всего'.
				$th.
				implode('', $spisok).
		'</table>';
}//income_month()
function income_day($day) {
	$data = income_spisok(array('day' => $day));
	return
		'<script type="text/javascript">var OPL={from:"income"};</script>'.
		'<div class="headName">Список платежей<a class="add income-add">Внести платёж</a></div>'.
		'<div class="inc-path">'.income_path($day).'</div>'.
		'<div id="spisok">'.$data['spisok'].'</div>';

}//income_day()
function income_days($month=0) {
	$sql = "SELECT DATE_FORMAT(`dtime_add`,'%Y-%m-%d') AS `day`
			FROM `gazeta_money`
			WHERE !`deleted`
			  AND `sum`>0
			  AND `dtime_add` LIKE ('".($month ? $month : strftime('%Y-%m'))."%')
			GROUP BY DATE_FORMAT(`dtime_add`,'%d')";
	$q = query($sql);
	$days = array();
	while($r = mysql_fetch_assoc($q))
		$days[$r['day']] = 1;
	return $days;
}//income_days()
function income_right($sel) {
	$workers = query_selJson("
		SELECT
			DISTINCT `m`.`viewer_id_add`,
			CONCAT(`u`.`first_name`,' ',`u`.`last_name`)
        FROM `gazeta_money` `m`,`vk_user` `u`
        WHERE `m`.`viewer_id_add`=`u`.`viewer_id`
          AND !`m`.`deleted`
          AND `m`.`sum`>0");
	return
		_calendarFilter(array(
			'days' => income_days(),
			'func' => 'income_days',
			'sel' => $sel
		)).
		'<div class="findHead">Счёт</div>'.
		'<input type="hidden" id="invoice_id" />'.
		'<script type="text/javascript">var WORKERS='.$workers.';</script>'.
		'<div class="findHead">Вносил сотрудник</div>'.
		'<input type="hidden" id="worker_id" />';
}//income_right()

function income_insert($v) {//Внесение платежа
	$v = array(
		'from' => empty($v['from']) ? '' : $v['from'],
		'client_id' => !empty($v['client_id']) ? intval($v['client_id']) : 0,
		'zayav_id' => !empty($v['zayav_id']) ? intval($v['zayav_id']) : 0,
		'invoice_id' => intval($v['invoice_id']),
		'sum' => str_replace(',', '.', $_POST['sum']),
		'prim' => empty($v['prim']) ? '' : win1251(htmlspecialchars(trim($v['prim'])))
	);

	if($v['zayav_id']) {
		$sql = "SELECT * FROM `gazeta_zayav` WHERE !`deleted` AND `id`=".$v['zayav_id'];
		if(!$z = mysql_fetch_assoc(query($sql)))
			return false;
		if($v['client_id'] && $v['client_id'] != $z['client_id'])
			return false;
		$v['client_id'] = $z['client_id'];
	}

	$sql = "INSERT INTO `gazeta_money` (
				`zayav_id`,
				`client_id`,
				`invoice_id`,
				`sum`,
				`prim`,
				`viewer_id_add`
			) VALUES (
				".$v['zayav_id'].",
				".$v['client_id'].",
				".$v['invoice_id'].",
				".$v['sum'].",
				'".addslashes($v['prim'])."',
				".VIEWER_ID."
			)";
	query($sql);
	$insert_id = mysql_insert_id();

	clientBalansUpdate($v['client_id']);
//	_zayavBalansUpdate($v['zayav_id']);

	invoice_history_insert(array(
		'action' => 1,
		'table' => 'gazeta_money',
		'id' => $insert_id
	));

	_historyInsert(
		45,
		array(
			'zayav_id' => $v['zayav_id'],
			'client_id' => $v['client_id'],
			'value' => $v['sum'],
			'value1' => $v['prim'],
			'value2' => $v['invoice_id']
		),
		'gazeta_history'
	);

	switch($v['from']) {
		case 'client':
			$data = income_spisok(array('client_id'=>$v['client_id'],'limit'=>15));
			return $data['spisok'];
		case 'zayav':
			$data = income_spisok(array('zayav_id'=>$v['zayav_id']));
			return $data['spisok'];
		default: return $insert_id;
	}
}//income_insert()
function incomeFilter($v) {
	$send = array(
		'page' => !empty($v['page']) && preg_match(REGEXP_NUMERIC, $v['page']) ? $v['page'] : 1,
		'limit' => !empty($v['limit']) && preg_match(REGEXP_NUMERIC, $v['limit']) ? $v['limit'] : 30,
		'invoice_id' => !empty($v['invoice_id']) && preg_match(REGEXP_NUMERIC, $v['invoice_id']) ? $v['invoice_id'] : 0,
		'worker_id' => !empty($v['worker_id']) && preg_match(REGEXP_NUMERIC, $v['worker_id']) ? $v['worker_id'] : 0,
		'client_id' => !empty($v['client_id']) && preg_match(REGEXP_NUMERIC, $v['client_id']) ? intval($v['client_id']) : 0,
		'zayav_id' => !empty($v['zayav_id']) && preg_match(REGEXP_NUMERIC, $v['zayav_id']) ? intval($v['zayav_id']) : 0,
		'deleted' => isset($v['deleted']) && preg_match(REGEXP_BOOL, $v['deleted']) ? $v['deleted'] : 0,
		'day' => '',
		'from' => '',
		'to' => ''
	);
	return _calendarPeriod(@$v['day']) + $send;
}//incomeFilter()
function income_spisok($v=array()) {
	$filter = incomeFilter($v);

	$page = $filter['page'];

	$cond = '!`deleted` AND `sum`>0';

	if($filter['worker_id'])
		$cond .= " AND `viewer_id_add`=".$filter['worker_id'];
	if($filter['invoice_id'])
		$cond .= " AND `invoice_id`=".$filter['invoice_id'];
	if($filter['client_id'])
		$cond .= " AND `client_id`=".$filter['client_id'];
	if($filter['zayav_id'])
		$cond .= " AND `zayav_id`=".$filter['zayav_id'];
	if($filter['day'])
		$cond .= " AND `dtime_add` LIKE '".$filter['day']."%'";
	if($filter['from'])
		$cond .= " AND `dtime_add`>='".$filter['from']." 00:00:00' AND `dtime_add`<='".$filter['to']." 23:59:59'";

	$sql = "SELECT
	            COUNT(*) AS `all`,
				SUM(`sum`) AS `sum`
			FROM `gazeta_money`
			WHERE ".$cond."
			LIMIT 1";
	$send = mysql_fetch_assoc(query($sql));
	if(!$send['all'])
		return array(
			'all' => 0,
			'spisok' => '<div class="_empty">Платежей нет.</div>',
			'filter' => $filter
		);

	$start = ($page - 1) * $filter['limit'];
	$sql = "SELECT *
			FROM `gazeta_money`
			USE INDEX (`i_dtime_add`)
			WHERE ".$cond."
			ORDER BY `id` ASC
			LIMIT ".$start.",".$filter['limit'];
	$q = query($sql);
	$money = array();
	while($r = mysql_fetch_assoc($q))
		$money[$r['id']] = $r;

	$money = _zayavLink($money);

	$send['spisok'] = '';
	$send['filter'] = $filter;
	if($page == 1)
		$send['spisok'] =
			'<input type="hidden" id="money_limit" value="'.$filter['limit'].'" />'.
			'<input type="hidden" id="money_client_id" value="'.$filter['client_id'].'" />'.
			'<input type="hidden" id="money_zayav_id" value="'.$filter['zayav_id'].'" />'.
			'<input type="hidden" id="money_deleted" value="'.$filter['deleted'].'" />'.
			'<input type="hidden" id="money_invoice_id" value="'.$filter['invoice_id'].'" />'.
			'<input type="hidden" id="money_worker_id" value="'.$filter['worker_id'].'" />'.
			(!$filter['zayav_id'] ?
				'<div class="_moneysum">'.
					'Показан'._end($send['all'], '', 'о').
					' <b>'.$send['all'].'</b> платеж'._end($send['all'], '', 'а', 'ей').
					' на сумму <b>'._sumSpace($send['sum']).'</b> руб.'.
				'</div>' : '').
			'<table class="_spisok _money">'.
			(!$filter['zayav_id'] ?
				'<tr><th class="sum">Сумма'.
					'<th>Описание'.
					'<th class="data">Дата'.
					'<th>'
			: '');
	foreach($money as $r)
		$send['spisok'] .= income_unit($r, $filter);
	if($start + $filter['limit'] < $send['all']) {
		$c = $send['all'] - $start - $filter['limit'];
		$c = $c > $filter['limit'] ? $filter['limit'] : $c;
		$send['spisok'] .=
			'<tr class="_next" val="'.($page + 1).'" id="income_next"><td colspan="4">'.
				'<span>Показать ещё '.$c.' платеж'._end($c, '', 'а', 'ей').'</span>';
	}
	$send['spisok'] .= '</table>';
	return $send;
}//income_spisok()
function income_unit($r, $filter=array()) {
	$about = '';
	if($r['zayav_id'] && !$filter['zayav_id'])
		$about .= $r['zayav_type'].' '.$r['zayav_link'].'. ';
	$about .= $r['prim'];
	return
		'<tr val="'.$r['id'].'">'.
			'<td class="sum"><b>'._sumSpace($r['sum']).'</b>'.
			'<td><span class="type">'._invoice($r['invoice_id']).(empty($about) ? '' : ':').'</span> '.$about.
			'<td class="dtime'._tooltip(viewerAdded($r['viewer_id_add']), -20).FullDataTime($r['dtime_add']).
			'<td class="ed"><div class="img_del income-del'._tooltip('Удалить платёж', -95, 'r').'</div>';
}//income_unit()

function expense_right() {
	$workers = query_selJson("
		SELECT
			DISTINCT `m`.`worker_id`,
			CONCAT(`u`.`first_name`,' ',`u`.`last_name`)
	    FROM `gazeta_money` `m`,`vk_user` `u`
	    WHERE `m`.`worker_id`=`u`.`viewer_id`
	      AND `worker_id`>0
	      AND !`m`.`deleted`
	      AND `m`.`sum`<0
	    ORDER BY `u`.`dtime_add`");
	return '<script type="text/javascript">var WORKERS='.($workers ? $workers : '[]').';</script>'.
	'<div class="findHead">Категория</div>'.
	'<input type="hidden" id="category">'.
	'<div class="findHead">Сотрудник</div>'.
	'<input type="hidden" id="worker">'.
	'<div class="findHead">Счёт</div>'.
	'<input type="hidden" id="invoice_id">'.
	'<input type="hidden" id="year">'.
	'<div id="monthList">'.expenseMonthSum().'</div>';
}//expense_right()
function expenseFilter($v) {
	$send = array(
		'page' => !empty($v['page']) && preg_match(REGEXP_NUMERIC, $v['page']) ? $v['page'] : 1,
		'limit' => !empty($v['limit']) && preg_match(REGEXP_NUMERIC, $v['limit']) ? $v['limit'] : 30,
		'category' => !empty($v['category']) && preg_match(REGEXP_NUMERIC, $v['category']) ? $v['category'] : 0,
		'worker' => !empty($v['worker']) && preg_match(REGEXP_NUMERIC, $v['worker']) ? $v['worker'] : 0,
		'invoice_id' => !empty($v['invoice_id']) && preg_match(REGEXP_NUMERIC, $v['invoice_id']) ? $v['invoice_id'] : 0,
		'year' => !empty($v['year']) && preg_match(REGEXP_NUMERIC, $v['year']) ? $v['year'] : strftime('%Y'),
		'month' => isset($v['month']) ? $v['month'] : intval(strftime('%m')),
		'del' => isset($v['del']) && preg_match(REGEXP_BOOL, $v['del']) ? $v['del'] : 0
	);
	$mon = array();
	if(!empty($send['month']))
		foreach(explode(',', $send['month']) as $r)
			$mon[$r] = 1;
	$send['month'] = $mon;
	return $send;
}//expenseFilter()
function expenseMonthSum($v=array()) {
	$filter = expenseFilter($v);
	$sql = "SELECT
				DISTINCT(DATE_FORMAT(`dtime_add`,'%m')) AS `month`,
				SUM(`sum`) AS `sum`
			FROM `gazeta_money`
			WHERE !`deleted`
			  AND `sum`<0
			  AND `dtime_add` LIKE '".$filter['year']."%'".
		($filter['category'] ? " AND `expense_id`=".$filter['category'] : '').
		($filter['worker'] ? " AND `worker_id`=".$filter['worker'] : '').
		($filter['invoice_id'] ? " AND `invoice_id`=".$filter['invoice_id'] : '')."
			GROUP BY DATE_FORMAT(`dtime_add`,'%m')
			ORDER BY `dtime_add` ASC";
	$q = query($sql);
	$res = array();
	while($r = mysql_fetch_assoc($q))
		$res[intval($r['month'])] = abs($r['sum']);
	$send = '';
	for($n = 1; $n <= 12; $n++)
		$send .= _check(
			'c'.$n,
			_monthDef($n).(isset($res[$n]) ? '<span class="sum">'.$res[$n].'</span>' : ''),
			isset($filter['month'][$n]),
			1
		);
	return $send;
}//expenseMonthSum()
function expense() {
	$data = expense_spisok();
	$year = array();
	for($n = 2012; $n <= strftime('%Y'); $n++)
		$year[$n] = $n;
	return
		'<script type="text/javascript">'.
		'var MON_SPISOK='._selJson(_monthDef(0, 1)).','.
			'YEAR_SPISOK='._selJson($year).';'.
		'</script>'.
		'<div class="headName">Список расходов газеты<a class="add">Новый расход</a></div>'.
		'<div id="spisok">'.$data['spisok'].'</div>';
}//expense()
function expense_spisok($filter=array()) {
	$filter = expenseFilter($filter);
	$dtime = array();
	foreach($filter['month'] as $mon => $k)
		$dtime[] = "`dtime_add` LIKE '".$filter['year']."-".($mon < 10 ? 0 : '').$mon."%'";
	$cond = "`deleted`=0
		AND `sum`<0".
		(!empty($dtime) ? " AND (".implode(' OR ', $dtime).")" : '').
		($filter['category'] ? ' AND `expense_id`='.$filter['category'] : '').
		($filter['worker'] ? " AND `worker_id`=".$filter['worker'] : '').
		($filter['invoice_id'] ? " AND `invoice_id`=".$filter['invoice_id'] : '');


	$sql = "SELECT
				COUNT(`id`) AS `all`,
				SUM(`sum`) AS `sum`
			FROM `gazeta_money`
			WHERE ".$cond;
	$send = mysql_fetch_assoc(query($sql));
	$send['filter'] = $filter;
	if(!$send['all'])
		return $send + array('spisok' => '<div class="_empty">Расходов нет.</div>');

	$all = $send['all'];
	$page = $filter['page'];
	$limit = $filter['limit'];
	$start = ($page - 1) * $limit;

	$send['spisok'] = '';
	if($page == 1) {
		$send['spisok'] =
			'<div class="_moneysum">'.
			'Показан'._end($all, 'а', 'о').' <b>'.$all.'</b> запис'._end($all, 'ь', 'и', 'ей').
			' на сумму <b>'.abs($send['sum']).'</b> руб.'.
			(empty($dtime) ? ' за всё время.' : '').
			'</div>'.
			'<table class="_spisok _money">'.
				'<tr><th>Сумма'.
				'<th>Описание'.
				'<th>Дата'.
				'<th>';
	}
	$sql = "SELECT *
			FROM `gazeta_money`
			WHERE ".$cond."
			ORDER BY `dtime_add` DESC
			LIMIT ".$start.",".$limit;
	$q = query($sql);
	$rashod = array();
	while($r = mysql_fetch_assoc($q))
		$rashod[$r['id']] = $r;
	$rashod = _viewer($rashod);
	foreach($rashod as $r) {
		$dtimeTitle = _tooltip(viewerAdded($r['viewer_id_add']), -40);
		//if($r['deleted'])
		//$dtimeTitle .= "\n".'Удалил: '.$r['viewer_del']."\n".FullDataTime($r['dtime_del']);
		$send['spisok'] .= '<tr'.($r['deleted'] ? ' class="deleted"' : '').' val="'.$r['id'].'">'.
			'<td class="sum"><b>'._sumSpace(abs($r['sum'])).'</b>'.
			'<td>'.($r['expense_id'] ? '<span class="type">'._expense($r['expense_id']).($r['prim'] || $r['worker_id'] ? ':' : '').'</span> ' : '').
			($r['worker_id'] ? _viewer($r['worker_id'], 'link').
				($r['prim'] ? ', ' : '') : '').$r['prim'].
			'<td class="dtime'.$dtimeTitle.FullDataTime($r['dtime_add']).
			'<td class="ed r">'.
			//'<div class="img_edit" title="Редактировать"></div>'.
			'<div class="img_del'._tooltip('Удалить расход', -52).'</div>'.
			'<div class="img_rest'._tooltip('Восстановить расход', -67).'</div>';
	}
	if($start + $limit < $all)
		$send['spisok'] .= '<tr class="_next" val="'.($page + 1).'"><td colspan="4"><span>Показать далее...</span>';
	if($page == 1)
		$send['spisok'] .= '</table>';
	return $send;
}//expense_spisok()

function _invoiceBalans($invoice_id, $start=false) {// Получение текущего баланса счёта
	if($start === false)
		$start = _invoice($invoice_id, 'start');
	$income = query_value("SELECT IFNULL(SUM(`sum`),0) FROM `gazeta_money` WHERE !`deleted` AND `invoice_id`=".$invoice_id);
	$from = query_value("SELECT IFNULL(SUM(`sum`),0) FROM `gazeta_invoice_transfer` WHERE `invoice_from`=".$invoice_id);
	$to = query_value("SELECT IFNULL(SUM(`sum`),0) FROM `gazeta_invoice_transfer` WHERE `invoice_to`=".$invoice_id);
	return round($income - $start - $from + $to, 2);
}//_invoiceBalans()
function invoice() {
	return
		'<div class="headName">'.
			'Счета'.
			'<a class="add transfer">Перевод между счетами</a>'.
			'<span>::</span>'.
			'<a href="'.URL.'&p=gazeta&d=setup&d1=invoice" class="add">Управление счетами</a>'.
		'</div>'.
		'<div id="invoice-spisok">'.invoice_spisok().'</div>'.
		'<div class="headName">История переводов</div>'.
		'<div id="transfer-spisok">'.transfer_spisok().'</div>';
}//invoice()
function invoice_spisok() {
	$invoice = _invoice();
	if(empty($invoice))
		return 'Счета не определены.';

	$send = '<table class="_spisok">';
	foreach($invoice as $r)
		$send .= '<tr>'.
			'<td class="name"><b>'.$r['name'].'</b><pre>'.$r['about'].'</pre>'.
			($r['start'] != -1 ?
				'<td class="balans"><b>'._sumSpace(_invoiceBalans($r['id'])).'</b> руб.'.
				'<td><div val="'.$r['id'].'" class="img_note'._tooltip('Посмотреть историю операций', -95).'</div>'
			: '').
	//		(GAZETA_ADMIN || $r['start'] != -1 ?
				'<td><a class="invoice_set" val="'.$r['id'].'">Установить<br />текущую сумму</a>';
	//		: '');
	$send .= '</table>';
	return $send;
}//invoice_spisok()
function transfer_spisok() {
	$sql = "SELECT * FROM `gazeta_invoice_transfer` ORDER BY `id` DESC";
	$q = query($sql);
	$send = '<table class="_spisok _money">'.
		'<tr><th>Cумма'.
			'<th>Со счёта'.
			'<th>На счёт'.
			'<th>Комментарий'.
			'<th>Дата';
	while($r = mysql_fetch_assoc($q))
		$send .=
			'<tr>'.
			'<td class="sum">'._sumSpace($r['sum']).
			'<td>'.($r['invoice_from'] ? '<span class="type">'._invoice($r['invoice_from']).'</span>' : '').
			($r['worker_from'] && $r['invoice_from'] ? '<br />' : '').
			($r['worker_from'] ? _viewer($r['worker_from'], 'name') : '').
			'<td>'.($r['invoice_to'] ? '<span class="type">'._invoice($r['invoice_to']).'</span>' : '').
			($r['worker_to'] && $r['invoice_to'] ? '<br />' : '').
			($r['worker_to'] ? _viewer($r['worker_to'], 'name') : '').
			'<td class="about">'.$r['note'].
			'<td class="dtime">'.FullDataTime($r['dtime_add'], 1);
	$send .= '</table>';
	return $send;
}//transfer_spisok()
function invoiceHistoryAction($id, $i='name') {//Варианты действий в истории счетов
	$action = array(
		1 => array(
			'name' => 'Внесение платежа',
			'znak' => '',
			'cash' => 1 //Учитывать внутренние счета при внесении
		),
		2 => array(
			'name' => 'Удаление платежа',
			'znak' => '-',
			'cash' => 1
		),
		3 => array(
			'name' => 'Восстановление платежа',
			'znak' => '',
			'cash' => 1
		),
		4 => array(
			'name' => 'Перевод между счетами',
			'znak' => '',
			'cash' => 0
		),
		5 => array(
			'name' => 'Установка текущей суммы',
			'znak' => '',
			'cash' => 0
		),
		6 => array(
			'name' => 'Внесение расхода',
			'znak' => '-',
			'cash' => 1
		),
		7 => array(
			'name' => 'Удаление расхода',
			'znak' => '',
			'cash' => 1
		),
		8 => array(
			'name' => 'Восстановление расхода',
			'znak' => '-',
			'cash' => 1
		),
		9 => array(
			'name' => 'Редактирование расхода',
			'znak' => '',
			'cash' => 0
		),
		10 => array(
			'name' => 'Изменение платежа',
			'znak' => '',
			'cash' => 1
		),
		11 => array(
			'name' => 'Подтверждение платежа',
			'znak' => '',
			'cash' => 1
		)
	);
	return $action[$id][$i];
}//invoiceHistoryAction()
function invoice_history($v) {
	$v = array(
		'page' => !empty($v['page']) && preg_match(REGEXP_NUMERIC, $v['page']) ? $v['page'] : 1,
		'limit' => !empty($v['limit']) && preg_match(REGEXP_NUMERIC, $v['limit']) ? $v['limit'] : 15,
		'invoice_id' => intval($v['invoice_id'])
	);
	$invoice = _invoice($v['invoice_id']);
	$send = '';
	if($v['page'] == 1)
		$send = '<div>Счёт <u>'.$invoice.'</u>:</div>'.
			'<input type="hidden" id="invoice_history_id" value="'.$v['invoice_id'].'" />';

	$all = query_value("SELECT COUNT(*) FROM `gazeta_invoice_history` WHERE `invoice_id`=".$v['invoice_id']);
	if(!$all)
		return $send.'<br />Истории нет.';

	$start = ($v['page'] - 1) * $v['limit'];
	$sql = "SELECT `h`.*,
				   IFNULL(`m`.`zayav_id`,0) AS `zayav_id`,
				   IFNULL(`m`.`expense_id`,0) AS `expense_id`,
				   IFNULL(`m`.`worker_id`,0) AS `worker_id`,
				   IFNULL(`m`.`prim`,'') AS `prim`,
				   IFNULL(`i`.`invoice_from`,0) AS `invoice_from`,
				   IFNULL(`i`.`invoice_to`,0) AS `invoice_to`,
				   IFNULL(`i`.`worker_from`,0) AS `worker_from`,
				   IFNULL(`i`.`worker_to`,0) AS `worker_to`
			FROM `gazeta_invoice_history` `h`
				LEFT JOIN `gazeta_money` `m`
				ON `h`.`table`='gazeta_money' AND `h`.`table_id`=`m`.`id`
				LEFT JOIN `gazeta_invoice_transfer` `i`
				ON `h`.`table`='gazeta_invoice_transfer' AND `h`.`table_id`=`i`.`id`
			WHERE `h`.`invoice_id`=".$v['invoice_id']."
			ORDER BY `h`.`id` DESC
			LIMIT ".$start.",".$v['limit'];
	$q = query($sql);
	$history = array();
	while($r = mysql_fetch_assoc($q))
		$history[$r['id']] = $r;

	$history = _zayavLink($history);

	if($v['page'] == 1)
		$send .= '<table class="_spisok _money invoice-history">'.
			'<tr><th>Действие'.
				'<th>Сумма'.
				'<th>Баланс'.
				'<th>Описание'.
				'<th>Дата';
	foreach($history as $r) {
		$about = '';
		if($r['zayav_id'])
			$about = $r['zayav_type'].' '.$r['zayav_link'];
		$worker = $r['worker_id'] ? _viewer($r['worker_id'], 'link') : '';
		$expense = $r['expense_id'] ? '<span class="type">'._expense($r['expense_id']).(!$about && !$worker ? '' : ': ').'</span>' : '';
		$about .= (($expense || $expense) && $r['prim'] ? ', ' : '').$r['prim'];
		if($r['invoice_from'] != $r['invoice_to']) {//Счета не равны, перевод внешний
			if(!$r['invoice_to'])//Деньги были переданы руководителю
				$about .= 'Передача сотруднику '._viewer($r['worker_to'], 'name');
			elseif(!$r['invoice_from'])//Деньги были получены от руководителя
				$about .= 'Получение от сотрудника '._viewer($r['worker_from'], 'name');
			elseif($r['invoice_id'] == $r['invoice_from'])//Просматриваемый счёт общий - оправитель
				$about .= 'Отправление на счёт <span class="type">'._invoice($r['invoice_to']).'</span>'.
					($r['worker_to'] ? ' '._viewer($r['worker_to'], 'name') : '').
					($r['worker_from'] ? ' со счёта <span class="type">'._invoice($r['invoice_from']).'</span> '._viewer($r['worker_from'], 'name') : '');
			elseif($r['invoice_id'] == $r['invoice_to'])//Просматриваемый счёт общий - получатель
				$about .= 'Поступление со счёта <span class="type">'._invoice($r['invoice_from']).'</span>'.
					($r['worker_from'] ? ' '._viewer($r['worker_from'], 'name') : '').
					($r['worker_to'] ? ' на счёт <span class="type">'._invoice($r['invoice_to']).'</span> '._viewer($r['worker_to'], 'name') : '');
			elseif($r['invoice_id'] == $r['worker_from'])//Просматриваемый счёт сотрудника - оправитель
				$about .= 'Отправление на счёт <span class="type">'._invoice($r['invoice_to']).'</span>';
			elseif($r['invoice_id'] == $r['worker_to'])//Просматриваемый счёт сотрудника - оправитель
				$about .= 'Поступление со счёта <span class="type">'._invoice($r['invoice_from']).'</span>';
		} else {//Счета равны, перевод внутренний
			if($r['invoice_id'] == $r['worker_from'])//Просматриваемый счёт сотрудника - оправитель
				$about .= 'Отправление на счёт <span class="type">'._invoice($r['invoice_to']).'</span> '._viewer($r['worker_to'], 'name');
			if($r['invoice_id'] == $r['worker_to'])//Просматриваемый счёт сотрудника - получатель
				$about .= 'Поступление со счёта <span class="type">'._invoice($r['invoice_from']).'</span> '._viewer($r['worker_from'], 'name');
		}
		$sum = '';
		if($r['sum_prev'] != 0)
			$sum = _sumSpace($r['sum'] - $r['sum_prev']).
				'<div class="sum-change">('.round($r['sum_prev'], 2).' &raquo; '.round($r['sum'], 2).')</div>';
		elseif($r['sum'] != 0)
			$sum = _sumSpace($r['sum']);
		$send .=
			'<tr><td class="action">'.invoiceHistoryAction($r['action']).
				'<td class="sum">'.$sum.
				'<td class="balans">'._sumSpace($r['balans']).
				'<td>'.$expense.$worker.$about.
				'<td class="dtime">'.FullDataTime($r['dtime_add']);
	}

	if($start + $v['limit'] < $all) {
		$c = $all - $start - $v['limit'];
		$c = $c > $v['limit'] ? $v['limit'] : $c;
		$send .=
			'<tr class="_next" val="'.($v['page'] + 1).'"><td colspan="5">'.
			'<span>Показать ещё '.$c.' запис'._end($c, 'ь', 'и', 'ей').'</span>';
	}
	if($v['page'] == 1)
		$send .= '</table>';
	return $send;
}//invoice_history()
function invoice_history_insert($v) {
	$v = array(
		'action' => $v['action'],
		'table' => empty($v['table']) ? '' : $v['table'],
		'id' => empty($v['id']) ? 0 : $v['id'],
		'sum' => empty($v['sum']) ? 0 : $v['sum'],
		'sum_prev' => empty($v['sum_prev']) ? 0 : $v['sum_prev'],
		'worker_id' => empty($v['worker_id']) ? 0 : $v['worker_id'],
		'invoice_id' => empty($v['invoice_id']) ? 0 : $v['invoice_id']
	);

	if($v['table']) {
		$r = query_assoc("SELECT * FROM `".$v['table']."` WHERE `id`=".$v['id']);
		$v['sum'] = abs($r['sum']);
		switch($v['table']) {
			case 'gazeta_money':
				$v['invoice_id'] = $r['invoice_id'];
				$v['sum'] = invoiceHistoryAction($v['action'], 'znak').$v['sum'];
				break;
			case 'gazeta_invoice_transfer':
				if($r['invoice_from'] && $r['invoice_to'] && $r['invoice_from'] == $r['invoice_to']) {//внутренний перевод
					$v['invoice_id'] = $r['worker_from'];
					invoice_history_insert_sql($r['worker_to'], $v);
					$v['sum'] *= -1;
					break;
				}
				if(!$r['invoice_from'] && !$r['invoice_to'])
					return;
				if(!$r['invoice_from']) {//взятие средств у руководителя
					$v['invoice_id'] = $r['invoice_to'];
					if($r['worker_to'])
						invoice_history_insert_sql($r['worker_to'], $v);
					break;
				}
				if(!$r['invoice_to']) {//передача средств руководителю
					$v['invoice_id'] = $r['invoice_from'];
					$v['sum'] *= -1;
					if($r['worker_from'])
						invoice_history_insert_sql($r['worker_from'], $v);
					break;
				}
				//Передача из банка в наличные и на счета сотрудников
				$v['invoice_id'] = $r['invoice_from'];
				invoice_history_insert_sql($r['invoice_to'], $v);
				if($r['worker_from']) {
					$v['sum'] *= -1;
					invoice_history_insert_sql($r['worker_from'], $v);
					$v['sum'] *= -1;
				}
				if($r['worker_to'])
					invoice_history_insert_sql($r['worker_to'], $v);
				$v['sum'] *= -1;
				break;
		}
	}
	invoice_history_insert_sql($v['invoice_id'], $v);
}//invoice_history_insert()
function invoice_history_insert_sql($invoice_id, $v) {
	if(_invoice($invoice_id, 'start') == -1)
		return;
	$sql = "INSERT INTO `gazeta_invoice_history` (
				`action`,
				`table`,
				`table_id`,
				`invoice_id`,
				`sum`,
				`sum_prev`,
				`balans`,
				`viewer_id_add`
			) VALUES (
				".$v['action'].",
				'".$v['table']."',
				".$v['id'].",
				".$invoice_id.",
				".$v['sum'].",
				".$v['sum_prev'].",
				"._invoiceBalans($invoice_id).",
				".VIEWER_ID."
			)";
	query($sql);
}

function report_zayav_nomer($nomer=GN_FIRST_ACTIVE) {//отчёт о заявках по номеру газеты
	$sql = "SELECT
 				COUNT(`id`) `count`,
 				SUM(`cena`) `sum`
 			FROM `gazeta_nomer_pub`
 			WHERE `general_nomer`=".$nomer;
	$all = query_assoc($sql);

	//Объявления
	$sql = "SELECT
 				COUNT(`pub`.`id`) `count`,
 				SUM(`pub`.`cena`) `sum`
 			FROM `gazeta_nomer_pub` `pub`,
 				 `gazeta_zayav` `z`
 			WHERE `z`.`id`=`pub`.`zayav_id`
 			  AND `z`.`category`=1
 			  AND `pub`.`general_nomer`=".$nomer;
	$ob = query_assoc($sql);

	//Реклама
	$sql = "SELECT
 				COUNT(`pub`.`id`) `count`,
 				SUM(`pub`.`cena`) `sum`
 			FROM `gazeta_nomer_pub` `pub`,
 				 `gazeta_zayav` `z`
 			WHERE `z`.`id`=`pub`.`zayav_id`
 			  AND `z`.`category`=2
 			  AND `pub`.`general_nomer`=".$nomer;
	$rek = query_assoc($sql);

	//Поздравления
	$sql = "SELECT
 				COUNT(`pub`.`id`) `count`,
 				SUM(`pub`.`cena`) `sum`
 			FROM `gazeta_nomer_pub` `pub`,
 				 `gazeta_zayav` `z`
 			WHERE `z`.`id`=`pub`.`zayav_id`
 			  AND `z`.`category`=3
 			  AND `pub`.`general_nomer`=".$nomer;
	$poz = query_assoc($sql);

	//Статьи
	$sql = "SELECT
 				COUNT(`pub`.`id`) `count`,
 				SUM(`pub`.`cena`) `sum`
 			FROM `gazeta_nomer_pub` `pub`,
 				 `gazeta_zayav` `z`
 			WHERE `z`.`id`=`pub`.`zayav_id`
 			  AND `z`.`category`=4
 			  AND `pub`.`general_nomer`=".$nomer;
	$st = query_assoc($sql);

	$polosaRek = '';
	$pc = _gn(470, 'pc');
	for($n = 1; $n <= $pc; $n++) {
		$sql = "SELECT
 				COUNT(`pub`.`id`) `count`,
 				SUM(`pub`.`cena`) `sum`,
 				SUM(`z`.`size_x`*`z`.`size_y`) `size`
 			FROM `gazeta_nomer_pub` `pub`,
 				 `gazeta_zayav` `z`
 			WHERE `z`.`id`=`pub`.`zayav_id`
 			  AND `z`.`category`=2
 			  AND ".($n == 1 ? "`dop`=1" :
						($n == $pc ? "`dop`=2" : "`polosa`=".$n)
					)."
 			  AND `pub`.`general_nomer`=".$nomer;
		$r = query_assoc($sql);

		$polosaRek .= '<tr>'.
			'<td>'.$n.
			'<td><center>'.($r['count'] ? $r['count'] :'').'</center>'.
			'<td class="r">'.($r['count'] ? round($r['sum'], 2).' руб.' :'').
			'<td class="r">'.($r['count'] ? round($r['size']).' см&sup2' :'');
	}

	//полоса не указана
	$sql = "SELECT
 				COUNT(`pub`.`id`) `count`,
 				SUM(`pub`.`cena`) `sum`,
 				SUM(`z`.`size_x`*`z`.`size_y`) `size`
 			FROM `gazeta_nomer_pub` `pub`,
 				 `gazeta_zayav` `z`
 			WHERE `z`.`id`=`pub`.`zayav_id`
 			  AND `z`.`category`=2
 			  AND `pub`.`dop`!=1
 			  AND `pub`.`dop`!=2
 			  AND !`pub`.`polosa`
 			  AND `pub`.`general_nomer`=".$nomer;
	$r = query_assoc($sql);

	$polosaRek .= '<tr>'.
		'<td>-'.
		'<td><center>'.($r['count'] ? $r['count'] :'').'</center>'.
		'<td class="r">'.($r['count'] ? round($r['sum'], 2).' руб.' :'').
		'<td class="r">'.($r['count'] ? round($r['size']).' см&sup2' :'');


	return
		'<div class="headName">Отчёт по заявкам за номер '._gn($nomer, 'week').' ('.$nomer.'):</div>'.
		'<table class="_spisok">'.
			'<tr><th><th>Количество<th>Стоимость'.
			'<tr><td><b>Всего</b>:<td><center><b>'.$all['count'].'</b></center><td class="r"><b>'.round($all['sum'], 2).'<b>'.
			'<tr><td>Объявления:<td><center>'.$ob['count'].'</center><td class="r">'.round($ob['sum'], 2).
			'<tr><td>Реклама:<td><center>'.$rek['count'].'</center><td class="r">'.round($rek['sum'], 2).
			'<tr><td>Поздравления:<td><center>'.$poz['count'].'</center><td class="r">'.round($poz['sum'], 2).
			'<tr><td>Статьи:<td><center>'.$st['count'].'</center><td class="r">'.round($st['sum'], 2).
		'</table>'.
		'<br />'.
		'<div class="headName">Реклама по полосам:</div>'.
		'<table class="_spisok">'.
			'<tr><th>'.
				'<th>Количество'.
				'<th>Стоимость'.
				'<th>Площадь'.
			$polosaRek.
		'</table>';
}//report_zayav_nomer()

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
		'invoice' => 'Счета',
		'skidka' => 'Скидки',
		'expense' => 'Категории расходов'
	);

	if(!GAZETA_ADMIN)
		unset($pages['worker']);

	$d1 = empty($_GET['d1']) ? $pageDef : $_GET['d1'];
	if(empty($_GET['d1']) && !empty($pages) && empty($pages[$d1])) {
		foreach($pages as $p => $name) {
			$d1 = $p;
			break;
		}
	}

	switch($d1) {
		default: $d1 = $pageDef;
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
		case 'invoice': $left = setup_invoice(); break;
		case 'skidka': $left = setup_skidka(); break;
		case 'expense': $left = setup_expense(); break;
	}
	$links = '';
	if($pages)
		foreach($pages as $p => $name)
			$links .= '<a href="'.URL.'&p=gazeta&d=setup&d1='.$p.'"'.($d1 == $p ? ' class="sel"' : '').'>'.$name.'</a>';
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
	$sql = "SELECT *
			FROM `gazeta_nomer`
			WHERE `day_public` LIKE '".$y."-%'
			ORDER BY `general_nomer`";
	$q = query($sql);
	if(!mysql_num_rows($q))
		return 'Номера газет, которые будут выходить в '.$y.' году, не определены.'.
			'<div class="vkButton"><button>Создать список</button></div>';
	$send =
		'<a id="gn-clear" val="'.$y.'">Очистить список за '.$y.' год</a>'.
		'<table class="_spisok">'.
			'<tr><th>Номер<br />выпуска'.
				'<th>День отправки<br />в печать'.
				'<th>День выхода'.
				'<th>Кол-во<br />полос'.
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
			'<td class="pc">'.$r['polosa_count'].
			'<td class="ed"><div class="img_edit"></div><div class="img_del"></div>';
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
				'<th class="pn">Указывать<br />номер<br />полосы'.
				'<th class="set">'.
		'</table>'.
		'<dl class="_sort" val="setup_polosa_cost">';
	while($r = mysql_fetch_assoc($q))
		$send .='<dd val="'.$r['id'].'">'.
			'<table class="_spisok">'.
				'<tr><td class="name">'.$r['name'].
					'<td class="cena">'.round($r['cena'], 2).
					'<td class="pn">'.($r['polosa'] ? 'да' : '').
					'<td class="set"><div class="img_edit"></div>'.
			'</table>';
	$send .= '</dl>';
	return $send;
}//setup_polosa_spisok()

function setup_invoice() {
	return
		'<div id="setup_invoice">'.
			'<div class="headName">Управление счетами<a class="add">Новый счёт</a></div>'.
			'<div class="spisok">'.setup_invoice_spisok().'</div>'.
		'</div>';
}//setup_invoice()
function setup_invoice_spisok() {
	$sql = "SELECT * FROM `gazeta_invoice` ORDER BY `id`";
	$q = query($sql);
	if(!mysql_num_rows($q))
		return 'Список пуст.';

	$spisok = array();
	while($r = mysql_fetch_assoc($q))
		$spisok[$r['id']] = $r;

	$send =
		'<table class="_spisok">'.
			'<tr><th class="name">Наименование'.
				'<th class="set">';
	foreach($spisok as $id => $r)
		$send .=
		'<tr val="'.$id.'">'.
			'<td class="name">'.
				'<div>'.$r['name'].'</div>'.
				'<pre>'.$r['about'].'</pre>'.
			'<td class="set">'.
				'<div class="img_edit"></div>';
	$send .= '</table>';
	return $send;
}//setup_invoice_spisok()

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

function setup_expense() {
	return
	'<div id="setup_expense">'.
		'<div class="headName">Категории расходов<a class="add">Добавить</a></div>'.
		'<div id="spisok">'.setup_expense_spisok().'</div>'.
	'</div>';
}//setup_expense()
function setup_expense_spisok() {
	$sql = "SELECT `r`.*,
				   COUNT(`m`.`id`) AS `count`
			FROM `setup_expense` AS `r`
			  LEFT JOIN `gazeta_money` AS `m`
			  ON `r`.`id`=`m`.`expense_id` AND `m`.`deleted`=0
			GROUP BY `r`.`id`
			ORDER BY `r`.`sort`";
	$q = query($sql);
	if(!mysql_num_rows($q))
		return 'Список пуст.';

	$send =
	'<table class="_spisok">'.
		'<tr><th class="name">Наименование'.
			'<th class="worker">Показывать<br />список<br />сотрудников'.
			'<th class="opl">Кол-во<br />платежей'.
			'<th class="set">'.
	'</table>'.
	'<dl class="_sort" val="setup_expense">';
	while($r = mysql_fetch_assoc($q))
		$send .='<dd val="'.$r['id'].'">'.
			'<table class="_spisok">'.
				'<tr><td class="name">'.$r['name'].
					'<td class="worker">'.($r['show_worker'] ? 'да' : '').
					'<td class="opl">'.($r['count'] ? $r['count'] : '').
					'<td class="set">'.
						'<div class="img_edit"></div>'.
						(!$r['count'] ? '<div class="img_del"></div>' : '').
			'</table>';
	$send .= '</dl>';
	return $send;
}//setup_expense_spisok()
