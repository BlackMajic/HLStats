#
# HLStats Database Upgrade file
# -----------------------------
#
# To upgrade an existing HLStats 1.36 database to version 1.37, type:
#
#   mysql hlstats_db_name < upgrade_from_1.37.sql
#

# the new options
INSERT INTO hlstats_Options VALUES ('useFlash', '1');
INSERT INTO hlstats_Options VALUES ('allowSig', '0');
INSERT INTO hlstats_Options VALUES ('useGEOIP', '0');
INSERT INTO hlstats_Options VALUES ('allowXML', '0');
