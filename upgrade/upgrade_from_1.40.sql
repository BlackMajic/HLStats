#
# HLStats Database Upgrade file
# -----------------------------
#
# REPLACE #DB_PREFIX# WITH YOUR CURRENT HLSTATS PREFIX eg. hlstats
#
# To upgrade an existing HLStats 1.40 database to version 1.50:
#
#   mysql hlstats_db_name < upgrade_from_1.40.sql
#

## ATTENTION THIS IS A SPECIAL ONE:
### replace #DB_NAME# with your current hlstats database name.
### this has nothing to do with the hlstats prefix for each table.
### but be warned use this as the latest possible choice
### to use this remove the # at the next line.
# ALTER DATABASE `#DB_NAME#` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

# improve sql query for very large amount of data
ALTER TABLE `#DB_PREFIX#_Events_Frags` ADD INDEX ( `eventTime` );

# add the default server map
ALTER TABLE `#DB_PREFIX#_Servers` ADD `defaultMap` VARCHAR( 128 ) NOT NULL ;

# the new rating system
ALTER TABLE `#DB_PREFIX#_Players` ADD `rating` float NOT NULL default '1500';
ALTER TABLE `#DB_PREFIX#_Players` ADD `rd2` float NOT NULL default '122500';
ALTER TABLE `#DB_PREFIX#_Players` ADD `rating_last` timestamp NOT NULL default CURRENT_TIMESTAMP;
