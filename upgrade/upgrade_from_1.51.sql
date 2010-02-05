#
# HLStats Database Upgrade file
# -----------------------------
#
# REPLACE #DB_PREFIX# WITH YOUR CURRENT HLSTATS PREFIX eg. hlstats
#
# To upgrade an existing HLStats 1.51 database to version 1.60, type:
#
#   mysql hlstats_db_name < upgrade_from_1.51.sql
#
#

UPDATE #DB_PREFIX#_Options SET `keyname` = 'showChart' WHERE `keyname` = 'useFlash';
