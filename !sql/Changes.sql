/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;

/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;

/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;

/*!40101 SET NAMES utf8 */;

alter table `gazeta_nomer` DROP `id`;
alter table gazeta_nomer change general_nomer general_nomer int unsigned NOT NULL PRIMARY KEY;
alter table vk_user add gazeta_worker tinyint(1) unsigned default 0;
alter table vk_user add gazeta_admin tinyint(1) unsigned default 0;
drop table worker;
update vk_user set gazeta_worker=1,gazeta_admin=1 where viewer_id=982006;
alter table gazeta_nomer drop day_txt;
alter table oplata rename gazeta_money;
alter table gazeta_money drop tip;
alter table gazeta_money change summa sum float(8,2) default 0;
REPLACE INTO `gazeta_money` (`sum`,`prim`,`dtime_add`,`viewer_id_add`) SELECT `summa`*-1,`name`,`dtime_add`,`viewer_id_add` FROM `rashod`;
drop table rashod;
alter table client rename gazeta_client;
alter table accrual rename gazeta_accrual;
alter table zayav rename gazeta_zayav;
CREATE TABLE gazeta_kassa (
	id int unsigned NOT NULL auto_increment,
	PRIMARY KEY (`id`),
	sum float(8,2) default 0,
	txt text default NULL,
	client_id int unsigned default 0,
	zayav_id int unsigned default 0,
	money_id int unsigned default 0,
	viewer_id_add int unsigned default 0,
	dtime_add timestamp default current_timestamp
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
alter table gazeta_money add kassa tinyint(1) unsigned default 0 after prim;
alter table setup_global add kassa_start int default -1;
CREATE TABLE setup_rashod_category (
	id int unsigned NOT NULL auto_increment,
	PRIMARY KEY (`id`),
	name varchar(200) default '',
	sort smallint unsigned default '0',
	dtime_add timestamp default current_timestamp,
	viewer_id_add int unsigned default '0'
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
alter table gazeta_money add rashod_category int unsigned default 0 after kassa;
alter table gazeta_client add inn varchar(100) default '' after telefon;
alter table gazeta_client add kpp varchar(100) default '' after inn;
alter table gazeta_client add email varchar(100) default '' after kpp;
alter table gazeta_client change balans balans float(8,2) default 0;
alter table gazeta_client add skidka tinyint unsigned default 0 after email;
alter table gazeta_nomer drop stripe_count;
alter table gazeta_nomer drop day_begin;
alter table gazeta_nomer drop day_end;
CREATE TABLE `setup_money_type` (
	id int unsigned NOT NULL auto_increment,
	PRIMARY KEY (`id`),
	name varchar(100) default '',
	sort tinyint unsigned default 0,
	dtime_add timestamp default current_timestamp,
	viewer_id_add int unsigned default 0
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
alter table gazeta_zayav drop skidka_id;
alter table gazeta_zayav change skidka_razmer skidka tinyint(3) unsigned default 0;
delete from setup_ob_dop where id=3;
update gazeta_nomer_pub set ob_dop_id=polosa_id where polosa_id>0 and ob_dop_id=0;
alter table gazeta_nomer_pub drop polosa_id;
alter table gazeta_nomer_pub change ob_dop_id dop tinyint(3) unsigned default 0;
alter table gazeta_money add type tinyint(3) unsigned default 1 after id;
drop table gazeta_accrual;
alter table gazeta_nomer_pub change summa summa decimal(12,6) unsigned default 0;
update gazeta_client set fio=REPLACE(fio,'"','&quot;'),org_name=REPLACE(org_name,'"','&quot;');
alter table gazeta_zayav add gn_count smallint unsigned default 0 after file;
INSERT INTO `gazeta_zayav` (`id`,`gn_count`)  SELECT `zayav_id`,COUNT(`id`) FROM `gazeta_nomer_pub` GROUP BY `zayav_id` ON DUPLICATE KEY UPDATE `gn_count`=VALUES(`gn_count`);
alter table visit rename vk_visit;
CREATE TABLE `vk_ob` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  `rubrika` int(10) unsigned DEFAULT '0',
  `podrubrika` int(10) unsigned DEFAULT '0',
  `txt` text,
  `telefon` varchar(200) DEFAULT '',
  `dop` varchar(5) DEFAULT '',
  `file` varchar(300) DEFAULT '',
  `viewer_id_show` tinyint(3) unsigned DEFAULT '0',
  `day_active` date DEFAULT '0000-00-00',
  `day_top` date DEFAULT '0000-00-00',
  `status` tinyint(3) unsigned DEFAULT '1',
  `order_id` int(10) unsigned DEFAULT '0',
  `order_votes` int(10) unsigned DEFAULT '0',
  `country_id` int(10) unsigned DEFAULT '0',
  `country_name` varchar(100) DEFAULT '',
  `city_id` int(10) unsigned DEFAULT '0',
  `city_name` varchar(100) DEFAULT '',
  `dtime_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `viewer_id_add` int(10) unsigned DEFAULT '0'
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;
INSERT INTO `vk_ob` (
  `rubrika`,
  `podrubrika`,
  `txt`,
  `telefon`,
  `dop`,
  `file`,
  `viewer_id_show`,
  `day_active`,
  `status`,
  `order_id`,
  `order_votes`,
  `country_id`,
  `country_name`,
  `city_id`,
  `city_name`,
  `dtime_add`,
  `viewer_id_add`)
  SELECT
    `rubrika`,
    `podrubrika`,
    `txt`,
    `telefon`,
    `dop`,
    `file`,
    `viewer_id_show`,
    `active_day`,
    `status`,
    `order_id`,
    `order_votes`,
    `country_id`,
    `country_name`,
    `city_id`,
    `city_name`,
    `dtime_add`,
    `viewer_id_add`
  FROM `gazeta_zayav`
  WHERE `whence`='vk'
  ORDER BY `id`;
DELETE FROM gazeta_zayav WHERE whence='vk';
alter table gazeta_zayav drop whence;
alter table gazeta_zayav drop active_day;
alter table gazeta_zayav drop viewer_id_show;
alter table gazeta_zayav drop dop;
alter table gazeta_zayav drop order_id;
alter table gazeta_zayav drop order_votes;
alter table gazeta_zayav drop country_id;
alter table gazeta_zayav drop country_name;
alter table gazeta_zayav drop city_id;
alter table gazeta_zayav drop city_name;
alter table gazeta_zayav drop top_day;
alter table gazeta_zayav drop status;
INSERT INTO `setup_money_type` (`id`,`name`,`sort`,`viewer_id_add`) VALUES
(1,'Наличный',0,982006),
(2,'Безналичный',1,982006),
(3,'Взаимозачёт',2,982006),
(4,'На телефон Юре',3,982006),
(5,'На телефон Маше',4,982006);
CREATE TABLE gazeta_log (
  id int unsigned NOT NULL auto_increment,
  PRIMARY KEY (`id`),
  type smallint unsigned default 0,
  client_id int unsigned default 0,
  zayav_id int unsigned default 0,
  value varchar(200) default '',
  dop varchar(200) default '',
  viewer_id_add int unsigned default 0,
  dtime_add timestamp default current_timestamp
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
alter table gazeta_client add activity date default '0000-00-00' after org_name;
alter table gazeta_nomer_pub add index gn_i (general_nomer);
alter table gazeta_nomer_pub add index zayav_id_i (zayav_id);
INSERT INTO
  `gazeta_client`
  (`id`,`activity`)

  SELECT
    `z`.`client_id`,
    MAX(`gn`.`day_public`)
  FROM
      `gazeta_zayav` AS `z`

      LEFT JOIN
      `gazeta_nomer_pub` AS `pub`
        ON
          `pub`.`zayav_id`=`z`.`id`

      LEFT JOIN
      `gazeta_nomer` AS `gn`
        ON
          `pub`.`general_nomer`=`gn`.`general_nomer`

  WHERE
    `z`.`client_id`>0
  GROUP BY
    `z`.`client_id`

ON DUPLICATE KEY UPDATE
  `activity`=VALUES(`activity`);

