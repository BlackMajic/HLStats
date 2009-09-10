ALTER TABLE hlstats_Events_Frags ADD INDEX ( victimId );
ALTER TABLE hlstats_Events_Frags ADD INDEX ( killerId );
ALTER TABLE hlstats_Events_Frags ADD INDEX ( weapon );
ALTER TABLE hlstats_Weapons ADD INDEX ( code );
