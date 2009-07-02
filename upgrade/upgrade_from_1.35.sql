#
# HLStats Database Upgrade file
# -----------------------------
#
# To upgrade an existing HLStats 1.35 database to version 1.36, type:
#
#   mysql hlstats_db_name < upgrade_from_1.35.sql
#

# map name fix
ALTER TABLE `hlstats_Events_Admin` CHANGE `map` `map` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `hlstats_Events_ChangeName` CHANGE `map` `map` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `hlstats_Events_ChangeRole` CHANGE `map` `map` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `hlstats_Events_ChangeTeam` CHANGE `map` `map` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `hlstats_Events_Connects` CHANGE `map` `map` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `hlstats_Events_Disconnects` CHANGE `map` `map` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `hlstats_Events_Entries` CHANGE `map` `map` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `hlstats_Events_Frags` CHANGE `map` `map` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `hlstats_Events_Frags` CHANGE `weapon` `weapon` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `hlstats_Events_PlayerActions` CHANGE `map` `map` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `hlstats_Events_PlayerPlayerActions` CHANGE `map` `map` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `hlstats_Events_Rcon` CHANGE `map` `map` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `hlstats_Events_Statsme` CHANGE `map` `map` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `hlstats_Events_Statsme2` CHANGE `map` `map` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `hlstats_Events_StatsmeLatency` CHANGE `map` `map` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `hlstats_Events_StatsmeTime` CHANGE `map` `map` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `hlstats_Events_Suicides` CHANGE `map` `map` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `hlstats_Events_TeamBonuses` CHANGE `map` `map` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `hlstats_Events_Teamkills` CHANGE `map` `map` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

# add news table
CREATE TABLE IF NOT EXISTS `hlstats_News` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `date` varchar(32) NOT NULL,
  `user` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `subject` varchar(128) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO `hlstats_News` (`id`, `date`, `user`, `email`, `subject`, `message`) VALUES
(1, '2007-12-11 10:17:25', 'admin', 'admin@website.com', 'The first news', 'This is the first news of the news Plugin. You can edit and add news at the admin interface. You can also hide the news at the admin options section.');

# extend the options and set new default design
UPDATE `hlstats_Options` SET `value` = '<font face="Verdana,Arial,Helvetica" size="2" class="fontNormal">' WHERE `keyname` = 'font_normal';
UPDATE `hlstats_Options` SET `value` = '</font>' WHERE `keyname` = 'fontend_normal';
UPDATE `hlstats_Options` SET `value` = '<font face="Verdana,Arial,Helvetica" size="1" class="fontSmall">' WHERE `keyname` = 'font_small';
UPDATE `hlstats_Options` SET `value` = '</font>' WHERE `keyname` = 'fontend_small';
UPDATE `hlstats_Options` SET `value` = '<font face="Arial,Helvetica" size="3" class="fontTitle"><b>' WHERE `keyname` = 'font_title';
UPDATE `hlstats_Options` SET `value` = '</b></font>' WHERE `keyname` = 'fontend_title';
UPDATE `hlstats_Options` SET `value` = '#283846' WHERE `keyname` = 'table_bgcolor1';
UPDATE `hlstats_Options` SET `value` = '#1F2F3D' WHERE `keyname` = 'table_bgcolor2';
UPDATE `hlstats_Options` SET `value` = '#253546' WHERE `keyname` = 'table_wpnbgcolor';
UPDATE `hlstats_Options` SET `value` = '#253546' WHERE `keyname` = 'table_border';
UPDATE `hlstats_Options` SET `value` = '#39495A' WHERE `keyname` = 'table_border';
UPDATE `hlstats_Options` SET `value` = '#C6C6C6' WHERE `keyname` = 'table_head_text';
UPDATE `hlstats_Options` SET `value` = '#39495A' WHERE `keyname` = 'table_head_bgcolor';
UPDATE `hlstats_Options` SET `value` = '#FFFFFF' WHERE `keyname` = 'location_link';
UPDATE `hlstats_Options` SET `value` = '#FFFFFF' WHERE `keyname` = 'location_text';
UPDATE `hlstats_Options` SET `value` = '#39495A' WHERE `keyname` = 'location_bgcolor';
UPDATE `hlstats_Options` SET `value` = '10' WHERE `keyname` = 'body_leftmargin';
UPDATE `hlstats_Options` SET `value` = '15' WHERE `keyname` = 'body_topmargin';
UPDATE `hlstats_Options` SET `value` = '#C6C6C6' WHERE `keyname` = 'body_alink';
UPDATE `hlstats_Options` SET `value` = '#C6C6C6' WHERE `keyname` = 'body_link';
UPDATE `hlstats_Options` SET `value` = '#FFFFFF' WHERE `keyname` = 'body_hlink';
UPDATE `hlstats_Options` SET `value` = '#FFFFFF' WHERE `keyname` = 'body_vlink';
UPDATE `hlstats_Options` SET `value` = '#C6C6C6' WHERE `keyname` = 'body_text';
UPDATE `hlstats_Options` SET `value` = '#253546' WHERE `keyname` = 'body_bgcolor';
UPDATE `hlstats_Options` SET `value` = '' WHERE `keyname` = 'body_background';
UPDATE `hlstats_Options` SET `value` = 'ua_style' WHERE `keyname` = 'style';
UPDATE `hlstats_Options` SET `value` = '' WHERE `keyname` = '';

INSERT INTO `hlstats_Options` (`keyname`, `value`) VALUES('hideAwards', '0');
INSERT INTO `hlstats_Options` (`keyname`, `value`) VALUES('hideNews', '0');
INSERT INTO `hlstats_Options` (`keyname`, `value`) VALUES('reset_date', '0');

RENAME TABLE `hlstats_server_addons`  TO `hlstats_Server_Addons` ;
