#
# HLStats Database Upgrade file
# -----------------------------
#
# To upgrade an existing HLStats 1.31 database to version 1.32, type:
#
#   mysql hlstats < upgrade_from_1.31.sql
#
#   THEN
#
#   mysql hlstats < gamesupport_xxxx.sql
#

#
# Add Source field to Games table
#

ALTER TABLE hlstats_Games ADD source TINYINT(1) DEFAULT '0' NOT NULL AFTER name;

#
# Add hide awards option to db
#
INSERT INTO hlstats_Options VALUES ('hideAwards', '0');


#
# update server addon URLs, add some more addons
#
TRUNCATE TABLE hlstats_Server_Addons;

INSERT INTO hlstats_Server_Addons VALUES ('hlg_version', 'HLGuard %', 'http://www.thezproject.org/projects.php?pid=1');
INSERT INTO hlstats_Server_Addons VALUES ('clanmod_version', 'ClanMod %', 'http://www.unitedadmins.com/index.php?p=content&content=clanmod');
INSERT INTO hlstats_Server_Addons VALUES ('statsme_version', 'StatsMe %', 'http://www.unitedadmins.com/index.php?p=content&content=statsme');
INSERT INTO hlstats_Server_Addons VALUES ('phpua_mm_version', 'phpUA %', 'http://www.phpua.com');
INSERT INTO hlstats_Server_Addons VALUES ('cdversion', 'Cheating-Death %', 'http://www.unitedadmins.com/index.php?p=content&content=cd');
INSERT INTO hlstats_Server_Addons VALUES ('metamod_version', 'MetaMod %', 'http://www.metamod.org');
INSERT INTO hlstats_Server_Addons VALUES ('amxmodx_version', 'AMXX %', 'http://www.amxmodx.org');
INSERT INTO hlstats_Server_Addons VALUES ('sbsrv_version', 'Steambans %', 'http://www.steambans.com');
INSERT INTO hlstats_Server_Addons VALUES ('logmod_version', 'LogMod %', 'http://www.hlsw.org/index.php?page=logmod_info');
