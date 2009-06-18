#!/bin/bash
#
# $Id: hlstats.sh 638 2008-11-26 15:18:35Z jumpin_banana $
# $HeadURL: https://hlstats.svn.sourceforge.net/svnroot/hlstats/trunk/hlstats/tools/hlstats.sh $
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

# get saved dirname; will return something with /tool at the end
saveDir=`dirname $(readlink -f ${0})`;

cd $saveDir;

case "$1" in
 start)
     echo "Starting HLstats...";
     if [ -f hlstats.pid ]; then
        kill -0 `cat hlstats.pid` >/dev/null 2>&1
        if [ "$?" == "0" ]; then
            echo "HLstats already running!"
        else
            rm -rf hlstats.pid
            perl ../daemon/hlstats.pl >/dev/null 2>&1 &
            echo $! >hlstats.pid
            echo "PID file created."
            echo "HLstats Started successfully!"
        fi
     else
        perl ../daemon/hlstats.pl >/dev/null 2>&1 &
        echo $! >hlstats.pid
        echo "PID file created."
        echo "HLstats Started successfully!"
     fi
 ;;
 stop)
     echo "Stopping HLstats..."
     kill -9 `cat hlstats.pid` >/dev/null 2>&1
     if [ "$?" == "0" ]; then
        rm -rf hlstats.pid
        echo "HLstats Stopped successfully."
     else
        echo "HLstats is not running!"
     fi
 ;;
 restart)
     echo "Restarting HLstats..."
     kill -9 `cat hlstats.pid` >/dev/null 2>&1
     if [ "$?" == "0" ]; then
         rm -rf hlstats.pid
         perl ../daemon/hlstats.pl >/dev/null 2>&1 &
         echo $! >hlstats.pid
         echo "PID file created."
         echo "HLstats Restarted successfully!"
     else
         echo "HLstats is not running!"
         if [ -f hlstats.pid ]; then
           rm -rf hlstats.pid
         fi
         perl ../daemon/hlstats.pl >/dev/null 2>&1 &
         echo $! >hlstats.pid
         echo "PID file created."
         echo "HLstats Started successfully!"
     fi
 ;;
 *)
     echo "Usage: ./`basename $0` [ start | stop | restart ]"
 ;;
esac

exit 0
