package HLstats_Server;
#
# $Id: HLstats_Server.pm 435 2008-04-09 12:19:03Z jumpin_banana $
# $HeadURL: https://hlstats.svn.sourceforge.net/svnroot/hlstats/tags/v1.40/daemon/HLstats_Server.pm $
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
#


sub new
{
	my ($class_name, $serverId, $address, $port, $game) = @_;

	my ($self) = {};

	bless($self, $class_name);

	$self->{id}      = $serverId;
	$self->{address} = $address;
	$self->{port}    = $port;
	$self->{game}    = $game;

	$self->{map}     = "";
	$self->{numplayers} = 0;

	return $self;
}

1;