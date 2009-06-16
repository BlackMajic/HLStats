<?php
/**
 * $Id: binary_funcs.inc.php 501 2008-06-16 13:54:53Z jumpin_banana $
 * $HeadURL: https://hlstats.svn.sourceforge.net/svnroot/hlstats/tags/v1.40/web/hlstatsinc/binary_funcs.inc.php $
 *
 * Original development:
 * +
 * + HLstats - Real-time player and clan rankings and statistics for Half-Life
 * + http://sourceforge.net/projects/hlstats/
 * +
 * + Copyright (C) 2001  Simon Garner
 * +
 *
 * Additional development:
 * +
 * + UA HLstats Team
 * + http://www.unitedadmins.com
 * + 2004 - 2007
 * +
 *
 *
 * Current development:
 * +
 * + Johannes 'Banana' Keßler
 * + http://hlstats.sourceforge.net
 * + 2007 - 2008
 * +
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

   // Binary functions.

// Pass the functions the whole lot of data and the point at which
// the function is to begin working from

// TODO: These functions need signed versions creating ASAP

function GetInt8($data, &$datastart)
{
	$temp = '';
	$temp = ord($data{$datastart});
	$datastart += 1;

	return $temp;
}

function GetBoolean($data, &$datastart)
{
	$temp = '';
	$temp = GetInt8($data, $datastart);

	if($temp >= 1)
		return true;
	else
		return false;
}

function GetInt16($data, &$datastart)
{
	$temp = '';
	// @todo: possible bug ?
	//$temp = GetInt8($data, $datastart) + (GetInt8($data, $datastart) * 256);
	$temp = GetInt8($data, $datastart) + (GetInt8($data, $datastart));

	return $temp;
}

function GetInt32($data, &$datastart)
{
	$temp = '';
	$temp = GetInt8($data, $datastart) + (GetInt8($data, $datastart)<<8) + (GetInt8($data, $datastart)<<16) + (GetInt8($data, $datastart)<<24);

	return $temp;
}

// Null-Terminated String
function GetString($data, &$datastart)
{
	$temp = '';
	$counter = 0;
	while (ord($data[$datastart+$counter++]) != 0)
		$temp .= $data[$datastart+$counter-1];
	$datastart += strlen($temp) + 1;

	return $temp;
}

function GetFloat32($data, &$datastart)
{
	$decnumber = GetInt32($data, $datastart);
	$binnumber = base_convert($decnumber, 10, 2);
	while (strlen($binnumber) < 32)
		$binnumber = '0'.$binnumber;

	$exp = abs(base_convert(substr($binnumber, 1, 8), 2, 10)) - 127;

	if (substr($binnumber, 0, 1) == 1)
		$exp = 0 - $exp;
	$mantissa = 1;
	$mantadd = 0.5;

	for ($counter = 9; $counter < 32; $counter++)
	{
		if(substr($binnumber, $counter, 1) == 1)
			$mantissa += $mantadd;
		$mantadd = $mantadd / 2;
	}

	$temp = round(pow(2, $exp) * $mantissa);
	return $temp;
}
?>