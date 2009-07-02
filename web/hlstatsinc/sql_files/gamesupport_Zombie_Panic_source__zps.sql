#
# HLstats Game Support file for zps
# ----------------------------------------------------
#
# If you want to insert this manually and not via the installer
# replace ++DB_PREFIX++ with the current table prefix !

#
# Game Definition
#
INSERT IGNORE INTO ++DB_PREFIX++_Games VALUES ('zps','Zombie Panic Source','1','0');


#
# Awards
#
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'O','zps','kill_streak_12','kill,kill,kill','12 kills in a row',NULL,NULL);

#
# Player Actions
#
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'zps','kill_streak_2',1,0,'','Double Kill','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'zps','kill_streak_3',2,0,'','Triple Kill','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'zps','kill_streak_4',3,0,'','Domination','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'zps','kill_streak_5',4,0,'','Rampage','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'zps','kill_streak_6',5,0,'','Mega Kill','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'zps','kill_streak_7',6,0,'','Ownage','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'zps','kill_streak_8',7,0,'','Ultra Kill','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'zps','kill_streak_9',8,0,'','Killing Spree','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'zps','kill_streak_10',9,0,'','Monster Kill','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'zps','kill_streak_11',10,0,'','Unstoppable','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'zps','kill_streak_12',11,0,'','God Like','1','0','0','0');

#
# Weapons
#
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','arms','ARMS',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','world','World',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','tireiron','Tire Iron',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','spanner','Wrench',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','sledgehammer','Sledgehammer',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','shovel','Shovel',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','racket','Tennis Racket',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','pot','Pot',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','plank','Wooden Plank',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','physics','Physics',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','machete','Machete',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','keyboard','Keyboard',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','hammer','Hammer',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','golf','Golf Club',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','fryingpan','Frying Pan',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','crowbar','Crowbar',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','chair','Chair',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','broom','Broomstrick',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','axe','Axe',1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','usp','Heckler & Koch USP',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','supershorty','Mossberg Super Shorty',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','ppk','Walther PPK',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','mp5','Heckler & Koch MP5',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','grenade_frag','Grenade',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','glock18c','Glock 18c',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','glock','Glock 17',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','carrierarms','Carrier',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','arms','Zombie',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','ak47','AK-47',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','870','Remington 870',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','revolver','Revolver',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','torque','Torque',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'zps','winchester','Winchester Double Barreled Shotgun',1.00);
