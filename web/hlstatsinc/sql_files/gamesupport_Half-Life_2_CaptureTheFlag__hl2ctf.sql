#
# HLstats Game Support file for Half-Life 2 Capture the Flag
#
# If you want to insert this manually and not via the installer
# replace ++DB_PREFIX++ with the current table prefix !

#
# Game Definition
#
INSERT IGNORE INTO ++DB_PREFIX++_Games VALUES ('hl2ctf','Half-Life 2 Capture the flag','1','0');

#
# Awards
#
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','hl2ctf','crowbar','Crowbar','bludgeonings with ol\' red',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','hl2ctf','physcannon','Anger Issues','kills with physics',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','hl2ctf','stunstick','Stun Stick','kills with stunstick',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','hl2ctf','pistol','Pistol','kills with pistol',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','hl2ctf','shotgun','A freakin\' 12 gague!','kills with shotgun',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','hl2ctf','357','Clint','kills with .357',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','hl2ctf','smg1','SMG','kills with the smg',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','hl2ctf','ar2','Assault','kills with the assault rifle',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','hl2ctf','crossbow_bolt','Sniper','snipings with crossbow',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','hl2ctf','slam','SLAMMED!','kills with the slam',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','hl2ctf','grenade_frag','Grenade Fiend','kills with \'nades',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','hl2ctf','rpg_missile','Role Player','kills with RPG',NULL,NULL);

#
# Actions
#
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'hl2ctf', 'ctf_flag_capture', 15, 0, '', 'Captured Enemy Flag', '1', '', '', '');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'hl2ctf', 'ctf_flag_defend', 2, 0, '', 'Defended the Flag', '1', '', '', '');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'hl2ctf', 'ctf_kill_carrier', 5, 0, '', 'Killed Enemy Flag Carrier', '1', '', '', '');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'hl2ctf', 'ctf_flag_return', 5, 0, '', 'Returned Flag', '1', '', '', '');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'hl2ctf', 'ctf_flag_stolen', 1, 0, '', 'Stole Enemy Flag', '1', '', '', '');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'hl2ctf', 'ctf_protect_carrier', 5, 0, '', 'Protected Flag Carrier', '1', '', '', '');


#
# Teams
#
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'hl2ctf','Combine','Combine', '0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'hl2ctf','Rebels','Rebels', '0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'hl2ctf','Spectator','Spectator','0');

#
# Weapons
#

INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2ctf','stunstick', 'Stun Stick', 2.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2ctf','Crowbar', 'Crowbar', 2.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2ctf','crossbow_bolt', 'Crossbow', 1.75);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2ctf','Slam', 'S.L.A.M', 1.80);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2ctf','Pistol', 'USP Match', 1.75);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2ctf','grenade_frag', 'Grenade', 1.75);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2ctf','combine_ball', 'Combine Ball', 1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2ctf','physcannon', 'Gravity Gun', 1.15);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2ctf','Shotgun', 'Shotgun', 1.10);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2ctf','357', '.357 Magnum', 1.10);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2ctf','rpg_missile', 'Rocket Propelled Grenade', 1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2ctf','ar2', 'Combine Assault Rifle', 1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2ctf','smg1', 'Sub Machine Gun', 1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2ctf','env_explosion', 'Explosion', 1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2ctf','ctf_combine_turret', 'Combine Turret', 1.25);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2ctf','ctf_rebel_turret', 'Rebel Turret', 1.25);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2ctf','ctf_oicw', 'OICW Rifle', 1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2ctf','ctf_sniper', 'Sniper Rifle', 1.10);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2ctf','grenade_ctf_oicw_airburst', 'OICW Grenade', 1.20);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2ctf','ctf_alyxgun', 'Alyx Gun', 1.15);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2ctf','smg1_grenade', 'SMG Grenade', 1.10);