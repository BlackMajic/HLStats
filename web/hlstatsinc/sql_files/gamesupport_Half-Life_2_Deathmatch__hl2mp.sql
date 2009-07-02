#
# HLstats Game Support file for Half-Life 2 [Team] Deathmatch
#
# If you want to insert this manually and not via the installer
# replace ++DB_PREFIX++ with the current table prefix !


#
# Game Definition
#
INSERT IGNORE INTO ++DB_PREFIX++_Games VALUES ('hl2mp','Half-Life 2 Deathmatch','1','0');

#
# Awards
#
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','hl2mp','crowbar','Crowbar','bludgeonings with ol\' red',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','hl2mp','physics','Anger Issues','kills with physics',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','hl2mp','physbox','Telefrags','telefrags',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','hl2mp','stunstick','Stun Stick','kills with stunstick',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','hl2mp','pistol','Pistol','kills with pistol',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','hl2mp','shotgun','Boomstick!','kills with shotgun',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','hl2mp','375','Clint','kills with .375',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','hl2mp','smg1','SMG','kills with the smg',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','hl2mp','ar2','Assault','kills with the assault rifle',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','hl2mp','crossbow_bolt','Sniper','snipings with crossbow',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','hl2mp','slam','SLAMMED!','kills with the slam',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','hl2mp','grenade_frag','Grenade Fiend','kills with \'nades',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','hl2mp','rpg_missile','Role Player','kills with RPG',NULL,NULL);

#
# Teams
#
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'hl2mp','No Team','Unassigned','0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'hl2mp','The Combine','Combine', '0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'hl2mp','Rebel Forces','Rebels', '0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'hl2mp','Spectator','Spectator','0');

#
# Weapons
#
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2mp','375','.375 Magnum',1.10);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2mp','ar2','Combine Assault Rifle',1.10);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2mp','combine_ball','Combine Ball',1.20);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2mp','crossbow_bolt','Crossbow',1.70);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2mp','crowbar','Crowbar',2.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2mp','env_explosion','Explosion',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2mp','grenade_frag','Frag Grenade',1.40);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2mp','physics','Physics',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2mp','pistol','USP Match',2.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2mp','rpg_missile','Rocket Propelled Grenade',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2mp','shotgun','Shotgun',1.30);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2mp','slam','S.L.A.M.',1.30);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2mp','smg1','Sub Machine Gun',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2mp','smg1_grenade','Impact Grenade',1.20);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2mp','stunstick','Stun Stick',2.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2mp','physbox','Physics Box',2.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'hl2mp','player','Player',3.00);