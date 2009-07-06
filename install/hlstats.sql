SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

DROP TABLE IF EXISTS `#DB_PREFIX#_Actions`;
CREATE TABLE `#DB_PREFIX#_Actions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `game` varchar(16) NOT NULL default 'valve',
  `code` varchar(64) NOT NULL default '',
  `reward_player` int(11) NOT NULL default '10',
  `reward_team` int(11) NOT NULL default '0',
  `team` varchar(32) NOT NULL default '',
  `description` varchar(128) default NULL,
  `for_PlayerActions` enum('0','1') NOT NULL default '0',
  `for_PlayerPlayerActions` enum('0','1') NOT NULL default '0',
  `for_TeamActions` enum('0','1') NOT NULL default '0',
  `for_WorldActions` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gamecode` (`game`,`code`),
  KEY `code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `#DB_PREFIX#_Awards`;
CREATE TABLE `#DB_PREFIX#_Awards` (
  `awardId` int(10) unsigned NOT NULL auto_increment,
  `awardType` enum('W','O') NOT NULL default 'W',
  `game` varchar(32) NOT NULL default 'valve',
  `code` varchar(128) NOT NULL default '',
  `name` varchar(128) NOT NULL default '',
  `verb` varchar(64) NOT NULL default '',
  `d_winner_id` int(10) unsigned default NULL,
  `d_winner_count` int(10) unsigned default NULL,
  PRIMARY KEY  (`awardId`),
  UNIQUE KEY `code` (`game`,`awardType`,`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `#DB_PREFIX#_Clans`;
CREATE TABLE `#DB_PREFIX#_Clans` (
  `clanId` int(10) unsigned NOT NULL auto_increment,
  `tag` varchar(32) NOT NULL default '',
  `name` varchar(128) NOT NULL default '',
  `homepage` varchar(64) NOT NULL default '',
  `game` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`clanId`),
  UNIQUE KEY `tag` (`game`,`tag`),
  KEY `game` (`game`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `#DB_PREFIX#_ClanTags`;
CREATE TABLE `#DB_PREFIX#_ClanTags` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pattern` varchar(64) NOT NULL default '',
  `position` enum('EITHER','START','END') NOT NULL default 'EITHER',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `pattern` (`pattern`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29 ;


INSERT INTO `#DB_PREFIX#_ClanTags` (`id`, `pattern`, `position`) VALUES
(1, '[AXXXXX]', 'EITHER'),
(2, '|AXXXXX|', 'EITHER'),
(3, '(AXXXXX)', 'EITHER'),
(4, '{AXXXXX}', 'EITHER'),
(5, '-=AXXX=-', 'START'),
(6, '=AXXXXX=', 'START'),
(7, '-AXXXXX-', 'START'),
(8, '=[AXXXX]=', 'START'),
(9, '-=|AXXXXXX|=-', 'EITHER'),
(10, '-=[AXXXXXX]=-', 'EITHER'),
(11, '-=]AXXXXXX[=-', 'EITHER'),
(12, '~{|AXXXXXX|}~', 'EITHER'),
(13, '-|-AXXXXXX-|-', 'EITHER'),
(14, '-(AXXXXXX)-', 'EITHER'),
(15, '::AXXXXXX::', 'EITHER'),
(16, '<<AXXXXXX>>', 'EITHER'),
(17, '{{AXXXXXX}}', 'EITHER'),
(18, '((AXXXXXX))', 'EITHER'),
(19, '.|AXXXXXX|.', 'EITHER'),
(20, '--AXXXXXX--', 'EITHER'),
(21, '-)AXXXXXX(-', 'EITHER'),
(22, '/AXXXXXX\\', 'EITHER'),
(23, '//AXXXXXX\\\\', 'EITHER'),
(24, '_AXXXXXX_', 'EITHER'),
(25, '_=|AXXXXXX|=_', 'EITHER'),
(26, '*AXXXXXX*', 'EITHER'),
(27, '.:AXXXXXX:', 'START'),
(28, '[(AXXXXXX)]', 'EITHER');

DROP TABLE IF EXISTS `#DB_PREFIX#_Events_Admin`;
CREATE TABLE `#DB_PREFIX#_Events_Admin` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `eventTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `serverId` int(10) unsigned NOT NULL default '0',
  `map` varchar(64) NOT NULL,
  `type` varchar(32) NOT NULL default 'Unknown',
  `message` varchar(128) NOT NULL default '',
  `playerName` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `#DB_PREFIX#_Events_ChangeName`;
CREATE TABLE `#DB_PREFIX#_Events_ChangeName` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `eventTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `serverId` int(10) unsigned NOT NULL default '0',
  `map` varchar(64) NOT NULL,
  `playerId` int(10) unsigned NOT NULL default '0',
  `oldName` varchar(64) NOT NULL default '',
  `newName` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `#DB_PREFIX#_Events_ChangeRole`;
CREATE TABLE `#DB_PREFIX#_Events_ChangeRole` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `eventTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `serverId` int(10) unsigned NOT NULL default '0',
  `map` varchar(64) NOT NULL,
  `playerId` int(10) unsigned NOT NULL default '0',
  `role` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `#DB_PREFIX#_Events_ChangeTeam`;
CREATE TABLE `#DB_PREFIX#_Events_ChangeTeam` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `eventTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `serverId` int(10) unsigned NOT NULL default '0',
  `map` varchar(64) NOT NULL,
  `playerId` int(10) unsigned NOT NULL default '0',
  `team` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `#DB_PREFIX#_Events_Connects`;
CREATE TABLE `#DB_PREFIX#_Events_Connects` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `eventTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `serverId` int(10) unsigned NOT NULL default '0',
  `map` varchar(64) NOT NULL,
  `playerId` int(10) unsigned NOT NULL default '0',
  `ipAddress` varchar(15) NOT NULL default '',
  `hostname` varchar(128) NOT NULL default '',
  `hostgroup` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `#DB_PREFIX#_Events_Disconnects`;
CREATE TABLE `#DB_PREFIX#_Events_Disconnects` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `eventTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `serverId` int(10) unsigned NOT NULL default '0',
  `map` varchar(64) NOT NULL,
  `playerId` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `#DB_PREFIX#_Events_Entries`;
CREATE TABLE `#DB_PREFIX#_Events_Entries` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `eventTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `serverId` int(10) unsigned NOT NULL default '0',
  `map` varchar(64) NOT NULL,
  `playerId` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `#DB_PREFIX#_Events_Frags`;
CREATE TABLE `#DB_PREFIX#_Events_Frags` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `eventTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `serverId` int(10) unsigned NOT NULL default '0',
  `map` varchar(64) NOT NULL,
  `killerId` int(10) unsigned NOT NULL default '0',
  `victimId` int(10) unsigned NOT NULL default '0',
  `weapon` varchar(64) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `#DB_PREFIX#_Events_PlayerActions`;
CREATE TABLE `#DB_PREFIX#_Events_PlayerActions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `eventTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `serverId` int(10) unsigned NOT NULL default '0',
  `map` varchar(64) NOT NULL,
  `playerId` int(10) unsigned NOT NULL default '0',
  `actionId` int(10) unsigned NOT NULL default '0',
  `bonus` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `#DB_PREFIX#_Events_PlayerPlayerActions`;
CREATE TABLE `#DB_PREFIX#_Events_PlayerPlayerActions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `eventTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `serverId` int(10) unsigned NOT NULL default '0',
  `map` varchar(64) NOT NULL,
  `playerId` int(10) unsigned NOT NULL default '0',
  `victimId` int(10) unsigned NOT NULL default '0',
  `actionId` int(10) unsigned NOT NULL default '0',
  `bonus` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `#DB_PREFIX#_Events_Rcon`;
CREATE TABLE `#DB_PREFIX#_Events_Rcon` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `eventTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `serverId` int(10) unsigned NOT NULL default '0',
  `map` varchar(64) NOT NULL,
  `type` varchar(6) NOT NULL default 'UNK',
  `remoteIp` varchar(15) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `command` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `#DB_PREFIX#_Events_Statsme`;
CREATE TABLE `#DB_PREFIX#_Events_Statsme` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `eventTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `serverId` int(10) unsigned NOT NULL default '0',
  `map` varchar(64) NOT NULL,
  `playerId` int(10) unsigned NOT NULL default '0',
  `weapon` varchar(64) NOT NULL default '',
  `shots` int(6) unsigned NOT NULL default '0',
  `hits` int(6) unsigned NOT NULL default '0',
  `headshots` int(6) unsigned NOT NULL default '0',
  `damage` int(6) unsigned NOT NULL default '0',
  `kills` int(6) unsigned NOT NULL default '0',
  `deaths` int(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `playerId` (`playerId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `#DB_PREFIX#_Events_Statsme2`;
CREATE TABLE `#DB_PREFIX#_Events_Statsme2` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `eventTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `serverId` int(10) unsigned NOT NULL default '0',
  `map` varchar(64) NOT NULL,
  `playerId` int(10) unsigned NOT NULL default '0',
  `weapon` varchar(64) NOT NULL default '',
  `head` int(6) unsigned NOT NULL default '0',
  `chest` int(6) unsigned NOT NULL default '0',
  `stomach` int(6) unsigned NOT NULL default '0',
  `leftarm` int(6) unsigned NOT NULL default '0',
  `rightarm` int(6) unsigned NOT NULL default '0',
  `leftleg` int(6) unsigned NOT NULL default '0',
  `rightleg` int(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `playerId` (`playerId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `#DB_PREFIX#_Events_StatsmeLatency`;
CREATE TABLE `#DB_PREFIX#_Events_StatsmeLatency` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `eventTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `serverId` int(10) unsigned NOT NULL default '0',
  `map` varchar(64) NOT NULL,
  `playerId` int(10) unsigned NOT NULL default '0',
  `ping` int(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `playerId` (`playerId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `#DB_PREFIX#_Events_StatsmeTime`;
CREATE TABLE `#DB_PREFIX#_Events_StatsmeTime` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `eventTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `serverId` int(10) unsigned NOT NULL default '0',
  `map` varchar(64) NOT NULL,
  `playerId` int(10) unsigned NOT NULL default '0',
  `time` time NOT NULL default '00:00:00',
  PRIMARY KEY  (`id`),
  KEY `playerId` (`playerId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `#DB_PREFIX#_Events_Suicides`;
CREATE TABLE `#DB_PREFIX#_Events_Suicides` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `eventTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `serverId` int(10) unsigned NOT NULL default '0',
  `map` varchar(64) NOT NULL,
  `playerId` int(10) unsigned NOT NULL default '0',
  `weapon` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `#DB_PREFIX#_Events_TeamBonuses`;
CREATE TABLE `#DB_PREFIX#_Events_TeamBonuses` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `eventTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `serverId` int(10) unsigned NOT NULL default '0',
  `map` varchar(64) NOT NULL,
  `playerId` int(10) unsigned NOT NULL default '0',
  `actionId` int(10) unsigned NOT NULL default '0',
  `bonus` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `#DB_PREFIX#_Events_Teamkills`;
CREATE TABLE `#DB_PREFIX#_Events_Teamkills` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `eventTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `serverId` int(10) unsigned NOT NULL default '0',
  `map` varchar(64) NOT NULL,
  `killerId` int(10) unsigned NOT NULL default '0',
  `victimId` int(10) unsigned NOT NULL default '0',
  `weapon` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `#DB_PREFIX#_Games`;
CREATE TABLE `#DB_PREFIX#_Games` (
  `code` varchar(16) NOT NULL default '',
  `name` varchar(128) NOT NULL default '',
  `source` tinyint(1) NOT NULL default '0',
  `hidden` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#DB_PREFIX#_HostGroups`;
CREATE TABLE `#DB_PREFIX#_HostGroups` (
  `id` int(11) NOT NULL auto_increment,
  `pattern` varchar(128) NOT NULL default '',
  `name` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `#DB_PREFIX#_News`;
CREATE TABLE `#DB_PREFIX#_News` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `date` varchar(32) NOT NULL,
  `user` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `subject` varchar(128) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;


INSERT INTO `#DB_PREFIX#_News` (`id`, `date`, `user`, `email`, `subject`, `message`) VALUES
(1, '2007-12-11 10:17:25', 'admin', 'admin@website.com', 'The first news', 'This is the first news of the news Plugin. You can edit and add news at the admin interface. You can also hide the news at the admin options section.');

DROP TABLE IF EXISTS `#DB_PREFIX#_Options`;
CREATE TABLE `#DB_PREFIX#_Options` (
  `keyname` varchar(32) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`keyname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#DB_PREFIX#_Options` (`keyname`, `value`) VALUES
('font_normal', '<font face="Verdana,Arial,Helvetica" size="2" class="fontNormal">'),
('fontend_normal', '</font>'),
('font_small', '<font face="Verdana,Arial,Helvetica" size="1" class="fontSmall">'),
('fontend_small', '</font>'),
('font_title', '<font face="Arial,Helvetica" size="3" class="fontTitle"><b>'),
('fontend_title', '</b></font>'),
('table_bgcolor1', '#283846'),
('table_bgcolor2', '#1F2F3D'),
('table_wpnbgcolor', '#253546'),
('table_border', '#39495A'),
('table_head_text', '#C6C6C6'),
('table_head_bgcolor', '#39495A'),
('location_link', '#FFFFFF'),
('location_text', '#FFFFFF'),
('location_bgcolor', '#39495A'),
('body_leftmargin', '10'),
('body_topmargin', '15'),
('body_alink', '#C6C6C6'),
('body_link', '#C6C6C6'),
('body_hlink', '#FFFFFF'),
('body_vlink', '#C6C6C6'),
('body_text', '#C6C6C6'),
('body_bgcolor', '#253546'),
('body_background', ''),
('imgdir', 'hlstatsimg/'),
('contact', 'mailto:admin@example.com'),
('sitename', 'Some Site'),
('siteurl', 'http://www.example.com'),
('style', 'ua_style'),
('hideAwards', '0'),
('hideNews', '0'),
('imgpath', ''),
('map_dlurl', ''),
('reset_date', '1203413710'),
('useFlash', '0'),
('allowSig', '0'),
('allowXML', '0'),
('scripturl', '');

DROP TABLE IF EXISTS `#DB_PREFIX#_PlayerNames`;
CREATE TABLE `#DB_PREFIX#_PlayerNames` (
  `playerId` int(10) unsigned NOT NULL default '0',
  `name` varchar(64) NOT NULL default '',
  `lastuse` datetime NOT NULL default '0000-00-00 00:00:00',
  `numuses` int(10) unsigned NOT NULL default '0',
  `kills` int(11) NOT NULL default '0',
  `deaths` int(11) NOT NULL default '0',
  `suicides` int(11) NOT NULL default '0',
  PRIMARY KEY  (`playerId`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#DB_PREFIX#_Players`;
CREATE TABLE `#DB_PREFIX#_Players` (
  `playerId` int(10) unsigned NOT NULL auto_increment,
  `lastName` varchar(64) NOT NULL default '',
  `clan` int(10) unsigned NOT NULL default '0',
  `kills` int(11) NOT NULL default '0',
  `deaths` int(11) NOT NULL default '0',
  `suicides` int(11) NOT NULL default '0',
  `skill` int(11) NOT NULL default '1000',
  `oldSkill` int(11) NOT NULL default '1000',
  `fullName` varchar(128) default NULL,
  `email` varchar(128) default NULL,
  `homepage` varchar(128) default NULL,
  `icq` int(10) unsigned default NULL,
  `game` varchar(16) NOT NULL default '',
  `hideranking` int(1) unsigned NOT NULL default '0',
  `rating` float NOT NULL default '1500',
  `rd2` float NOT NULL default '122500',
  `rating_last` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`playerId`),
  KEY `clan` (`clan`),
  KEY `game` (`game`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `#DB_PREFIX#_PlayerUniqueIds`;
CREATE TABLE `#DB_PREFIX#_PlayerUniqueIds` (
  `playerId` int(10) unsigned NOT NULL default '0',
  `uniqueId` varchar(64) NOT NULL default '',
  `game` varchar(16) NOT NULL default '',
  `merge` int(10) unsigned default NULL,
  PRIMARY KEY  (`uniqueId`,`game`),
  KEY `playerId` (`playerId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#DB_PREFIX#_Roles`;
CREATE TABLE `#DB_PREFIX#_Roles` (
  `roleId` int(10) unsigned NOT NULL auto_increment,
  `game` varchar(16) NOT NULL default 'valve',
  `code` varchar(32) NOT NULL default '',
  `name` varchar(64) NOT NULL default '',
  `hidden` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`roleId`),
  UNIQUE KEY `gamecode` (`game`,`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `#DB_PREFIX#_Servers`;
CREATE TABLE `#DB_PREFIX#_Servers` (
  `serverId` int(10) unsigned NOT NULL auto_increment,
  `address` varchar(15) NOT NULL default '',
  `port` int(5) unsigned NOT NULL default '0',
  `name` varchar(64) NOT NULL default '',
  `game` varchar(16) NOT NULL default 'valve',
  `publicaddress` varchar(64) NOT NULL default '',
  `statusurl` varchar(255) default NULL,
  `rcon_password` varchar(48) NOT NULL default '',
  `defaultMap` VARCHAR( 128 ) NOT NULL,
  PRIMARY KEY  (`serverId`),
  UNIQUE KEY `addressport` (`address`,`port`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `#DB_PREFIX#_Server_Addons`;
CREATE TABLE `#DB_PREFIX#_Server_Addons` (
  `rule` varchar(64) NOT NULL default '',
  `addon` varchar(64) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`rule`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#DB_PREFIX#_Server_Addons` (`rule`, `addon`, `url`) VALUES
('hlg_version', 'HLGuard %', 'http://www.thezproject.org/projects.php?pid=1'),
('clanmod_version', 'ClanMod %', 'http://www.unitedadmins.com/index.php?p=content&content=clanmod'),
('statsme_version', 'StatsMe %', 'http://www.unitedadmins.com/index.php?p=content&content=statsme'),
('phpua_mm_version', 'phpUA %', 'http://www.phpua.com'),
('cdversion', 'Cheating-Death %', 'http://www.unitedadmins.com/index.php?p=content&content=cd'),
('metamod_version', 'MetaMod %', 'http://www.metamod.org'),
('amxmodx_version', 'AMXX %', 'http://www.amxmodx.org'),
('sbsrv_version', 'Steambans %', 'http://www.steambans.com'),
('sourcemod_version', 'SourceMod %', 'http://www.sourcemod.net'),
('logmod_version', 'LogMod %', 'http://www.hlsw.org/other_projects/logmod/');

DROP TABLE IF EXISTS `#DB_PREFIX#_Style`;
CREATE TABLE `#DB_PREFIX#_Style` (
  `keyname` varchar(32) NOT NULL default '',
  `def` varchar(255) NOT NULL default '',
  `black` varchar(255) NOT NULL default '',
  `light_blue` varchar(255) NOT NULL default '',
  `grey` varchar(255) NOT NULL default '',
  `ua_style` varchar(255) NOT NULL default '',
  `red` varchar(255) NOT NULL default '',
  `light_grey` varchar(255) NOT NULL default '',
  `white` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`keyname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


INSERT INTO `#DB_PREFIX#_Style` (`keyname`, `def`, `black`, `light_blue`, `grey`, `ua_style`, `red`, `light_grey`, `white`) VALUES
('font_normal', '<font face="Verdana, Arial, sans-serif" size=2 class="fontNormal">', '<font face="Courier New, Courier" size=2 class="fontNormal">', '<font face="Verdana, Arial, sans-serif" size=2 class="fontNormal">', '<font face="Georgia, Times New Roman, Times, serif" size=2 class="fontNormal">', '<font face="Verdana,Arial,Helvetica" size="2" class="fontNormal">', '<font face="Verdana, Arial, sans-serif" size=2 class="fontNormal">', '<font face="Georgia, Times New Roman, Times, serif" size=2 class="fontNormal">', '<font face="Geneva, Arial, Helvetica, sans-serif" size=2 class="fontNormal">'),
('fontend_normal', '</font>', '</font>', '</font>', '</font>', '</font>', '</font>', '</font>', '</font>'),
('font_small', '<font face="Verdana, Arial, sans-serif" size=1 class="fontSmall">', '<font face="Courier New, Courier" size=1 class="fontSmall">', '<font face="Verdana, Arial, sans-serif" size=1 class="fontSmall">', '<font face="Georgia, Times New Roman, Times, serif" size=1 class="fontSmall">', '<font face="Verdana,Arial,Helvetica" size="1" class="fontSmall">', '<font face="Verdana, Arial, sans-serif" size=1 class="fontSmall">', '<font face="Georgia, Times New Roman, Times, serif" size=1 class="fontSmall">', '<font face="Geneva, Arial, Helvetica, sans-serif" size=1 class="fontSmall">'),
('fontend_small', '</font>', '</font>', '</font>', '</font>', '</font>', '</font>', '</font>', '</font>'),
('font_title', '<font face="Arial, sans-serif" size=4 class="fontTitle"><b>', '<font face="Courier New, Courier" size=4 class="fontTitle"><b>', '<font face="Arial, sans-serif" size=4 class="fontTitle"><b>', '<font face="Georgia, Times New Roman, Times, serif" size=4 class="fontTitle"><b>', '<font face="Arial,Helvetica" size="3" class="fontTitle"><b>', '<font face="Arial, sans-serif" size=4 class="fontTitle"><b>', '<font face="Georgia, Times New Roman, Times, serif" size=4 class="fontTitle"><b>', '<font face="Geneva, Arial, Helvetica, sans-serif" size=4 class="fontTitle"><b>'),
('fontend_title', '</b></font>', '</b></font>', '</b></font>', '</b></font>', '</b></font>', '</b></font>', '</b></font>', '</b></font>'),
('table_bgcolor1', '#15154D', '#282828', '#3F5576', '#CCCCCC', '#283846', '#550000', '#FFFFFF', '#edf3f9'),
('table_bgcolor2', '#161652', '#282828', '#3F5569', '#999999', '#1F2F3D', '#440000', '#FAFAFA', '#d0dbe6'),
('table_wpnbgcolor', '#000000', '#000000', '#3F5569', '#999999', '#253546', '#440000', '#EAEAEA', '#d0dbe6'),
('table_border', '#001B73', '#E0E0E0', '#59748D', '#333333', '#39495A', '#000000', '#000000', '#000000'),
('table_head_text', '#EEEEEE', '#EEEEEE', '#EEEEEE', '#FFFFFF', '#C6C6C6', '#FFFFFF', '#000000', '#000000'),
('table_head_bgcolor', '#002E8A', '#282828', '#3F5576', '#666666', '#39495A', '#660000', '#AEAEAE', '#E8E8E8'),
('location_link', '#FFFFFF', '#FFFFFF', '#FFFFFF', '#808080', '#FFFFFF', '#FFFFFF', '#AEAEAE', '#000000'),
('location_text', '#FFFFFF', '#FFFFFF', '#FFFFFF', '#000000', '#FFFFFF', '#FFFFFF', '#000000', '#000000'),
('location_bgcolor', '#003399', '#003399', '#3F5576', '#AAAAAA', '#39495A', '#660000', '#EAEAEA', '#E8E8E8'),
('body_leftmargin', '10', '10', '10', '5', '10', '10', '10', '10'),
('body_topmargin', '15', '15', '15', '8', '15', '15', '15', '15'),
('body_alink', '#FFBB00', '#FFFFFF', '#B0B0B0', '#808080', '#C6C6C6', '#800000', '#AEAEAE', '#000000'),
('body_link', '#FF9900', '#FFFFFF', '#B0B0B0', '#808080', '#C6C6C6', '#C0C0C0', '#AEAEAE', '#000000'),
('body_text', '#EEEEEE', '#CCCCCC', '#FFFFFF', '#000000', '#C6C6C6', '#FFFFFF', '#000000', '#000000'),
('body_vlink', '#FF9000', '#FFFFFF', '#B0B0B0', '#808080', '#C6C6C6', '#C0C0C0', '#AEAEAE', '#000000'),
('body_bgcolor', '#0C1D40', '#000000', '#3F5569', '#AAAAAA', '#253546', '#000000', '#EAEAEA', '#FFFFFF'),
('body_background', '', '', '', '', '', '', '', ''),
('body_hlink', '#FF9900', '#494949', '#FFFFFF', '#FFFFFF', '#FFFFFF', '#800000', '#7A7A7A', '#404040');

DROP TABLE IF EXISTS `#DB_PREFIX#_Teams`;
CREATE TABLE `#DB_PREFIX#_Teams` (
  `teamId` int(10) unsigned NOT NULL auto_increment,
  `game` varchar(16) NOT NULL default 'valve',
  `code` varchar(32) NOT NULL default '',
  `name` varchar(64) NOT NULL default '',
  `hidden` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`teamId`),
  UNIQUE KEY `gamecode` (`game`,`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `#DB_PREFIX#_Users`;
CREATE TABLE `#DB_PREFIX#_Users` (
  `username` varchar(16) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `acclevel` int(11) NOT NULL default '0',
  `playerId` int(11) NOT NULL default '0',
  PRIMARY KEY  (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


INSERT INTO `#DB_PREFIX#_Users` (`username`, `password`, `acclevel`, `playerId`) VALUES
('admin', 'e10adc3949ba59abbe56e057f20f883e', 100, 0);

DROP TABLE IF EXISTS `#DB_PREFIX#_Weapons`;
CREATE TABLE `#DB_PREFIX#_Weapons` (
  `weaponId` int(10) unsigned NOT NULL auto_increment,
  `game` varchar(16) NOT NULL default 'valve',
  `code` varchar(32) NOT NULL default '',
  `name` varchar(64) NOT NULL default '',
  `modifier` float(10,2) NOT NULL default '1.00',
  PRIMARY KEY  (`weaponId`),
  UNIQUE KEY `gamecode` (`game`,`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `#DB_PREFIX#_Events_Chat`;
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
