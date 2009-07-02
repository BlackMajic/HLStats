#
# HLStats Database Upgrade file
# -----------------------------
#
# REPLACE #DB_PREFIX# WITH YOUR CURRENT HLSTATS PREFIX eg. hlstats
#
# To upgrade an existing HLStats 1.38 database to version 1.40, type:
#
#   mysql hlstats_db_name < upgrade_from_1.38.sql
#
#

CREATE TABLE `#DB_PREFIX#_Events_Chat` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `eventTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `serverId` int(10) unsigned NOT NULL default '0',
  `map` varchar(64) NOT NULL,
  `playerId` int(10) NOT NULL,
  `type` int(1) NOT NULL,
  `message` varchar(128) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
