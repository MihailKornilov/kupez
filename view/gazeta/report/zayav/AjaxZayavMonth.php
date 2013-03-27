<?php
require_once('../../../../include/AjaxHeader.php');


$send = array();

for ($n = 1; $n <= 4; $n++) {
    $spisok = $VK->QueryObjectArray("
        SELECT
          DISTINCT(DATE_FORMAT(`gn`.`day_public`, '%m')) AS `mon`,
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
          DATE_FORMAT(`gn`.`day_public`,'%m')");

    if (count($spisok) > 0) {
        foreach ($spisok as $sp) {
            $send[abs($sp->mon)][$n] = array(
                'count' => $sp->count,
                'summa' => round($sp->summa, 2)
            );
      }
    }
}

echo json_encode($send);
?>



