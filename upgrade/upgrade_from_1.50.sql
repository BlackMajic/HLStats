#
# HLStats Database Upgrade file
# -----------------------------
#
# REPLACE #DB_PREFIX# WITH YOUR CURRENT HLSTATS PREFIX eg. hlstats
#
# To upgrade an existing HLStats 1.50 database to version 1.51:
#
#   mysql hlstats_db_name < upgrade_from_1.50.sql
#

ALTER TABLE `#DB_PREFIX#_Events_Frags` ADD INDEX ( victimId );
ALTER TABLE `#DB_PREFIX#_Events_Frags` ADD INDEX ( killerId );
ALTER TABLE `#DB_PREFIX#_Events_Frags` ADD INDEX ( weapon );
ALTER TABLE `#DB_PREFIX#_Weapons` ADD INDEX ( code );
ALTER TABLE `#DB_PREFIX#_Events_Frags` ADD INDEX ( `map` )  ;
ALTER TABLE `#DB_PREFIX#_Players` ADD `skillchangeDate` INT( 10 ) NULL AFTER `oldSkill` ;
ALTER TABLE `#DB_PREFIX#_Players` ADD `active` INT( 1 ) NOT NULL DEFAULT '1' AFTER `skillchangeDate` ;
ALTER TABLE `#DB_PREFIX#_Players` ADD INDEX ( `active` ) ;
ALTER TABLE `#DB_PREFIX#_Players` ADD INDEX ( `hideranking` );
ALTER TABLE `#DB_PREFIX#_Events_PlayerActions` ADD INDEX ( `actionId` );
ALTER TABLE `#DB_PREFIX#_Events_PlayerActions` ADD INDEX ( `playerId` );
ALTER TABLE `#DB_PREFIX#_Events_PlayerActions` ADD INDEX ( `serverId` );
ALTER TABLE `#DB_PREFIX#_Events_PlayerPlayerActions` ADD INDEX ( `serverId` );
ALTER TABLE `#DB_PREFIX#_Events_PlayerPlayerActions` ADD INDEX ( `playerId` );
ALTER TABLE `#DB_PREFIX#_Events_PlayerPlayerActions` ADD INDEX ( `victimId` );
ALTER TABLE `#DB_PREFIX#_Events_PlayerPlayerActions` ADD INDEX ( `actionId` );
