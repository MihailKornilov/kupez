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

function client_info($client_id) {
	$sql = "SELECT * FROM `client` WHERE `status`=1 AND `id`=".$client_id;
	if(!$client = mysql_fetch_assoc(query($sql)))
		return _noauth('Клиента не существует');

	$zamer = zamer_spisok(1, array('client'=>$client_id), 10);
	$commCount = query_value("SELECT COUNT(`id`)
							  FROM `vk_comment`
							  WHERE `status`=1
								AND `parent_id`=0
								AND `table_name`='client'
								AND `table_id`=".$client_id);

	$sql = "SELECT * FROM `money` WHERE `status`=1 AND `client_id`=".$client_id;
	$q = query($sql);
	$moneyCount = mysql_num_rows($q);
	$money = '<div class="_empty">Платежей нет.</div>';
	if($moneyCount) {
		$money = '<table class="_spisok _money">'.
			'<tr><th class="sum">Сумма'.
			'<th>Описание'.
			'<th class="data">Дата';
		while($r = mysql_fetch_assoc($q)) {
			$about = '';
			if($r['zayav_id'] > 0)
				$about .= 'Заявка '.$r['zayav_id'].'. ';
			$about .= $r['prim'];
			$money .= '<tr><td class="sum"><b>'.$r['sum'].'</b>'.
				'<td>'.$about.
				'<td class="dtime" title="Внёс: '._viewer($r['viewer_id_add'], 'name').'">'.FullDataTime($r['dtime_add']);
		}
		$money .= '</table>';
	}
	// $remindData = remind_data(1, array('client'=>$client_id));

	$histCount = query_value("SELECT COUNT(`id`) FROM `history` WHERE `client_id`=".$client_id);

	return
		'<script type="text/javascript">'.
		'var CLIENT={'.
		'id:'.$client_id.','.
		'fio:"'.$client['fio'].'",'.
		'telefon:"'.$client['telefon'].'",'.
		'adres:"'.$client['adres'].'",'.
		'pasp_seria:"'.$client['pasp_seria'].'",'.
		'pasp_nomer:"'.$client['pasp_nomer'].'",'.
		'pasp_adres:"'.$client['pasp_adres'].'",'.
		'pasp_ovd:"'.$client['pasp_ovd'].'",'.
		'pasp_data:"'.$client['pasp_data'].'"'.
		'};'.
		'</script>'.
		'<div id="clientInfo">'.
		'<table class="tabLR">'.
		'<tr><td class="left">'.clientInfoGet($client).
		'<td class="right">'.
		'<div class="rightLink">'.
		'<a class="sel">Информация</a>'.
		'<a class="cedit">Редактировать</a>'.
		'<a class="zamer_add"><b>Новый замер</b></a>'.
		'<a class="cdel">Удалить клиента</a>'.
		'</div>'.
		'</table>'.

		'<div id="dopLinks">'.
		'<a class="link sel" val="zayav">Заявки'.($zamer['all'] ? ' ('.$zamer['all'].')' : '').'</a>'.
		'<a class="link" val="money">Платежи'.($moneyCount ? ' ('.$moneyCount.')' : '').'</a>'.
		//'<a class="link" val="remind">Задания'.(!empty($remindData) ? ' ('.$remindData['all'].')' : '').'</a>'.
		'<a class="link" val="comm">Заметки'.($commCount ? ' ('.$commCount.')' : '').'</a>'.
		'<a class="link" val="hist">История'.($histCount ? ' ('.$histCount.')' : '').'</a>'.
		'</div>'.

		'<table class="tabLR">'.
		'<tr><td class="left">'.
		'<div id="zayav_spisok">'.$zamer['spisok'].'</div>'.
		'<div id="money_spisok">'.$money.'</div>'.
		'<div id="remind_spisok">'.(!empty($remindData) ? report_remind_spisok($remindData) : '<div class="_empty">Заданий нет.</div>').'</div>'.
		'<div id="comments">'._vkComment('client', $client_id).'</div>'.
		'<div id="histories">'.report_history_spisok(1, array('client_id'=>$client_id)).'</div>'.
		'<td class="right">'.
		'<div id="zayav_filter">'.
		//'<div id="zayav_result">'.zayav_count($zayavData['all'], 0).'</div>'.
		//'<div class="findHead">Статус заявки</div>'.
		//_rightLink('status', _zayavStatusName()).
		'</div>'.
		'</table>'.
		'</div>';
}//client_info()



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
		case 'gn': $left = ''; break;
		case 'person': $left = setup_person(); break;
		case 'rubric':
			if(preg_match(REGEXP_NUMERIC, @$_GET['id'])) {
				$left = setup_rubric_sub(intval($_GET['id']));
				break;
			}
			$left = setup_rubric();
			break;
		case 'oblen': $left = setup_oblen(); break;
		case 'obdop': $left = ''; break;
		case 'polosa': $left = ''; break;
		case 'money': $left = setup_money(); break;
		case 'skidka': $left = ''; break;
		case 'rashod': $left = ''; break;
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
