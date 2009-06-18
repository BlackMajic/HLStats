#
# HLstats Game Support file for Natural Selection 3.0
# ----------------------------------------------------
#
# If you want to insert this manually and not via the installer
# replace ++DB_PREFIX++ with the current table prefix !


#
# Game Definition
#
INSERT IGNORE INTO ++DB_PREFIX++_Games VALUES ('ns','Natural Selection','0','0');


#
# Awards
#
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','ns','slash','Vicious Kitty','killings by le Swipe',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','ns','shotgun','Buckshot Masta','killings with the shotty',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','ns','pistol','Harold Handgun Alert','asskickings by pistola',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','ns','knife','Iron Chef Alert','vicious stabbings',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','ns','grenade','absolute n00b','pathetic killings by n00b grenades',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','ns','bitegun','Teething Tommy','killings with le jaw',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','ns','bite2gun','Mouth Full','killings with le big jaw',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','ns','leap','Tigger Alert','crushings by leap',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','ns','divinewind','Silent but Violent','slayings by recal relief',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','ns','sporegunprojectile','Left Feet Larry','killings with Lerk',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','ns','devour','Hungry Hungry Hippo','killings by Ingestion',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','ns','spitgunspit','Masta Fatty','Marines too dumb to kill a gorge',NULL,NULL);


#
# Actions
#
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built',1,0,'','Structures Built','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_destroyed',2,0,'','Structures Destroyed','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','research_start',1,0,'','Researches Performed','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','recycle',-3,0,'','Structures Recycled','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_alienresourcetower',1,0,'','Built Alien Resource Tower','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_destroyed_alienresourcetower',2,0,'','Destroyed Alien Resource Tower','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_phasegate',1,0,'','Built Phasegate','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_destroyed_phasegate',2,0,'','Destroyed Phasegate','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_resourcetower',1,0,'','Built Resource Tower','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_destroyed_resourcetower',2,0,'','Destroyed Resource Tower','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_siegeturret',1,0,'','Built Siege Turret','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_destroyed_siegeturret',2,0,'','Destroyed Siege Turret','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_team_advturretfactory',1,0,'','Built Advanced Turret Factory','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_destroyed_team_advturretfactory',2,0,'','Destroyed Advanced Turret Factory','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_team_armory',1,0,'','Built Armory','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_destroyed_team_armory',2,0,'','Destroyed Armory','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_team_turretfactory',1,0,'','Built Turret Factory','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_destroyed_team_turretfactory',2,0,'','Destroyed Turret Factory','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_turret',1,0,'','Built Turret','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_destroyed_turret',2,0,'','Destroyed Turret','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_team_infportal',1,0,'','Built INF Portal','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_destroyed_team_infportal',2,0,'','Destroyed INF Portal','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_defensechamber',1,0,'','Built Defense Chamber','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_destroyed_defensechamber',2,0,'','Destroyed Defense Chamber','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_offensechamber',1,0,'','Built Offense Chamber','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_destroyed_offensechamber',2,0,'','Destroyed Offense Chamber','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_movementchamber',1,0,'','Built Movement Chamber','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_destroyed_movementchamber',2,0,'','Destroyed Movement Chamber','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_team_hive',1,0,'','Built Alien Hive','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_destroyed_team_hive',2,0,'','Destroyed Alien Hive','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_team_armslab',1,0,'','Built Arms Lab','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_destroyed_team_armslab',2,0,'','Destroyed Arms Lab','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_sensorychamber',1,0,'','Built Sensory Chamber','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_destroyed_sensorychamber',2,0,'','Destroyed Sensory Chamber','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_team_prototypelab',1,0,'','Built Prototype Lab','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_destroyed_team_prototypelab',2,0,'','Destroyed Prototype Lab','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_team_command',1,0,'','Built Command Unit','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_destroyed_team_command',2,0,'','Destroyed Command Unit','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_team_observatory',1,0,'','Built Observatory','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_destroyed_team_observatory',1,0,'','Destroyed Observatory','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_team_advarmory',1,0,'','Built Advanced Armory','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_destroyed_team_advarmory',1,0,'','Destroyed Advanced Armory','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_scan',1,0,'','Built Scanner','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_destroyed_scan',1,0,'','Destroyed Scanner','1','0','0','0');


INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_weapon_grenadegun',1,0,'','Created a Grenade Gun','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_weapon_heavymachinegun',1,0,'','Created a Heavy Machine Gun','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_weapon_shotgun',1,0,'','Created a Shotgun','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_weapon_welder',1,0,'','Created a Welder','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_weapon_mine',1,0,'','Created a Mine','1','0','0','0');


INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_item_heavyarmour',1,0,'','Created Heavy Armour','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_item_catalyst',1,0,'','Created a Catalyst','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_item_genericammo',1,0,'','Created Generic Ammo','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_item_health',1,0,'','Created a Healthpack','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','structure_built_item_jetpack',1,0,'','Created a Jetpack','1','0','0','0');


INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'ns','research_cancel',-1,0,'','Stopped Researching ','1','0','0','0');



#
# Teams
#
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'ns','alien1team','Aliens','0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'ns','marine1team','Marines','0');


#
# Roles
#
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'ns','soldier','Soldier','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'ns','commander','Commander','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'ns','skulk','Skulk','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'ns','gorge','Gorge','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'ns','lerk','Lerk','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'ns','fade','Fade','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'ns','onos','Onos','0');
INSERT IGNORE INTO ++DB_PREFIX++_Roles VALUES (NULL,'ns','gestate','Gestate','1');


#
# Weapons
#
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ns','welder','Marine Welder','3.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ns','item_mine','Marine Mine','1.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ns','handgrenade','Marine Hand Grenade','1.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ns','grenade','Marine Grenade Launcher','1.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ns','knife','Marine Knife','4.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ns','pistol','Marine Pistol','2.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ns','machinegun','Marine Light Machine Gun','1.25');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ns','shotgun','Marine Shotgun','1.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ns','heavymachinegun','Marine Heavy Machine Gun','1.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ns','turret','Marine Turret','.75');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ns','siegeturret','Marine Siege Turret','1.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ns','resourcetower','Electrified Marine Resource Tower','2.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ns','team_turretfactor','Electric Marine Turret Factory','2.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ns','team_advturretfactor','Electrified Marine Advance Turret Factory','2.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ns','acidrocket','Fade Acid Rocket','1.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ns','bitegun','Skulk Bite','1.25');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ns','charge','Onos Charge','1.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ns','claws','Onos Gore','1.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ns','divinewind','Skulk Xenocide','1.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ns','leap','Skulk Leap','2.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ns','bite2gun','Lerk Bite','2.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ns','spitgunspit','Gorge Spit','2.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ns','sporegunprojectile','Lerk Spores','1.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ns','swipe','Fade Slash','1.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ns','healingspray','Gorge Health Spray','3.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ns','parasite','Skulk Parasite','3.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ns','devour','Onos Devour','2.00');
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'ns','offensechamber','Offense Chamber','1.00');
