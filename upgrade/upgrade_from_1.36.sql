#
# HLStats Database Upgrade file
# -----------------------------
#
# To upgrade an existing HLStats 1.36 database to version 1.37, type:
#
#   mysql hlstats_db_name < upgrade_from_1.36.sql
#

# weapon name fix
ALTER TABLE `hlstats_Events_Frags` CHANGE `weapon` `weapon` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `hlstats_Events_Statsme` CHANGE `weapon` `weapon` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `hlstats_Events_Statsme2` CHANGE `weapon` `weapon` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `hlstats_Events_Suicides` CHANGE `weapon` `weapon` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `hlstats_Events_Teamkills` CHANGE `weapon` `weapon` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
