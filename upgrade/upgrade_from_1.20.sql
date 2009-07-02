#
# HLStats Database Upgrade file
# -----------------------------
#
# To upgrade an existing HLStats 1.20 database to version 1.30, type:
#
#   mysql hlstats < upgrade_from_1.20.sql
#
#   THEN
#
#   mysql hlstats < gamesupport_xxxx.sql
#


#
# Add new values into 'hlstats_Options'
#

INSERT INTO hlstats_Options VALUES ('body_hlink', '#FF9900');
INSERT INTO hlstats_Options VALUES ('style', 'def');
INSERT INTO hlstats_Options VALUES ('hideAwards', '0');

#
# Drop 'hlstats_Users' table
#

DROP TABLE hlstats_Users;

#
# Table structure for table 'hlstats_Users'
#

CREATE TABLE hlstats_Users (
  username varchar(16) DEFAULT '' NOT NULL,
  password varchar(32) DEFAULT '' NOT NULL,
  acclevel int(11) DEFAULT '0' NOT NULL,
  playerId int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (username)
);

#
# Dumping data for table 'hlstats_Users'
#

INSERT INTO hlstats_Users VALUES ('admin', MD5('123456'), 100, 0);

#
# Add Style Table
#

CREATE TABLE hlstats_Style (
 keyname varchar(32) NOT NULL default '',
 def varchar(255) NOT NULL default '',
 black varchar(255) NOT NULL default '',
 light_blue varchar(255) NOT NULL default '',
 grey varchar(255) NOT NULL default '',
 ua_style varchar(255) NOT NULL default '',
 red varchar(255) NOT NULL default '',
 light_grey varchar(255) NOT NULL default '',
 white varchar(255) NOT NULL default '',
 PRIMARY KEY  (`keyname`)
) TYPE=MyISAM;

#
# Dumping data for table `hlstats_Style`
#

INSERT INTO hlstats_Style VALUES ('font_normal', '<font face="Verdana, Arial, sans-serif" size=2 class="fontNormal">', '<font face="Courier New, Courier" size=2 class="fontNormal">', '<font face="Verdana, Arial, sans-serif" size=2 class="fontNormal">', '<font face="Georgia, Times New Roman, Times, serif" size=2 class="fontNormal">', '<font face="Verdana,Arial,Helvetica" size="2" class="fontNormal">', '<font face="Verdana, Arial, sans-serif" size=2 class="fontNormal">', '<font face="Georgia, Times New Roman, Times, serif" size=2 class="fontNormal">', '<font face="Geneva, Arial, Helvetica, sans-serif" size=2 class="fontNormal">');
INSERT INTO hlstats_Style VALUES ('fontend_normal', '</font>', '</font>', '</font>', '</font>', '</font>', '</font>', '</font>', '</font>');
INSERT INTO hlstats_Style VALUES ('font_small', '<font face="Verdana, Arial, sans-serif" size=1 class="fontSmall">', '<font face="Courier New, Courier" size=1 class="fontSmall">', '<font face="Verdana, Arial, sans-serif" size=1 class="fontSmall">', '<font face="Georgia, Times New Roman, Times, serif" size=1 class="fontSmall">', '<font face="Verdana,Arial,Helvetica" size="1" class="fontSmall">', '<font face="Verdana, Arial, sans-serif" size=1 class="fontSmall">', '<font face="Georgia, Times New Roman, Times, serif" size=1 class="fontSmall">', '<font face="Geneva, Arial, Helvetica, sans-serif" size=1 class="fontSmall">');
INSERT INTO hlstats_Style VALUES ('fontend_small', '</font>', '</font>', '</font>', '</font>', '</font>', '</font>', '</font>', '</font>');
INSERT INTO hlstats_Style VALUES ('font_title', '<font face="Arial, sans-serif" size=4 class="fontTitle"><b>', '<font face="Courier New, Courier" size=4 class="fontTitle"><b>', '<font face="Arial, sans-serif" size=4 class="fontTitle"><b>', '<font face="Georgia, Times New Roman, Times, serif" size=4 class="fontTitle"><b>', '<font face="Arial,Helvetica" size="3" class="fontTitle"><b>', '<font face="Arial, sans-serif" size=4 class="fontTitle"><b>', '<font face="Georgia, Times New Roman, Times, serif" size=4 class="fontTitle"><b>', '<font face="Geneva, Arial, Helvetica, sans-serif" size=4 class="fontTitle"><b>');
INSERT INTO hlstats_Style VALUES ('fontend_title', '</b></font>', '</b></font>', '</b></font>', '</b></font>', '</b></font>', '</b></font>', '</b></font>', '</b></font>');
INSERT INTO hlstats_Style VALUES ('table_bgcolor1', '#15154D', '#282828', '#3F5576', '#CCCCCC', '#283846', '#550000', '#FFFFFF', '#edf3f9');
INSERT INTO hlstats_Style VALUES ('table_bgcolor2', '#161652', '#282828', '#3F5569', '#999999', '#1F2F3D', '#440000', '#FAFAFA', '#d0dbe6');
INSERT INTO hlstats_Style VALUES ('table_wpnbgcolor', '#000000', '#000000', '#3F5569', '#999999', '#253546', '#440000', '#EAEAEA', '#d0dbe6');
INSERT INTO hlstats_Style VALUES ('table_border', '#001B73', '#E0E0E0', '#59748D', '#333333', '#39495A', '#000000', '#000000', '#000000');
INSERT INTO hlstats_Style VALUES ('table_head_text', '#EEEEEE', '#EEEEEE', '#EEEEEE', '#FFFFFF', '#C6C6C6', '#FFFFFF', '#000000', '#000000');
INSERT INTO hlstats_Style VALUES ('table_head_bgcolor', '#002E8A', '#282828', '#3F5576', '#666666', '#39495A', '#660000', '#AEAEAE', '#E8E8E8');
INSERT INTO hlstats_Style VALUES ('location_link', '#FFFFFF', '#FFFFFF', '#FFFFFF', '#808080', '#FFFFFF', '#FFFFFF', '#AEAEAE', '#000000');
INSERT INTO hlstats_Style VALUES ('location_text', '#FFFFFF', '#FFFFFF', '#FFFFFF', '#000000', '#FFFFFF', '#FFFFFF', '#000000', '#000000');
INSERT INTO hlstats_Style VALUES ('location_bgcolor', '#003399', '#003399', '#3F5576', '#AAAAAA', '#39495A', '#660000', '#EAEAEA', '#E8E8E8');
INSERT INTO hlstats_Style VALUES ('body_leftmargin', '10', '10', '10', '5', '10', '10', '10', '10');
INSERT INTO hlstats_Style VALUES ('body_topmargin', '15', '15', '15', '8', '15', '15', '15', '15');
INSERT INTO hlstats_Style VALUES ('body_alink', '#FFBB00', '#FFFFFF', '#B0B0B0', '#808080', '#C6C6C6', '#800000', '#AEAEAE', '#000000');
INSERT INTO hlstats_Style VALUES ('body_link', '#FF9900', '#FFFFFF', '#B0B0B0', '#808080', '#C6C6C6', '#C0C0C0', '#AEAEAE', '#000000');
INSERT INTO hlstats_Style VALUES ('body_text', '#EEEEEE', '#CCCCCC', '#FFFFFF', '#000000', '#C6C6C6', '#FFFFFF', '#000000', '#000000');
INSERT INTO hlstats_Style VALUES ('body_vlink', '#FF9000', '#FFFFFF', '#B0B0B0', '#808080', '#C6C6C6', '#C0C0C0', '#AEAEAE', '#000000');
INSERT INTO hlstats_Style VALUES ('body_bgcolor', '#0C1D40', '#000000', '#3F5569', '#AAAAAA', '#253546', '#000000', '#EAEAEA', '#FFFFFF');
INSERT INTO hlstats_Style VALUES ('body_background', '', '', '', '', '', '', '', '');
INSERT INTO hlstats_Style VALUES ('body_hlink', '#FF9900', '#494949', '#FFFFFF', '#FFFFFF', '#FFFFFF', '#800000', '#7A7A7A', '#404040');

