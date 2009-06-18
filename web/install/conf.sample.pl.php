##
## HLstats Configuration File
##
#
# This file is used by hlstats.pl, hlstats-awards.pl and hlstats-resolve.pl.
# Note that many options can be overridden on the command line; try running with
# the --help switch for details of available command line options.
#


##
## Database Settings
##

# DBName - Name of the database to use.
DBName "#DB_NAME#"

# DBUsername - User to connect to the database as.
DBUsername "#DB_USER#"

# DBPassword - Password for the database user.
DBPassword "#DB_PASS#"

# DBHost - Database server "address" or "address:port". Address can be an IP or
#          a hostname. The default MySQL port is 3306 (tcp).
DBHost "#DB_HOST#"

# DBPrefix - Table prefix. Default is hlstats
DBPrefix "#DB_PREFIX#"

# DBLowPriority - Use INSERT DELAYED and DELETE LOW_PRIORITY for some queries.
#                 This can give better performance, but may make statistics less
#                 "real time". 1=on 0=off
DBLowPriority 0


##
## UDP Socket Settings (should match "logaddress ip port" on the game servers)
##

# BindIP - IP address to bind to (leave empty to use all interfaces).
BindIP "#HLS_IP#"

# Port - Port to listen on for log data from the game servers.
Port #HLS_PORT#


##
## DNS Settings
##

# DNSResolveIP - Resolve player IP addresses to hostnames. Requires a working
#                DNS setup (on the box running hlstats.pl). 1=on 0=off
DNSResolveIP 1

# DNSTimeout - Time in seconds to wait for DNS queries to complete before
#              cancelling. You may need to increase this if on a slow connection
#              or if you find a lot of IPs are not being resolved. However,
#              while hlstats.pl is waiting for an IP to resolve it cannot be
#              parsing log data.
DNSTimeout 5


##
## Rcon Settings
##

# Rcon - Allow HLstats to send Rcon commands to the game servers. 1=on 0=off
Rcon 1

# RconIgnoreSelf - Ignore (do not log) Rcon commands originating from the same
#                  IP as the server being Rcon'd. (Useful if you run any kind of
#                  monitoring script which polls the server regularly by Rcon.)
#                  1=on 0=off
RconIgnoreSelf 0

# RconRecord - Sets whether to record Rcon commands to the Admin event table.
#              This can be useful to see what your admins are doing. But if you
#              run programs like PB it can also fill your database up with a lot
#              of useless junk.
#              1=on 0=off
RconRecord 1

# RconSay - How the Rcon say command would be returned
#			Values are
#				"say" => ordinary say command (default)
#				"admin_psay" => return a private say adminMod
#				"amx_psay" => return a private say with amxMod
#				"sm_psay" => return a private say with sourceMod
RconSay "say"

##
## General Settings
##

# MailTo - E-mail address to mail database errors to. (See also MailPath.)
MailTo "#ADMIN_MAIL#"

# MailPath - Path to the 'mail' program -- usually /bin/mail
MailPath "/bin/mail"

# Mode - EXPERIMENTAL: Sets the player-tracking mode.
#        Possible values:
#             Normal     - Recommended for public Internet server use.
#                          Players will be tracked by WON ID.
#             NameTrack  - Useful for shared-PC environments, such as
#                          Internet cafes, etc. Players will be tracked
#                          by nickname. EXPERIMENTAL!
#             LAN        - Useful for LAN servers where players do not
#                          have a real WON ID. Players will be tracked
#                          by IP Address. EXPERIMENTAL!
#         ++ Note: Make sure you change the setting of MODE at the top of
#            hlstats.php, as well.
Mode #MODE#

# MinPlayers - Specifies the minimum number of players required in the server
#              for most player events (objectives, frags, etc.) to be recorded.
#              This prevents players from hopping on an empty server and
#              boosting their skill rating by capturing the flag, etc., with no
#              opposition. (Default is 2 players required on the server.)
MinPlayers 2

# SkillMaxChange - Specifies the maximum number of skill points a player can
#                  gain at one time through frags. Because players with low
#                  skill ratings gain more for killing players with high skill
#                  ratings, this cap is needed to prevent e.g. a player with a
#                  skill rating of '100' killing a player with a skill rating of
#                  '2000' and therefore gaining several hundred or thousand
#                  skill points. Instead they cannot gain more than the value
#                  specified here. (Default is 100 skill points maximum change.)
SkillMaxChange 100

# DeleteDays - HLstats will automatically delete history events from the
#              events tables when they are over this many days old.
#              This is important for performance reasons. Set lower if you
#              are logging a large number of game servers or find the load on
#              the MySQL server is too high. Don't forget to also change the
#              DELETEDAYS define at the top of hlstats.php.
#               ++ Note: Make sure you change the setting of DELETEDAYS at the
#                  top of hlstatsinc/hlstats.conf.php, as well.
#				A value of 0 means no delete days
DeleteDays #DELETE_DAYS#

# UseTimestamp - Set to 0 (the default) to use the current time on the database
#                server for the timestamp when recording events. Set to 1 to use
#                the timestamp provided on the log data. Unless you are
#                processing old log files on STDIN, you probably want to set
#                this to 0; otherwise you will need to ensure that the clocks on
#                your game servers are accurate. 1=on 0=off
UseTimestamp 0

# DebugLevel - Set this to 1 to have debugging information printed on stdout.
#              Set higher for even more debugging information. Set to 0 for
#              quiet operation. It is recommended that you set this to 1 when
#              first configuring HLstats, to help diagnose any problems.
DebugLevel 0

# PatchLoopbackIP - Set to 1 to replace player IP 127.0.0.1 (aka loopback) with
#                   remote host IP. This works for LAN mode only.
PatchLoopbackIP 0

# LogChat - Log all your chat massages,.
#			This is conected with the DeleteDays setting. So the chat messages
#			will be only hold in the DB for the DeleteDays value.
LogChat 0

# IngamePoints - Show ingame stat report if you kill / or be killed
# 				The response is done via rcon and the option RconSay
# 				from above is used
# 				default 0=off 1=on
IngamePoints 0

# IgnoreBots - Completly ignore anything which has BOT
# 				default 0=off 1=on
IgnoreBots 0

# the Elo rating system
# developed by HampusW
# 			here you can decide if you want to use this system
#			IMPORTANT: Must match the value of EloRating in hlstats.conf !!
#			Possible values are
#			1) "0"		- Off. Do not use the ratins system at all.
#			2) "1"		- Use the system and display it with the ordinary system
#			3) "2"		- Only use the EloRating and show only the new one.
EloRating "0"