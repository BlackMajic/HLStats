#
# HLstats Game Support file for DMC, ya know, Quake styles
# --------------------------------------------------------
#
# If you want to insert this manually and not via the installer
# replace ++DB_PREFIX++ with the current table prefix !


#
# Game Definition
#
INSERT IGNORE INTO ++DB_PREFIX++_Games VALUES ('dmc','DeathMatch Classic','0','0');


#
# Awards
#
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dmc','axe','Axe Murderer','hackings',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dmc','shotgun','Load of Buckshot','kills with shotgun',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dmc','doubleshotgun','Twin Shotty','kills with double shotgun',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dmc','spike','NailGunner','nailings',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dmc','superspike','Das Perforatah','puncturings',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dmc','grenade','Pineapples!','kills with grenades',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dmc','missile','Rocket Rider','kills with rockets',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dmc','lightning','Electrician','zappings',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dmc','teledeath','Mr. Timing','telefrags',NULL,NULL);

#
# Player Actions
#

#
# Team Actions
#

#
# Teams
#
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'dmc','RED','Red Team','0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'dmc','BLUE','Blue Team','0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'dmc','SPECTATOR','Spectator','0');

#
# Weapons
#
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'dmc','axe','Axe', 3.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'dmc','shotgun','Shotgun',2.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'dmc','doubleshotgun','Double-Barrelled Shotgun',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'dmc','spike','Nailgun',2.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'dmc','superspike','Perforator',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'dmc','grenade','Grenade Launcher',1.30);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'dmc','missile','Rocket Launcher',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'dmc','lightning','Thunderbolt',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'dmc','teledeath','Telefrag',3.00);
