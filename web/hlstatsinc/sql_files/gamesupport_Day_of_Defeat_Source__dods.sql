#
# HLstats Game Support file for Day of Defeat: Source
# ----------------------------------------------------
#
# If you want to insert this manually and not via the installer
# replace ++DB_PREFIX++ with the current table prefix !


#
# Game Definition
#
INSERT IGNORE INTO ++DB_PREFIX++_Games VALUES ('dods','Day of Defeat: Source','1','0');


#
# Awards
#
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dods','amerknife','Backstabbing Beotch','kills with the American Knife',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dods','mp40','MP40 Hor','kills with the MP40 Machine Pistol',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dods','spade','Shovel God','kills with the spade',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dods','mp44','MP44 Hor','kills with the MP44 Assault Rifle',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dods','colt','Colt Freak','kills with the Colt .45 model 1911',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dods','garand','GarandMeister','kills with the M1 Garand Rifle',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dods','thompson','Thompson Hor','kills with the Thompson Submachine Gun',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dods','spring','Spring Sniper','snipings with the Springfield 03 Rifle',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dods','bar','Bar Browning Hor','kills with the BAR Browning Automatic Rifle',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dods','frag_us','McVeigh Alert','bombings with the U.S. Grenade',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dods','frag_ger','Grenade Freak','bombings with the German Grenade',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dods','bazooka','Bazooka Joe','kills with the Bazooka',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dods','pschreck','Panzerschreck Hans','kills with the Panzerschreck',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dods','mg42','German Machine Hor','kills with the MG42',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'O','dods','bomb_defuse', 'Top Defuser', 'bomb defusions', NULL, NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'O','dods','bomb_plant', 'Top Demolitionist', 'bomb plantings', NULL, NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'O','dods','captured_loc','Area Captured','flags captured',NULL,NULL);


#
# Actions
#
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'dods', 'dod_control_point',6,1,'','Control Points Captured','1','0','1','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'dods', 'capblock', 6, 1, '', 'Capture Blocked', '1', '', '1', '');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'dods', 'captured_loc', 6, 1, '', 'Area Captured', '1', '', '1', '');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'dods', 'kill_planter', 2, 0, '', 'Bomb Planter Killed', '1', '', '', '');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'dods', 'bomb_defuse', 6, 1, '', 'Bomb Defused', '1', '', '1', '');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'dods', 'bomb_plant', 6, 1, '', 'Bomb Planted', '1', '', '1', '');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'dods', 'round_win', 0, 5, '', 'Round Win', '', '', '1', '');


#
# Teams
#
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'dods','Allies','Allies','0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'dods','Axis','Axis','0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'dods','Spectators','Spectators','0');


#
# Roles
#
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','Random','Random','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_allied_garand','Allied Rifleman','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_allied_thompson','Allied Assault','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_allied_heavy','Allied Support','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_allied_sniper','Allied Sniper','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_allied_mg','Allied Machine Gunner','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_allied_bazooka','Allied Rocketeer','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_axis_kar98','German Rifleman','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_axis_mp40','German Assault','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_axis_mp44','German Support','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_axis_sniper','German Sniper','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_axis_mg42','German Machine Gunner','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_axis_pschreck','German Rocketeer','0');


#
# Weapons
#
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dods', 'thompson', 'Thompson Submachine Gun', '1.25');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dods', 'mp40', 'MP40 Machine Pistol', '1.25');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dods', 'mp44', 'MP44 Assault Rifle', '1.35');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dods', 'mg42', 'MG42 Machine Gun', '1.20');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dods', 'k98', 'Karbiner 98', '1.50');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dods', 'bar', 'BAR Browning Automatic Rifle', '1.20');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dods', 'spring', 'Springfield Rifle with Scope', '1.50');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dods', 'k98_scoped', 'Karbiner 98 (Scoped)', '1.50');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dods', 'garand', 'M1 Garand Rifle', '1.30');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dods', '30cal', '.30 Caliber Machine Gun', '1.25');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dods', 'colt', 'Colt .45 model 1911', '1.60');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dods', 'frag_us', 'U.S. Grenade', '1.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dods', 'frag_ger', 'German Grenade', '1.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dods', 'm1carbine', 'M1 Carbine', '1.20');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dods', 'riflegren_ger', 'German Rifle Grenade', '2.50');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dods', 'riflegren_us', 'U.S. Rifle Grenade', '2.50');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dods', 'bazooka', 'Bazooka', '2.25');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dods', 'spade', 'Spade Entrenchment Tool', '3.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dods', 'amerknife', 'U.S. Issue Knife', '3.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dods', 'pschreck', 'Panzerschreck', '2.25');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dods', 'punch', 'Fist', '3.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dods', 'c96', 'c96 Pistol', '1.25');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dods', 'p38', 'p38 Pistol', '1.25');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dods', 'world', 'worldspawn', '0.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dods', 'smoke_ger', 'German Smoke Grenade', '5.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dods', 'smoke_us', 'U.S. Smoke Grenade', '5.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dods', 'amerknife', 'U.S. Issue Knife', '3.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dods', 'dod_bomb_target', 'TNT Bomb', '1.00');
