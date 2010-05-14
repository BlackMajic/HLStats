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

ALTER TABLE `hlstats_dev`.`hlstats_Events_ChangeTeam` ADD INDEX ( `team` ) ;
ALTER TABLE `hlstats_dev`.`hlstats_Events_ChangeTeam` ADD INDEX ( `serverId` ) ;
ALTER TABLE `hlstats_dev`.`hlstats_Servers` ADD INDEX ( `game` ) ;

UPDATE `hlstats_Server_Addons` SET `url` = 'http://wiki.hlsw.net/index.php/LogMod_Information'  WHERE `hlstats_Server_Addons`.`rule` = 'logmod_version';
UPDATE `hlstats_Server_Addons` SET `url` = 'http://wiki.hlsw.net/index.php/HLGuard' WHERE `hlstats_Server_Addons`.`rule` = 'hlg_version';
UPDATE `hlstats_Server_Addons` SET `url` = 'http://sourceforge.net/projects/clanmod/' WHERE `hlstats_Server_Addons`.`rule` = 'clanmod_version';
UPDATE `hlstats_Server_Addons` SET `url` = 'http://sourceforge.net/projects/statsme/' WHERE `hlstats_Server_Addons`.`rule` = 'statsme_version';
UPDATE `hlstats_Server_Addons` SET `url` = 'http://phpua.sourceforge.net/' WHERE `hlstats_Server_Addons`.`rule` = 'phpua_mm_version';
UPDATE `hlstats_Server_Addons` SET `url` = 'http://de.wikipedia.org/wiki/Cheating-Death'  WHERE `hlstats_Server_Addons`.`rule` = 'cdversion';