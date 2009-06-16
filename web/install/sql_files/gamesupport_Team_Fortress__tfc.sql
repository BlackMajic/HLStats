#
# HLstats Game Support file for Team Fortress 1.5
# ----------------------------------------------------
#
# If you want to insert this manually and not via the installer
# replace ++DB_PREFIX++ with the current table prefix !


#
# Game Definition
#
INSERT IGNORE INTO ++DB_PREFIX++_Games VALUES ('tfc','Team Fortress','0','0');


#
# Awards
#
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','tfc','axe','Crowbar Maniac','murders with crowbar',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','tfc','spanner','Evil Engie','bludgeonings with spanner',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','tfc','rocket','Rocketeer','kills with rocket',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','tfc','ac','HWGuy Extraordinaire','ownings with ac',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','tfc','sniperrifle','Red Dot Special','snipings',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','tfc','flames','Fire Man','roastings',NULL,NULL);


#
# Player Actions
#
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'tfc','rock2_goalitem',5,0,'','(rock2) Pick up keycard','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'tfc','rock2_bcave1',10,3,'1','(rock2) Blow Red Cave','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'tfc','rock2_rcave1',10,3,'2','(rock2) Blow Blue Cave','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'tfc','rock2_rholedet',10,3,'2','(rock2) Blow Blue Yard','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'tfc','rock2_bholedet',10,3,'1','(rock2) Blow Red Yard','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'tfc','team_two_dropoff',75,25,'2','Captured Blue Flag','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'tfc','team_one_dropoff',75,25,'1','Captured Red Flag','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'tfc','Team_2_dropoff',75,25,'2','Captured Blue Flag','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'tfc','Team_1_dropoff',75,25,'1','Captured Red Flag','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'tfc','Teleporter_Entrance_Destroyed',5,0,'0','eleporter Entrance Destroyed','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'tfc','Teleporter_Entrance_Finished',8,0,'0','Teleporter Entrance Build','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'tfc','Teleporter_Exit_Destroyed',5,0,'0','Teleporter Exit Destroyed','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'tfc','Teleporter_Exit_Finished',8,0,'0','Teleporter Exit Build','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'tfc','Built_Dispenser',8,0,'0','Built Dispenser','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'tfc','Discovered_Spy',2,0,'0','Discovered a Spy','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'tfc','Dispenser_Destroyed',5,0,'0','Dispenser Destroyed','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'tfc','Built_Dispenser',8,0,'0','Built Dispenser','1','0','0','0');


#
# Teams
#
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'tfc','1','Blue','0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'tfc','2','Red','0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'tfc','3','Yellow','0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'tfc','4','Green','0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'tfc','#Hunted_team1','(Hunted) VIP','0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'tfc','#Hunted_team2','(Hunted) Bodyguards','0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'tfc','#Hunted_team3','(Hunted) Assassins','0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'tfc','#Dustbowl_team1','Attackers','0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'tfc','#Dustbowl_team2','Defenders','0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'tfc','SPECTATOR','Spectator','0');


#
# Roles
#
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'tfc','Scout','Scout','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'tfc','Sniper','Sniper','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'tfc','Soldier','Soldier','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'tfc','Demoman','Demoman','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'tfc','Medic','Medic','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'tfc','HWGuy','HWGuy','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'tfc','Pyro','Pyro','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'tfc','Spy','Spy','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'tfc','Engineer','Engineer','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'tfc','RandomPC','Random','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'tfc','Civilian','The Hunted','0');



#
# Weapons
#
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'tfc','sniperrifle','Sniper Rifle',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'tfc','normalgrenade','Normal Grenade',1.10);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'tfc','ac','Autocannon',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'tfc','rocket','Rocket Launcher',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'tfc','sentrygun','Sentry Gun',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'tfc','supershotgun','Super Shotgun',1.15);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'tfc','autorifle','Sniper Rifle (Auto Mode)',1.20);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'tfc','empgrenade','EMP Grenade',1.25);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'tfc','mirvgrenade','MIRV Grenade',1.25);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'tfc','gl_grenade','Grenade Launcher',1.35);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'tfc','pipebomb','Pipebomb',1.35);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'tfc','timer','Infection Timer',0.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'tfc','infection','Infection',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'tfc','flames','Flame Thrower',1.60);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'tfc','shotgun','Shotgun',1.60);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'tfc','nails','Nail Gun',1.70);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'tfc','nailgrenade','Nail Grenade',1.70);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'tfc','supernails','Super Nail Gun',1.65);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'tfc','axe','Crowbar',1.80);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'tfc','medikit','Medikit',1.85);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'tfc','napalmgrenade','Napalm Grenade',1.70);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'tfc','detpack','Detpack',1.80);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'tfc','gasgrenade','Gas Grenade',1.90);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'tfc','spanner','Spanner',2.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'tfc','caltrop','Caltrops',2.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'tfc','railgun','Rail Gun',1.85);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'tfc','building_dispenser','Dispenser',2.00);
