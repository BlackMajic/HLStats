#
# HLstats Game Support file for Opposing Force CTF
#
# Thanks to Sean Cavanaugh from Gearbox Software.
#
# Note: you currently need to create two unique databases and two unique hlstats directories
# in apache in order to house Opposing Force CTF and Opposing Force DM seperatly correctly.
#
# If you want to insert this manually and not via the installer
# replace ++DB_PREFIX++ with the current table prefix !


#
# Game Definition
#
INSERT IGNORE INTO ++DB_PREFIX++_Games VALUES ('gearbox','Opposing Force CTF','0','0');


#
# Awards
#
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','gearbox','357','.357','kills with 357',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','gearbox','9mmAR','MP5','kills with 9mmAR',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','gearbox','9mmhandgun','Glock','kills with 9mmhandgun',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','gearbox','bolt','Explosive Crossbow Bolt','kills with explosive crossbow bolt',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','gearbox','crossbow','Sniper Crossbow Bolt','kills with sniper crossbow bolt',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','gearbox','crowbar','Crowbar Maniac','murders with crowbar',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','gearbox','displacer_ball','Displacer','kills with displacer',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','gearbox','eagle','Desert Eagle','kills with desert eagle',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','gearbox','gluon gun','Gluon Gun','kills with gluon cannon',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','gearbox','grapple','Barnacle','kills with barnacle',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','gearbox','grenade','Grenade','kills with grenade',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','gearbox','hornet','Hive Hand','kills with hive hand',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','gearbox','knife','Combat Knife','kills with combat knife',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','gearbox','m249','m249','kills with M249',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','gearbox','penguin','Penguin','kills with penguin',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','gearbox','pipewrench','Pipe Wrench','kills with pipe wrench',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','gearbox','rpg_rocket','Rocket Launcher','kills with rocket launcher',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','gearbox','satchel','Satchel','kills with satchel',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','gearbox','shock_beam','Shock Rifle','kills with shock rifle',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','gearbox','shockrifle','Shock Rifle Discharge','kills with shock rifle discharge',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','gearbox','shotgun','Redneck','kills with shotgun',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','gearbox','snark','Snark Master','kills with snark',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','gearbox','sniperrifle','Sniper','kills with sniper rifle',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','gearbox','spore','Spore Launcher','kills with spore launcher',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','gearbox','tau_cannon','Tau Cannon','kills with tau cannon',NULL,NULL);
INSERT IGNORE INTO ++DB_PREFIX++_Awards VALUES (NULL,'W','gearbox','tripmine','Tripmine','kills with tripmine',NULL,NULL);


#
# Actions
#

#
# Disabled tracking these in CTF because there are a LOT of ammo
# and health powerup events due to powerup passing in teamplay
#

# INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'gearbox','take_Ammo_Powerup',0,0,'','picked up ammo powerup','1','0','0','0');
# INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'gearbox','take_Damage_Powerup',0,0,'','picked up damage powerup','1','0','0','0');
# INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'gearbox','take_Health_Powerup',0,0,'','picked up health regen powerup','1','0','0','0');
# INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'gearbox','take_Jump_Powerup',0,0,'','picked up jump pack powerup','1','0','0','0');
# INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'gearbox','take_Shield_Powerup',0,0,'','picked up armor regen powerup','1','0','0','0');
# INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'gearbox','drop_Ammo_Powerup',0,0,'','dropped ammo powerup','0','0','0','0');
# INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'gearbox','drop_Damage_Powerup',0,0,'','dropped damage powerup','0','0','0','0');
# INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'gearbox','drop_Health_Powerup',0,0,'','dropped health regen powerup','0','0','0','0');
# INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'gearbox','drop_Jump_Powerup',0,0,'','dropped jump powerup','0','0','0','0');
# INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'gearbox','drop_Shield_Powerup',0,0,'','dropped armor regen powerup','0','0','0','0');

# Team Actions
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'gearbox','TookFlag',50,0,'','took the enemy flag','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'gearbox','FlagDefense',40,0,'','defended the flag','0','1','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'gearbox','ReturnedFlag',10,0,'','returned the flag','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'gearbox','CapturedFlag',100,1,'','captured the flag','1','0','0','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'gearbox','ReturnedBlackMesaFlag',0,0,'Black Mesa','Black Mesa flag returned','0','0','0','1');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'gearbox','ReturnedOpposingForceFlag',0,0,'Opposing Force','Opposing Force flag returned','0','0','0','1');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'gearbox','Victory',0,0,'','Team Win','0','0','1','0');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'gearbox','Draw',0,0,'','Draw Game','0','0','0','1');

INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'gearbox','op4cp_park_tc1_opscore',0,1,'','Opposing Force controls Command Point 1','0','0','0','1');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'gearbox','op4cp_park_tc2_opscore',0,1,'','Opposing Force controls Command Point 2','0','0','0','1');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'gearbox','op4cp_park_tc3_opscore',0,1,'','Opposing Force controls Command Point 3','0','0','0','1');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'gearbox','op4cp_park_tc4_opscore',0,1,'','Opposing Force controls Command Point 4','0','0','0','1');

INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'gearbox','op4cp_park_tc1_bmscore',0,1,'','Black Mesa controls Command Point 1','0','0','0','1');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'gearbox','op4cp_park_tc2_bmscore',0,1,'','Black Mesa controls Command Point 2','0','0','0','1');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'gearbox','op4cp_park_tc3_bmscore',0,1,'','Black Mesa controls Command Point 3','0','0','0','1');
INSERT IGNORE INTO ++DB_PREFIX++_Actions VALUES (NULL,'gearbox','op4cp_park_tc4_bmscore',0,1,'','Black Mesa controls Command Point 4','0','0','0','1');

#
# Teams
#

INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'gearbox','Black Mesa','Black Mesa', '0');
INSERT IGNORE INTO ++DB_PREFIX++_Teams VALUES (NULL,'gearbox','Opposing Force','Opposing Force', '0');


#
# Weapons
#
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'gearbox','357','357 Revolver',2.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'gearbox','9mmAR','9mm SMG',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'gearbox','9mmhandgun','9mm Handgun',2.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'gearbox','bolt','Crossbow (Explosive)',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'gearbox','crossbow','Crossbow (Sniper)',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'gearbox','crowbar','Crowbar',2.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'gearbox','displacer_ball','Displacer',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'gearbox','eagle','Desert Eagle',2.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'gearbox','gluon gun','Gluon Gun',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'gearbox','grapple','Barnacle',2.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'gearbox','grenade','Grenade',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'gearbox','hornet','Hive Hand',2.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'gearbox','knife','Combat Knife',2.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'gearbox','m249','M249',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'gearbox','penguin','Penguin',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'gearbox','pipewrench','Pipe Wrench',2.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'gearbox','rpg_rocket','Rocket Propelled Grenade',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'gearbox','satchel','Satchel Charge',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'gearbox','shock_beam','Shock Rifle',2.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'gearbox','shockrifle','Shock Rifle Discharge',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'gearbox','shotgun','Shotgun',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'gearbox','snark','Snark',2.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'gearbox','sniperrifle','Sniper Rifle',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'gearbox','spore','Spore Launcher',2.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'gearbox','tau_cannon','Tau Cannon',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'gearbox','tripmine','Tripmine',1.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'gearbox','mortar_shell','Mounted Mortar', 5.00);
INSERT IGNORE INTO ++DB_PREFIX++_Weapons VALUES (NULL,'gearbox','tank','Mounted Turret', 5.00);