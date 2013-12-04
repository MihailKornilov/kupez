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


function GvaluesCreate() {// составление файла G_values.js
	$save = //'function _toSpisok(s){var a=[];for(k in s)a.push({uid:k,title:s[k]});return a}'.
		//'function _toAss(s){var a=[];for(var n=0;n<s.length;a[s[n].uid]=s[n].title,n++);return a}'.

	'var CATEGORY_SPISOK=[{uid:1,title:"Объявление"},{uid:2,title:"Реклама"},{uid:3,title:"Поздравление"},{uid:4,title:"Статья"}],'.
    "\n".'PERSON_SPISOK='.query_selJson("SELECT `id`,`name` FROM `setup_person` ORDER BY `sort`").
	"\n".'SKIDKA_SPISOK='.query_selJson('SELECT `razmer`,CONCAT(`razmer`,"%") FROM `setup_skidka` ORDER BY `id`').';';

	/*	$save .= "\nG.rubrika_spisok = ".$VK->vkSelJson('SELECT `id`,`name` FROM `setup_rubrika` ORDER BY `sort`').";G.rubrika_ass = SpisokToAss(G.rubrika_spisok);";
		$save .= "\nG.money_type_spisok = ".$VK->vkSelJson('SELECT `id`,`name` FROM `setup_money_type` ORDER BY `sort`').";G.money_type_ass = SpisokToAss(G.money_type_spisok);";
		$save .= "\nG.polosa_spisok = ".$VK->vkSelJson('SELECT `id`,`name` FROM `setup_polosa_cost` ORDER BY `sort`').";G.polosa_ass = SpisokToAss(G.polosa_spisok);";
		$save .= "\nG.polosa_cena_ass = ".$VK->ptpJson('SELECT `id`,`cena` FROM `setup_polosa_cost` ORDER BY `id`').";G.polosa_cena_ass[0] = 0;";
		$save .= "\nG.ob_dop_spisok = ".$VK->vkSelJson('SELECT `id`,`name` FROM `setup_ob_dop` ORDER BY `id`').";G.ob_dop_ass = SpisokToAss(G.ob_dop_spisok);";
		$save .= "\nG.ob_dop_cena_ass = ".$VK->ptpJson('SELECT `id`,`cena` FROM `setup_ob_dop` ORDER BY `id`').";G.ob_dop_cena_ass[0] = 0;";
		$save .= "\nG.skidka_spisok = ".$VK->vkSelJson('SELECT `razmer`,CONCAT(`razmer`,"%") FROM `setup_skidka` ORDER BY `id`').";G.skidka_ass = SpisokToAss(G.skidka_spisok);";
		$save .= "\nG.rashod_category_spisok = ".$VK->vkSelJson('SELECT `id`,`name` FROM `setup_rashod_category` ORDER BY `id`').";G.rashod_category_ass = SpisokToAss(G.rashod_category_spisok);";
		$save .= "\nG.txt_len_first = ".$VK->QRow("SELECT `txt_len_first` FROM `setup_global` LIMIT 1").";";
		$save .= "\nG.txt_cena_first = ".$VK->QRow("SELECT `txt_cena_first` FROM `setup_global` LIMIT 1").";";
		$save .= "\nG.txt_len_next = ".$VK->QRow("SELECT `txt_len_next` FROM `setup_global` LIMIT 1").";";
		$save .= "\nG.txt_cena_next = ".$VK->QRow("SELECT `txt_cena_next` FROM `setup_global` LIMIT 1").";";

		$spisok = $VK->QueryObjectArray("SELECT * FROM `gazeta_nomer` ORDER BY `general_nomer`");
		if (count($spisok) > 0) {
			$gn = array();
			foreach ($spisok as $sp) {
				array_push($gn, "\n".$sp->general_nomer.':{'.
					'week:'.$sp->week_nomer.','.
					'public:"'.$sp->day_public.'",'.
					'txt:"'.FullData($sp->day_public, 0, 1).'"'.
					'}');
			}
			$save .= "\nG.gn = {".implode(',', $gn)."};";
		}

		$spisok = $VK->QueryObjectArray("SELECT `id`,`name`,`rubrika_id` FROM `setup_pod_rubrika` ORDER BY `rubrika_id`,`sort`");
		$podrubrika = array();
		if (count($spisok) > 0) {
			foreach ($spisok as $sp) {
				if (!isset($podrubrika[$sp->rubrika_id])) { $podrubrika[$sp->rubrika_id] = array(); }
				array_push($podrubrika[$sp->rubrika_id], '{uid:'.$sp->id.',title:"'.$sp->name.'"}');
			}
			$v = array();
			foreach ($podrubrika as $n => $sp) { array_push($v, $n.":[".implode(',',$sp)."]\n"); }
			$podrubrika = $v;
		}
		$save .= "\nG.podrubrika_spisok = {".implode(',',$podrubrika)."};";
		$save .= "\nG.podrubrika_ass = []; G.podrubrika_ass[0] = ''; for (var k in G.podrubrika_spisok) { for (var n = 0; n < G.podrubrika_spisok[k].length; n++) { var sp = G.podrubrika_spisok[k][n]; G.podrubrika_ass[sp.uid] = sp.title; } }";

		$save .= "\nG.countries_spisok = [{uid:1,title:'Россия'},{uid:2,title:'Украина'},{uid:3,title:'Беларусь'},{uid:4,title:'Казахстан'},{uid:5,title:'Азербайджан'},{uid:6,title:'Армения'},{uid:7,title:'Грузия'},{uid:8,title:'Израиль'},{uid:11,title:'Кыргызстан'},{uid:12,title:'Латвия'},{uid:13,title:'Литва'},{uid:14,title:'Эстония'},{uid:15,title:'Молдова'},{uid:16,title:'Таджикистан'},{uid:17,title:'Туркмения'},{uid:18,title:'Узбекистан'}];";
	*/
	$fp = fopen(PATH.'/js/G_values.js', 'w+');
	fwrite($fp, $save);
	fclose($fp);

	query("UPDATE `setup_global` SET `g_values`=`g_values`+1");
	xcache_unset(CACHE_PREFIX.'setup_global');
} // end of GvaluesCreate()



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

	);

	if(!RULES_WORKER)
		unset($pages['worker']);

	$d = empty($_GET['d']) ? $pageDef : $_GET['d'];
	if(empty($_GET['d']) && !empty($pages) && empty($pages[$d])) {
		foreach($pages as $p => $name) {
			$d = $p;
			break;
		}
	}

	switch($d) {
		default: $d = $pageDef;
		case 'worker':
			if(preg_match(REGEXP_NUMERIC, @$_GET['id'])) {
				$left = setup_worker_rules(intval($_GET['id']));
				break;
			}
			$left = setup_worker();
			break;
	}
	$links = '';
	if($pages)
		foreach($pages as $p => $name)
			$links .= '<a href="'.URL.'&p=setup&d='.$p.'"'.($d == $p ? ' class="sel"' : '').'>'.$name.'</a>';
	return
	'<div id="setup">'.
		'<table class="tabLR">'.
			'<tr><td class="left">'.$left.
				'<td class="right"><div class="rightLink">'.$links.'</div>'.
		'</table>'.
	'</div>';
}//setup()