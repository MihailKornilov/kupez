<?php
require_once('../config.php');

if(empty($_GET['gn']) || !preg_match(REGEXP_NUMERIC, $_GET['gn']) || !$_GET['gn'])
	wordPrint('Получен некорректный номер газеты');

$gn = intval($_GET['gn']);

$sql = "SELECT
		  `rub`.`name` AS `rub`,
		  IFNULL(`sub`.`name`,'') AS `sub`,
		  `z`.`txt` AS `txt`,
		  `z`.`telefon` AS `telefon`,
		  `z`.`adres` AS `adres`,
		  IFNULL(`dop`.`name`,'') AS `dop`
		FROM `gazeta_nomer_pub` AS `pub`
			LEFT JOIN `gazeta_zayav` AS `z` ON `pub`.`zayav_id`=`z`.`id`
			LEFT JOIN `setup_rubric` AS `rub` ON `z`.`rubric_id`=`rub`.`id`
			LEFT JOIN `setup_rubric_sub` AS `sub` ON `z`.`rubric_sub_id`=`sub`.`id`
			LEFT JOIN `setup_ob_dop` AS `dop` ON `pub`.`dop`=`dop`.`id`
		WHERE `pub`.`general_nomer`=".$gn."
		  AND `z`.`category`=1
		ORDER BY
		    `rub`.`sort`,
		    `sub`.`sort`,
		    `z`.`txt`";
$q = query($sql);
if(!mysql_num_rows($q))
	wordPrint('Нет объявлений для номера '.$gn);

$word = 'Список объявлений для номера '.$gn.':';  // Составление объявлений для отправки
$rub = '';   // Контроль рубрик
$sub = '';// Контроль подрубрик
while($r = mysql_fetch_assoc($q)) {
	// Если рубрика изменилась, то печать
	if ($rub != $r['rub']) {
		$rub = $r['rub'];
		$word .= '<DIV class="rub">'.$rub.'</DIV>';
	}
	// Если подрубрика изменилась, то печать
	if ($sub != $r['sub']) {
		$sub = $r['sub'];
		$word .= '<DIV class="sub">'.$sub.'</DIV>';
	}
	$word .=
		'<DIV class="unit">'.
			$r['txt'].' '.
			($r['telefon'] ? '<b>Тел.: '.$r['telefon'].'</b>' : '').' '.
			($r['adres'] ? ($r['telefon'] ? ", " : '')."<B>Адрес: ".$r['adres']."</B>" : '').
			($r['dop'] ? '<span class="dop">('.$r['dop'].')</span>' : '').
		"</DIV>";
}
wordPrint($word, $gn);


function wordPrint($txt, $nomer=0) {
	require_once(VKPATH.'clsMsDocGenerator.php');
	$doc = new clsMsDocGenerator(
	    $pageOrientation = 'PORTRAIT',
	    $pageType = 'A4',
	    $cssFile = DOCUMENT_ROOT.'/css/ob-word.css',
	    $topMargin = 0.5,
	    $rightMargin = 1.0,
	    $bottomMargin = 0.5,
	    $leftMargin = 1.0);
	$doc->addParagraph($txt);
	$doc->output('nomer_'.$nomer);
	exit;
}
