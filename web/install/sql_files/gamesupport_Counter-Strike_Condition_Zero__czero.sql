#
# HLstats Game Support file for Counter-Strike: Condition Zero
# --------------------------------------------------------------
#
# If you want to insert this manually and not via the installer
# replace ++DB_PREFIX++ with the current table prefix !


#
# Game Definition
#
INSERT IGNORE INTO ++DB_PREFIX++_Games VALUES ('czero','Counter-Strike: Condition Zero','0','0');


#
# Awards
#
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'O','czero','Defused_The_Bomb','Top Defuser','bomb defusions',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'O','czero','Planted_The_Bomb','Top Demolitionist','bomb plantings',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'O','czero','Rescued_A_Hostage','Top Hostage Rescuer','hostages rescued',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'O','czero','Assassinated_The_VIP','Top Assassin','assassinations',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','czero','elite','Dual Berretta Elites','kills with elite',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','czero','knife','Knife Maniac','knifings',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','czero','awp','AWP','snipings with awp',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','czero','p90','P90','kills with p90',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','czero','deagle','Desert Eagle','kills with deagle',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','czero','m3','Shotgun','kills with m3 shotgun',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','czero','usp','USP Master','kills with usp',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','czero','m4a1','Colt M4A1 Carbine','kills with m4a1',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','czero','glock18','Glock','kills with glock18',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','czero','ak47','AK47','kills with ak47',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','czero','famas','Fusil Automatique','kills with famas',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','czero','galil','Galil','kills with galil',NULL,NULL);


#
# Player Actions
#
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'czero','Begin_Bomb_Defuse_Without_Kit',0,0,'CT','Start Defusing the Bomb Without a Defuse Kit','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'czero','Begin_Bomb_Defuse_With_Kit',0,0,'CT','Start Defusing the Bomb With a Defuse Kit','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'czero','Assassinated_The_VIP',10,0,'TERRORIST','Assassinate the VIP','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'czero','Planted_The_Bomb',15,2,'TERRORIST','Plant the Bomb','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'czero','Defused_The_Bomb',15,0,'CT','Defuse the Bomb','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'czero','Touched_A_Hostage',2,0,'CT','Touch a Hostage','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'czero','Rescued_A_Hostage',5,1,'CT','Rescue a Hostage','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'czero','Killed_A_Hostage',-25,1,'CT','Kill a Hostage','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'czero','Became_VIP',1,0,'CT','Become the VIP','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'czero','Spawned_With_The_Bomb',2,0,'TERRORIST','Spawn with the Bomb','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'czero','Got_The_Bomb',2,0,'TERRORIST','Pick up the Bomb','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'czero','Dropped_The_Bomb',-2,0,'TERRORIST','Drop the Bomb','1','0','0','0');


#
# Team Actions
#
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'czero','CTs_Win',0,2,'CT','All Terrorists eliminated','0','0','1','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'czero','Terrorists_Win',0,2,'TERRORIST','All Counter-Terrorists eliminated','0','0','1','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'czero','All_Hostages_Rescued',0,10,'CT','Counter-Terrorists rescued all the hostages','0','0','1','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'czero','Target_Bombed',0,10,'TERRORIST','Terrorists bombed the target','0','0','1','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'czero','VIP_Assassinated',0,6,'TERRORIST','Terrorists assassinated the VIP','0','0','1','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'czero','Bomb_Defused',0,6,'CT','Counter-Terrorists defused the bomb','0','0','1','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'czero','VIP_Escaped',0,10,'CT','VIP escaped','0','0','1','0');


#
# Teams
#
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'czero','TERRORIST','Terrorist','0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'czero','CT','Counter-Terrorist','0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'czero','SPECTATOR','Spectator','0');


#
# Weapons
#
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'czero','knife','Bundeswehr Advanced Combat Knife',1.80);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'czero','usp','H&K USP .45 Tactical',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'czero','glock18','Glock 18 Select Fire',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'czero','deagle','Desert Eagle .50AE',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'czero','p228','Sig Sauer P-228',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'czero','m3','Benelli M3 Super 90 Combat',1.40);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'czero','xm1014','Benelli/H&K M4 Super 90 XM1014',1.40);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'czero','mp5navy','H&K MP5-Navy',1.25);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'czero','tmp','Steyr Tactical Machine Pistol',1.25);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'czero','p90','FN P90',1.25);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'czero','m4a1','Colt M4A1 Carbine',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'czero','ak47','Kalashnikov AK-47',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'czero','sg552','Sig Sauer SG-552 Commando',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'czero','scout','Steyr Scout',1.60);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'czero','awp','Arctic Warfare Magnum (Police)',1.40);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'czero','g3sg1','H&K G3/SG1 Sniper Rifle',1.40);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'czero','m249','M249 PARA Light Machine Gun',0.80);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'czero','grenade','High Explosive Grenade',1.80);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'czero','elite','Dual Beretta 96G Elite',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'czero','aug','Steyr Aug',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'czero','mac10','Ingram MAC-10',1.25);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'czero','fiveseven','FN Five-Seven',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'czero','ump45','H&K UMP45',1.25);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'czero','sg550','Sig SG-550 Sniper',1.70);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'czero','famas','Fusil Automatique',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'czero','galil','Galil',1.00);