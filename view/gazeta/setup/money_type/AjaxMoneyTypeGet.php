<?php
require_once('../../../../include/AjaxHeader.php');

$send = array();
$spisok = $VK->QueryObjectArray("SELECT
  `s`.`id`,
  `name`,
  COUNT(`m`.`id`) AS `count`
FROM
  `setup_money_type` AS `s`

    LEFT JOIN
      `gazeta_money` AS `m`
    ON
      `m`.`type`=`s`.`id`

GROUP BY `s`.`id`
ORDER BY `sort`");
if (count($spisok) > 0)
    foreach($spisok as $sp)
        array_push($send, array(
            'id' => $sp->id,
            'name' => utf8($sp->name),
            'count' => $sp->count
        ));

echo json_encode($send);
?>