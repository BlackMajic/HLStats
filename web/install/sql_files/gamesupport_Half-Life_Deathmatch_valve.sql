#
# HLstats Game Support file for Half-Life Deathmatch
# ----------------------------------------------------
#
# If you want to insert this manually and not via the installer
# replace ++DB_PREFIX++ with the current table prefix !


#
# Game Definition
#
INSERT IGNORE INTO ++DB_PREFIX++_Games VALUES ('valve','Half-Life Deathmatch','0','0');


#
# Awards
#
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','valve','357','357','kills with 357',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','valve','9mmAR','MP5','kills with 9mmAR',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','valve','9mmhandgun','Glock','kills with 9mmhandgun',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','valve','bolt','Crossbow Sniper','kills with bolt',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','valve','crowbar','Crowbar Maniac','murders with crowbar',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','valve','gluon gun','Gauss King','kills with gluon gun',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','valve','tau_cannon','Egon','kills with tau_cannon',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','valve','grenade','Grenadier','kills with grenade',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','valve','hornet','Hornet Master','kills with hornet',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','valve','rpg_rocket','Rocketeer','kills with rocket',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','valve','satchel','Lord Satchel','kills with satchel',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','valve','shotgun','Redneck','kills with shotgun',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','valve','snark','Snark Master','kills with snark',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','valve','tripmine','Shady Assassin','kills with tripmine',NULL,NULL);


#
# Weapons
#
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'valve','357','357 Revolver',1.60);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'valve','9mmAR','9mm Automatic Rifle',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'valve','9mmhandgun','9mm Handgun',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'valve','bolt','Crossbow Bolt',1.70);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'valve','crossbow','Crossbow',1.40);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'valve','crowbar','Crowbar',1.90);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'valve','tau_cannon','Egon Tau Cannon / Rail Gun',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'valve','gluon gun','Gluon / Gauss Gun',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'valve','grenade','Grenade',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'valve','hornet','Hornet',1.30);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'valve','rpg_rocket','Rocket Propelled Grenade',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'valve','satchel','Satchel Charge',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'valve','shotgun','Shotgun',1.20);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'valve','snark','Snark',1.80);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'valve','tripmine','Trip Mine',1.60);