#
# Add Addon Table
#

CREATE TABLE hlstats_Server_Addons (
rule varchar(64) NOT NULL default '',
addon varchar(64) NOT NULL default '',
url varchar(255) NOT NULL default '',
PRIMARY KEY  (rule)
) TYPE=MyISAM;

#
# Dumping data for table `hlstats_Server_Addons`
#

INSERT INTO hlstats_Server_Addons VALUES ('hlg_version', 'HLGuard %', 'http://www.thezproject.org/projects.php?pid=1');
INSERT INTO hlstats_Server_Addons VALUES ('clanmod_version', 'ClanMod %', 'http://www.unitedadmins.com/index.php?p=content&content=clanmod');
INSERT INTO hlstats_Server_Addons VALUES ('statsme_version', 'StatsMe %', 'http://www.unitedadmins.com/index.php?p=content&content=statsme');
INSERT INTO hlstats_Server_Addons VALUES ('phpua_mm_version', 'phpUA %', 'http://www.phpua.com');
INSERT INTO hlstats_Server_Addons VALUES ('cdversion', 'Cheating-Death %', 'http://www.unitedadmins.com/index.php?p=content&content=cd');
INSERT INTO hlstats_Server_Addons VALUES ('metamod_version', 'MetaMod %', 'http://www.metamod.org');
INSERT INTO hlstats_Server_Addons VALUES ('amxmodx_version', 'AMXX %', 'http://www.amxmodx.org');
INSERT INTO hlstats_Server_Addons VALUES ('sbsrv_version', 'Steambans %', 'http://www.steambans.com');
INSERT INTO hlstats_Server_Addons VALUES ('logmod_version', 'LogMod %', 'http://www.hlsw.org/index.php?page=logmod_info');

#
# Add Source field to Games table
#

ALTER TABLE hlstats_Games ADD source TINYINT(1) DEFAULT '0' NOT NULL AFTER name;
