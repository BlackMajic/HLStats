#
# HLStats Database Upgrade file
# -----------------------------
#
# REPLACE #DB_PREFIX# WITH YOUR CURRENT HLSTATS PREFIX eg. hlstats
#
# To upgrade an existing HLStats 1.51 database to version 1.60, type:
#
#   mysql hlstats_db_name < upgrade_from_1.51.sql
#
#

UPDATE #DB_PREFIX#_Options SET `keyname` = 'showChart' WHERE `keyname` = 'useFlash';
DELETE FROM `#DB_PREFIX#_Options` WHERE `keyname` = 'scripturl';
ALTER TABLE `#DB_PREFIX#_Events_ChangeRole` ADD INDEX ( `playerId` );
ALTER TABLE `#DB_PREFIX#_Events_ChangeRole` ADD INDEX ( `serverId` );

ALTER TABLE `#DB_PREFIX#_Events_ChangeTeam` ADD INDEX ( `team` ) ;
ALTER TABLE `#DB_PREFIX#_Events_ChangeTeam` ADD INDEX ( `serverId` ) ;
ALTER TABLE `#DB_PREFIX#_Servers` ADD INDEX ( `game` ) ;

UPDATE `#DB_PREFIX#_Server_Addons` SET `url` = 'http://wiki.hlsw.net/index.php/LogMod_Information'  WHERE `hlstats_Server_Addons`.`rule` = 'logmod_version';
UPDATE `#DB_PREFIX#_Server_Addons` SET `url` = 'http://wiki.hlsw.net/index.php/HLGuard' WHERE `hlstats_Server_Addons`.`rule` = 'hlg_version';
UPDATE `#DB_PREFIX#_Server_Addons` SET `url` = 'http://sourceforge.net/projects/clanmod/' WHERE `hlstats_Server_Addons`.`rule` = 'clanmod_version';
UPDATE `#DB_PREFIX#_Server_Addons` SET `url` = 'http://sourceforge.net/projects/statsme/' WHERE `hlstats_Server_Addons`.`rule` = 'statsme_version';
UPDATE `#DB_PREFIX#_Server_Addons` SET `url` = 'http://phpua.sourceforge.net/' WHERE `hlstats_Server_Addons`.`rule` = 'phpua_mm_version';
UPDATE `#DB_PREFIX#_Server_Addons` SET `url` = 'http://de.wikipedia.org/wiki/Cheating-Death'  WHERE `hlstats_Server_Addons`.`rule` = 'cdversion';

ALTER TABLE `#DB_PREFIX#_Players` ADD `myspace` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `icq`;
ALTER TABLE `#DB_PREFIX#_Players` ADD `facebook` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `myspace` ;
ALTER TABLE `#DB_PREFIX#_Players` ADD `jabber` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `facebook` ;
ALTER TABLE `#DB_PREFIX#_Players` ADD `steamprofile` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `jabber` ;

ALTER TABLE `#DB_PREFIX#_Users`  DROP `acclevel`,  DROP `playerId`;
ALTER TABLE `#DB_PREFIX#_Users` ADD `authCode` VARCHAR( 40 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL 

ALTER TABLE `#DB_PREFIX#_Players` CHANGE `icq` `icq` VARCHAR( 10 ) NOT NULL ;

ALTER TABLE `#DB_PREFIX#_Clans` ADD `steamGroup` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;

DELETE FROM `#DB_PREFIX#_Options` WHERE `keyname` = 'imgdir';
DELETE FROM `#DB_PREFIX#_Options` WHERE `keyname` = 'imgpath';

DELETE FROM `#DB_PREFIX#_Options` WHERE `keyname` = 'font_normal';
DELETE FROM `#DB_PREFIX#_Options` WHERE `keyname` = 'fontend_normal';
DELETE FROM `#DB_PREFIX#_Options` WHERE `keyname` = 'font_small';
DELETE FROM `#DB_PREFIX#_Options` WHERE `keyname` = 'fontend_small';
DELETE FROM `#DB_PREFIX#_Options` WHERE `keyname` = 'font_title';
DELETE FROM `#DB_PREFIX#_Options` WHERE `keyname` = 'fontend_title';
DELETE FROM `#DB_PREFIX#_Options` WHERE `keyname` = 'table_bgcolor1';
DELETE FROM `#DB_PREFIX#_Options` WHERE `keyname` = 'table_bgcolor2';
DELETE FROM `#DB_PREFIX#_Options` WHERE `keyname` = 'table_wpnbgcolor';
DELETE FROM `#DB_PREFIX#_Options` WHERE `keyname` = 'table_border';
DELETE FROM `#DB_PREFIX#_Options` WHERE `keyname` = 'table_head_text';
DELETE FROM `#DB_PREFIX#_Options` WHERE `keyname` = 'table_head_bgcolor';
DELETE FROM `#DB_PREFIX#_Options` WHERE `keyname` = 'location_link';
DELETE FROM `#DB_PREFIX#_Options` WHERE `keyname` = 'location_text';
DELETE FROM `#DB_PREFIX#_Options` WHERE `keyname` = 'location_bgcolor';
DELETE FROM `#DB_PREFIX#_Options` WHERE `keyname` = 'body_leftmargin';
DELETE FROM `#DB_PREFIX#_Options` WHERE `keyname` = 'body_topmargin';
DELETE FROM `#DB_PREFIX#_Options` WHERE `keyname` = 'body_alink';
DELETE FROM `#DB_PREFIX#_Options` WHERE `keyname` = 'body_link';
DELETE FROM `#DB_PREFIX#_Options` WHERE `keyname` = 'body_hlink';
DELETE FROM `#DB_PREFIX#_Options` WHERE `keyname` = 'body_vlink';
DELETE FROM `#DB_PREFIX#_Options` WHERE `keyname` = 'body_text';
DELETE FROM `#DB_PREFIX#_Options` WHERE `keyname` = 'body_bgcolor';
DELETE FROM `#DB_PREFIX#_Options` WHERE `keyname` = 'body_background';

UPDATE `#DB_PREFIX#_Options` SET `value` = 'default' WHERE `keyname` =  'style';