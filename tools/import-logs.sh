#!/bin/bash
#
# $Id: import-logs.sh 436 2008-04-09 12:20:04Z jumpin_banana $
# $HeadURL: https://hlstats.svn.sourceforge.net/svnroot/hlstats/tags/v1.40/tools/import-logs.sh $
#
# Original development:
# +
# + HLstats - Real-time player and clan rankings and statistics for Half-Life
# + http://sourceforge.net/projects/hlstats/
# +
# + Copyright (C) 2001  Simon Garner
# +
#
# Additional development:
# +
# + UA HLstats Team
# + http://www.unitedadmins.com
# + 2004 - 2007
# +
#
#
# Current development:
# +
# + Johannes 'Banana' Keßler
# + http://hlstats.sourceforge.net
# + 2007 - 2008
# +
#
# HLstats - Real-time player and clan rankings and statistics for Half-Life
# http://sourceforge.net/projects/hlstats/
#
# Copyright (C) 2001  Simon Garner
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
#

NO_ARGS=0
E_OPTERROR=65

if [ $# -eq "$NO_ARGS" ]; then
    echo "Usage: `basename $0` -i server-ip -p server-port [-mtdrn] log_dir"
    echo "    -i serverip    IP address of the server you are importing logs from"
    echo "    -p serverport  PORT of the server your are importing logs from"
    echo "    -m mode        Player tracking mode (Normal, NameTrack, LAN)"
    echo "    -t             Use Timestamp from logs (default)"
    echo "    -d             Use Timestamp from database"
    echo "    -r             Enable Rcon exec support (default)"
    echo "    -n             Disable Rcon exec support"
    echo "    -h             Prints this"
else
    echo "Parsing Logs..."

    while getopts ":m:i:p:tdrn" Option
    do
    case $Option in
        m) m="--mode $OPTARG" ;;
        i) i="--server-ip $OPTARG" ;;
        p) p="--server-port $OPTARG" ;;
        t) t="--timestamp" ;;
        d) d="--notimestamp" ;;
        r) r="--rcon" ;;
        n) n="--norcon" ;;
        h) echo "Usage: `basename $0` -i server-ip -p server-port [-mtdrn] log_dir"
           echo "    -i serverip    IP address of the server you are importing logs from"
           echo "    -p serverport  PORT of the server your are importing logs from"
           echo "    -m mode        Player tracking mode (Normal, NameTrack, LAN)"
           echo "    -t             Use Timestamp from logs (default)"
           echo "    -d             Use Timestamp from database"
           echo "    -r             Enable Rcon exec support (default)"
           echo "    -n             Disable Rcon exec support"
           echo "    -h             Prints this"
          ;;
        esac
    done
    shift $(($OPTIND - 1))

    cd ..
    cat $1/*.log | ./hlstats.pl --stdin $m $i $p $t $d $r $n >/dev/null 2>&1

    echo "Parse Complete."
fi

exit 0
