#
# HLstats Game Support file for Vampire Slayer
# ----------------------------------------------------
#
# If you want to insert this manually and not via the installer
# replace ++DB_PREFIX++ with the current table prefix !


#
# Game Definition
#
INSERT IGNORE INTO ++DB_PREFIX++_Games VALUES ('vs','Vampire Slayer','0','0');


#
# Awards
#
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'O','vs','VAMPIRE_CROSS_CAPTURE','Sneaky Slayer','vampire crosses captured',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'O','vs','SLAYER_CROSS_CAPTURE','Vampire Theif','slayer crosses captured',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'O','vs','VAMPIRE_CROSS_RETURNED','Defensive Vampire?','vampire crosses returned',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'O','vs','SLAYER_CROSS_RETURNED','Slayer Defender','slayer crosses returned',NULL,NULL);


#
# Player Actions
#
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'vs','knockout',1,0,'SLAYER','Knocked out','0','1','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'vs','resurrection',0,0,'VAMPIRE','Resurected','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'vs','VAMPIRE_CROSS_STOLEN',5,1,'SLAYER','Stole Vampire Cross','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'vs','VAMPIRE_CROSS_DROPPED',-5,-1,'SLAYER','Dropped Vampire Cross','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'vs','VAMPIRE_CROSS_CAPTURE',75,15,'SLAYER','Captured Vampire Cross','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'vs','SLAYER_CROSS_STOLEN',5,1,'VAMPIRE','Stole Slayer Cross','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'vs','SLAYER_CROSS_DROPPED',-5,-1,'VAMPIRE','Dropped Slayer Cross','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'vs','SLAYER_CROSS_CAPTURE',75,15,'VAMPIRE','Captured Slayer Cross','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'vs','SLAYER_CROSS_RETURNED',10,2,'SLAYER','Returned Slayer Cross','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'vs','VAMPIRE_CROSS_RETURNED',10,2,'VAMPIRE','Returned Vampire Cross','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'vs','SLAYER_WIN',0,10,'SLAYER','Slayer Win','0','0','1','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'vs','VAMPIRE_WIN',0,10,'VAMPIRE','Vampire Win','0','0','1','0');

#
# Teams
#
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'vs','SLAYER','Slayers','0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'vs','VAMPIRE','Vampires','0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'vs','SPECTATOR','Spectator','0');


#
# Roles
#
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'vs','EIGHTBALL','Eightball','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'vs','NINA','Nina','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'vs','EDGAR','Edgar','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'vs','FATHERD','Father D.','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'vs','MOLLY','Molly','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'vs','LOUIS','Louis','0');


#
# Weapons
#
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'vs','claw','Vampire Claw',2.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'vs','cue','Pool Cue',1.30);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'vs','stake','Wooden Stake',1.30);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'vs','colt','Colt .45',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'vs','crossbow','Crossbow',1.00);
