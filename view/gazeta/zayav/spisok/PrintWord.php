<?php
require_once('../../../../include/clsMsDocGenerator.php');
require_once('../../../../include/AjaxHeader.php');



$spisok = $VK->QueryObjectArray('SELECT
  rub.name rub,
  IFNULL(podrub.name,"") podrub,
  z.txt txt,
  z.telefon telefon,
  z.adres adres,
  IFNULL(dop.name,"") dop

FROM
  gazeta_nomer_pub pub

LEFT JOIN
  gazeta_zayav z
ON
  pub.zayav_id=z.id

LEFT JOIN
  setup_rubrika rub
ON
  z.rubrika=rub.id

LEFT JOIN
  setup_pod_rubrika podrub
ON
  z.podrubrika=podrub.id

LEFT JOIN
  setup_ob_dop dop
ON
  pub.dop=dop.id

WHERE
  pub.general_nomer='.$_GET['gn'].'
ORDER BY
  rub.sort,podrub.sort,z.txt');

if (count($spisok) > 0) {
    $word = 'Список объявлений для номера '.$_GET['gn'].':';  // Составление объявлений для отправки
    $rub = '';   // Контроль рубрик
    $podrub = '';// Контроль подрубрик
    foreach($spisok as $sp) {
        // Если рубрика изменилась, то печать
        if ($rub != $sp->rub) {
            $rub = $sp->rub;
            $word .= '<DIV class=rub>'.$rub.'</DIV>';
        }
        // Если подрубрика изменилась, то печать
        if ($podrub != $sp->podrub) {
            $podrub = $sp->podrub;
            $word .= '<DIV class=podrub>'.$podrub.'</DIV>';
        }
        $word .= "<DIV class=unit>".
                    $sp->txt." ".
                    ($sp->telefon ? "<B>Тел.: ".$sp->telefon."</B>" : '')." ".
                    ($sp->adres ? ($sp->telefon ? ", " : '')."<B>Адрес: ".$sp->adres."</B>" : '').
                    ($sp->dop ? '<SPAN class=dop>('.$sp->dop.')</SPAN>' : '').
                  "</DIV>";
    }
} else $word = "Нет объявлений для номера ".$_GET['gn'];

$doc = new clsMsDocGenerator(
    $pageOrientation = 'PORTRAIT',
    $pageType = 'A4',
    $cssFile = 'PrintWord.css',
    $topMargin = 0.5,
    $rightMargin = 1.0,
    $bottomMargin = 0.5,
    $leftMargin = 1.0);
$doc->addParagraph($word);
$doc->output('nomer_'.$_GET['gn']);
?>