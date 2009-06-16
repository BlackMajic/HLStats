#
# HLstats Game Support file for Insurgency
# ----------------------------------------------------
#
# If you want to insert this manually and not via the installer
# replace ++DB_PREFIX++ with the current table prefix !

#
# Game Definition
#
INSERT IGNORE INTO ++DB_PREFIX++_Games VALUES ('insurgency','Insurgency: Modern Infantry Combat','1','0');


#
# Awards
#
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'O','insurgency','weapon_kabar','Knife stabber','knife kills',NULL,NULL);

#
# Player Actions
#
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'insurgency','kill_teammate',-10,0,'Iraqi Insurgents','Killed Teammate','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'insurgency','kill_teammate',-10,0,'U.S. Marines','Killed Teammate','1','0','0','0');


#
# Teams
#
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'insurgency','Iraqi Insurgents','Iraqi Insurgents','0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'insurgency','U.S. Marines','U.S. Marines','0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'insurgency','Spectator','Spectator','0');


#
# Weapons
#
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'insurgency','weapon_kabar','KA-BAR Knife',2.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'insurgency','weapon_bayonet','Bayonet',2.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'insurgency','weapon_makarov','Soviet Makarov',1.70);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'insurgency','weapon_m9','9mm Beretta Pistol',1.70);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'insurgency','world','RPG or Grenade',1.20);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'insurgency','weapon_toz','TOZ Rifle',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'insurgency','weapon_svd','Dragunov Sniper Rifle',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'insurgency','weapon_sks','Simonov SKS carbine',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'insurgency','weapon_rpk','RPK Ruchnoy Pulemyot Kalashnikova',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'insurgency','weapon_m67','M67 Recoilless Rifle',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'insurgency','weapon_m4med','M4 Medium Rangle Rifle',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'insurgency','weapon_m4','M4 Carbine Rifle',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'insurgency','weapon_m249','M249 SAW (Squad Automatic Weapon)',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'insurgency','weapon_m18','M18 Rifle',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'insurgency','weapon_m16m203','M16 carbine M203 Grenade Launcher',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'insurgency','weapon_m16a4','M16A4 Infantry Rifle',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'insurgency','weapon_m14','Colt M14 Carbine',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'insurgency','weapon_m1014','M1014 Auto-Shotgun',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'insurgency','weapon_l42a1','Enfield L42A1 Sniper',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'insurgency','weapon_fnfa1','FN FAL Rifle',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'insurgency','weapon_aks74u','AKS-74U Rifle',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'insurgency','weapon_ak47','AK-47 Rifle',1.00);
