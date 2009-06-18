#
# HLstats Game Support file for Counter-Strike: Source
# ------------------------------------------------------
#
# If you want to insert this manually and not via the installer
# replace ++DB_PREFIX++ with the current table prefix !


#
# Game Definition
#
INSERT IGNORE INTO ++DB_PREFIX++_Games VALUES ('css','Counter-Strike: Source','1','0');


#
# Awards
#
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'O','css','Defused_The_Bomb','Top Defuser','bomb defusions',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'O','css','Planted_The_Bomb','Top Demolitionist','bomb plantings',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'O','css','Rescued_A_Hostage','Top Hostage Rescuer','hostages rescued',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'O','css','Assassinated_The_VIP','Top Assassin','assassinations',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','css','elite','Dual Berretta Elites','kills with elite',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','css','knife','Knife Maniac','knifings',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','css','awp','AWP','snipings with awp',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','css','p90','P90','kills with p90',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','css','deagle','Desert Eagle','kills with deagle',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','css','m3','Shotgun','kills with m3 shotgun',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','css','usp','USP Master','kills with usp',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','css','m4a1','Colt M4A1 Carbine','kills with m4a1',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','css','glock','Glock','kills with glock18',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','css','ak47','AK47','kills with ak47',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','css','famas','Fusil Automatique','kills with famas',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','css','galil','Galil','kills with galil',NULL,NULL);


#
# Player Actions
#
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'css','Begin_Bomb_Defuse_Without_Kit',0,0,'CT','Start Defusing the Bomb Without a Defuse Kit','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'css','Begin_Bomb_Defuse_With_Kit',0,0,'CT','Start Defusing the Bomb With a Defuse Kit','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'css','Assassinated_The_VIP',10,0,'TERRORIST','Assassinate the VIP','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'css','Planted_The_Bomb',15,2,'TERRORIST','Plant the Bomb','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'css','Defused_The_Bomb',15,0,'CT','Defuse the Bomb','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'css','Touched_A_Hostage',2,0,'CT','Touch a Hostage','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'css','Rescued_A_Hostage',5,1,'CT','Rescue a Hostage','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'css','Killed_A_Hostage',-25,1,'CT','Kill a Hostage','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'css','Became_VIP',1,0,'CT','Become the VIP','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'css','Spawned_With_The_Bomb',2,0,'TERRORIST','Spawn with the Bomb','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'css','Got_The_Bomb',2,0,'TERRORIST','Pick up the Bomb','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'css','Dropped_The_Bomb',-2,0,'TERRORIST','Drop the Bomb','1','0','0','0');


#
# Team Actions
#
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'css','CTs_Win',0,2,'CT','All Terrorists eliminated','0','0','1','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'css','Terrorists_Win',0,2,'TERRORIST','All Counter-Terrorists eliminated','0','0','1','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'css','All_Hostages_Rescued',0,10,'CT','Counter-Terrorists rescued all the hostages','0','0','1','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'css','Target_Bombed',0,10,'TERRORIST','Terrorists bombed the target','0','0','1','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'css','VIP_Assassinated',0,6,'TERRORIST','Terrorists assassinated the VIP','0','0','1','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'css','Bomb_Defused',0,6,'CT','Counter-Terrorists defused the bomb','0','0','1','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'css','VIP_Escaped',0,10,'CT','VIP escaped','0','0','1','0');


#
# Teams
#
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'css','TERRORIST','Terrorist','0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'css','CT','Counter-Terrorist','0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'css','SPECTATOR','Spectator','0');


#
# Weapons
#
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','knife','Bundeswehr Advanced Combat Knife',1.80);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','usp','H&K USP .45 Tactical',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','glock','Glock 18 Select Fire',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','deagle','Desert Eagle .50AE',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','p228','Sig Sauer P-228',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','m3','Benelli M3 Super 90 Combat',1.40);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','xm1014','Benelli/H&K M4 Super 90 XM1014',1.40);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','mp5navy','H&K MP5-Navy',1.25);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','tmp','Steyr Tactical Machine Pistol',1.25);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','p90','FN P90',1.25);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','m4a1','Colt M4A1 Carbine',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','ak47','Kalashnikov AK-47',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','sg552','Sig Sauer SG-552 Commando',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','scout','Steyr Scout',1.60);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','awp','Arctic Warfare Magnum (Police)',1.40);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','g3sg1','H&K G3/SG1 Sniper Rifle',1.40);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','m249','M249 PARA Light Machine Gun',0.80);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','grenade','High Explosive Grenade',1.80);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','flashbang','Flashbang',3.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','smokegrenade_projectile','Smoke Grenade',3.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','elite','Dual Beretta 96G Elite',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','aug','Steyr Aug',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','mac10','Ingram MAC-10',1.25);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','fiveseven','FN Five-Seven',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','ump45','H&K UMP45',1.25);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','sg550','Sig SG-550 Sniper',1.70);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','famas','Fusil Automatique',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','galil','Galil',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'css','PROP_PHYSICS','Rare Physics kill',10.00);
