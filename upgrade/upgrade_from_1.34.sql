#
# HLStats Database Upgrade file
# -----------------------------
#
# To upgrade an existing HLStats 1.34 database to version 1.35, type:
#
#   mysql hlstats_db_name < upgrade_from_1.34.sql
#

#
# add ability to hide options
#
INSERT INTO hlstats_Options VALUES ('hideAwards', '0');
INSERT INTO hlstats_Options VALUES ('hideNews', '0');

#
# the news table
#
INSERT INTO `hlstats_News` ( `id` , `date` , `user` , `email` , `subject` , `message` )
VALUES (NULL , CURDATE( ) , 'admin', 'admin@website.com', 'The first news', 'This is the first news of the news Plugin. You can edit and add news at the admin interface. You can also hide the news at the admin options section.');


#
# update URLs, add more addons
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
