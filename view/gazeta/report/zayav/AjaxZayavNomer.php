<?php
require_once('../../../../include/AjaxHeader.php');

$send = array();

for ($n = 1; $n <= 4; $n++) {
    $spisok = $VK->QueryObjectArray("
SELECT
  `gn`.`week_nomer` AS `week`,
  `gn`.`general_nomer` AS `general`,
  `gn`.`day_public` AS `public`,
  COUNT(`z`.`id`) AS `count`,
  SUM(`pub`.`summa`) AS `summa`

FROM
  `gazeta_nomer` AS `gn`

            LEFT JOIN
              `gazeta_nomer_pub` AS `pub`
            ON
              `pub`.`general_nomer`=`gn`.`general_nomer`

            LEFT JOIN
              `gazeta_zayav` AS `z`
            ON
              `z`.`id`=`pub`.`zayav_id`

WHERE
  `gn`.`day_public` LIKE '".$_GET['year']."%' AND
  `z`.`category`=".$n."
GROUP BY
  `gn`.`general_nomer`;
");

    if (count($spisok) > 0) {
        foreach ($spisok as $sp) {
            $send[$sp->general]['gn']['week'] = $sp->week;
            $send[$sp->general]['gn']['public'] = utf8(FullData($sp->public, 1));
            $send[$sp->general][$n]['count'] = $sp->count;
            $send[$sp->general][$n]['summa'] = round($sp->summa, 2);
        }
    }
}

echo json_encode($send);
?>



