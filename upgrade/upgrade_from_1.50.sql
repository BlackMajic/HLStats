ALTER TABLE hlstats_Events_Frags ADD INDEX ( victimId );
ALTER TABLE hlstats_Events_Frags ADD INDEX ( killerId );
ALTER TABLE hlstats_Events_Frags ADD INDEX ( weapon );
ALTER TABLE hlstats_Weapons ADD INDEX ( code );
ALTER TABLE `hlstats_Events_Frags` ADD INDEX ( `map` )  ;
ALTER TABLE `hlstats_Players` ADD `skillchangeDate` INT( 10 ) NULL AFTER `oldSkill` ;
ALTER TABLE `hlstats_Players` ADD `active` INT( 1 ) NOT NULL DEFAULT '1' AFTER `skillchangeDate` ;