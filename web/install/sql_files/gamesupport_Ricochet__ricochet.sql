#
# HLstats Game Support file for Ricochet
# --------------------------------------
#
# If you want to insert this manually and not via the installer
# replace ++DB_PREFIX++ with the current table prefix !

#
# Game Definition
#
INSERT IGNORE INTO ++DB_PREFIX++_Games VALUES ('ricochet','Ricochet','0','0');


#
# Awards
#
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','ricochet','decapitate','Decapitator','decapitiations',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','ricochet','3bounce','Bouncer','triple bounce kills',NULL,NULL);

#
# Player Actions
#
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ricochet','falling',-1,0,'','Fell','1','0','0','0');

#
# Weapons
#
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ricochet','0bounce','Disc, 0 Bounces', 1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ricochet','1bounce','Disc, 1 Bounce', 1.50);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ricochet','2bounce','Disc, 2 Bounces', 2.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ricochet','3bounce','Disc, 3 Bounces', 3.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ricochet','decapitate','Decapitator Disc',1.30);
