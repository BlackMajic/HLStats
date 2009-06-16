#
# HLstats Game Support file for Day Of Defeat 1.3
# ----------------------------------------------------
#
# If you want to insert this manually and not via the installer
# replace ++DB_PREFIX++ with the current table prefix !


#
# Game Definition
#
INSERT IGNORE INTO ++DB_PREFIX++_Games VALUES ('dod','Day Of Defeat','0','0');


#
# Awards
#
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dod','amerknife','Backstabbing Beotch','kills with the American Knife',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dod','luger','Luger Freak','kills with the Luger 08 Pistol',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dod','kar','KarMeister','kills with the Mauser Kar 98k',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dod','mp40','MP40 Hor','kills with the MP40 Machine Pistol',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dod','spade','Shovel God','kills with the spade',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dod','mp44','MP44 Hor','kills with the MP44 Assault Rifle',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dod','colt','Colt Freak','kills with the Colt .45 model 1911',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dod','garand','GarandMeister','kills with the M1 Garand Rifle',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dod','thompson','Thompson Hor','kills with the Thompson Submachine Gun',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dod','spring','Spring Sniper','snipings with the Springfield 03 Rifle',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dod','bar','Bar Browning Hor','kills with the BAR Browning Automatic Rifle',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dod','grenade','McVeigh Alert','bombings with the Grenade',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dod','garandbutt','Headsmasher','kills with Garand Butt Stock',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dod','bazooka','Bazooka Joe','kills with the Bazooka',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','dod','pschreck','Panzerschreck Hans','kills with the Panzerschreck',NULL,NULL);


#
# Actions
#
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'dod','dod_control_point',6,1,'','Control Points Captured','1','0','1','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'dod','dod_capture_area',6,1,'','Areas Captured','1','0','1','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'dod','dod_object_goal',4,0,'','Objectives Achieved','1','0','0','0');


#
# Teams
#
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'dod','Allies','Allies','0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'dod','Axis','Axis','0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'dod','Spectators','Spectators','0');


#
# Roles
#
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','Random','Random','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_allied_garand','American Rifleman','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_allied_carbine','American Staff Sergeant','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_allied_thompson','American Master Sergeant','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_allied_grease','American Sergeant','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_allied_sniper','American Sniper','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_allied_heavy','American Support Infantry','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_allied_mg','American Machine Gunner','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_alliedpara_garand','American Para Rifleman','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_alliedpara_carbine','American Para Staff Sergeant','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_alliedpara_thompson','American Para Master Sergeant','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_alliedpara_grease','American Para Sergeant','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_alliedpara_spring','American Para Sniper','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_alliedpara_bar','American Para Support Infantry','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_alliedpara_30cal','American Para Machine Gunner','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_brit_light','British Rifleman','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_brit_medium','British Sergeant Major','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_brit_sniper','British Marksman','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_brit_heavy','British Gunner','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_axis_kar98','Axis Grenadier','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_axis_k43','Axis Stosstruppe','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_axis_mp40','Axis Unteroffizier','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_axis_mp44','Axis Sturmtruppe','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_axis_sniper','Axis Scharfsch�tze','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_axis_mg34','Axis MG34-Sch�tze','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_axis_mg42','Axis MG42-Sch�tze','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_axispara_kar98','Axis Para Grenadier','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_axispara_k43','Axis Para Stosstruppe','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_axispara_mp40','Axis Para Unteroffizier','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_axispara_mp44','Axis Para Sturmtruppe','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_axispara_scopedkar','Axis Para Scharfsch�tze','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_axispara_fg42bipod','Axis Para Fg42-Zweinbein','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_axispara_fg42scope','Axis Para Fg42-Zielfernrohr','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_axispara_mg34','Axis Para MG34-Sch�tze','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'dod','#class_axispara_mg42','Axis Para MG42-Sch�tze','0');


#
# Weapons
#
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'k43', 'Karbiner 43', '1.50');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'luger', 'Luger 08 Pistol', '1.50');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'kar', 'Mauser Kar 98k', '1.30');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'mp40', 'MP40 Machine Pistol', '1.25');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'scopedkar', 'Mauser Karbiner 98k Sniper Rifle', '1.50');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'mp44', 'MP44 Assault Rifle', '1.35');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'colt', 'Colt .45 model 1911', '1.60');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'garand', 'M1 Garand Rifle', '1.30');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'thompson', 'Thompson Submachine Gun', '1.25');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'spring', 'Springfield Rifle with Scope', '1.50');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'bar', 'BAR Browning Automatic Rifle', '1.20');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'grenade', 'U.S. Grenade', '1.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'enf_bayonet', 'Enfield Bayonet', '2.50');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'bren', 'Bren Machine Gun', '1.25');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'm1carbine', 'M1 Carbine', '1.20');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'greasegun', 'Greasegun', '1.30');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', '30cal', '.30 Caliber Machine Gun', '1.25');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'mg42', 'MG42 Machine Gun', '1.20');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'grenade2', 'German Grenade', '1.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'spade', 'Spade Entrenchment Tool', '3.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'gerknife', 'German Knife', '3.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'fg42', 'FG42 Paratroop Rifle', '1.25');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'world', 'worldspawn', '0.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'amerknife', 'U.S. Issue Knife', '3.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'bayonet', 'Karbiner Bayonet', '2.40');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'mg34', 'MG34 Machine Gun', '1.20');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'brit_knife', 'British Knife', '3.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'mortar', 'Mortar', '1.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'fcarbine', 'F1 Carbine', '1.35');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'scoped_fg42', 'Scoped FG42', '1.30');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'bazooka', 'Bazooka', '2.25');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'enfield', 'Enfield Rifle', '1.35');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'garandbutt', 'Butt Stock Hit', '3.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'mills_bomb', 'British Grenade', '1.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'piat', 'Piat', '2.25');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'pschreck', 'Panzerschreck', '2.25');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'scoped_enfield', 'Scoped Enfield', '1.50');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'sten', 'Sten Submachine Gun', '1.25');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL, 'dod', 'webley', 'Webley Revolver', '1.60');