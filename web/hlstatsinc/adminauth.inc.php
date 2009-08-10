<?php
/**
 *
 * Original development:
 * +
 * + HLStats - Real-time player and clan rankings and statistics for Half-Life
 * + http://sourceforge.net/projects/hlstats/
 * +
 * + Copyright (C) 2001  Simon Garner
 * +
 *
 * Additional development:
 * +
 * + UA HLStats Team
 * + http://www.unitedadmins.com
 * + 2004 - 2007
 * +
 *
 *
 * Current development:
 * +
 * + Johannes 'Banana' Keßler
 * + http://hlstats.sourceforge.net
 * + 2007 - 2009
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

	pageHeader(array(l("Admin")), array(l("Admin")=>""));
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">

<tr>
	<td width="100%"><?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"> <b><?php echo l('Authorisation Required'); ?></b><p>

	<form method="POST" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" name="auth">

	<table width="100%" border="0" cellspacing="0" cellpadding="0">

	<tr>
		<td width="10%">&nbsp;</td>
		<td width="90%">

<?php
	if ($this->error)
	{
?>
			<table width="60%" border="0" cellspacing="0" cellpadding="0">

			<tr valign="top">
				<td width=40><img src="<?php echo $g_options["imgdir"]; ?>/warning.gif" width="16" height="16" border="0" hspace=5></td>
				<td width="100%"><?php
		echo $g_options["font_normal"];
		echo "<b>$this->error</b>";
		echo $g_options["fontend_normal"];
?></td>
			</tr>

			</table><p>
<?php
	}
?>

			<table width="30%" border="0" cellspacing="0" cellpadding="0">

			<tr valign="top" bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>">
				<td>
					<table width="100%" border="0" cellspacing="1" cellpadding="4">

					<tr valign="middle">
						<td width="45%" bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>"><?php echo $g_options["font_normal"]; ?><?php echo l('Username'); ?>:<?php echo $g_options["fontend_normal"]; ?></td>
						<td width="55%" bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>"><input type="text" name="authusername" size=20 maxlength=16 value="<?php echo $this->username; ?>" class="textbox"></td>
					</tr>

					<tr valign="middle">
						<td width="45%" bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>"><?php echo $g_options["font_normal"]; ?><?php echo l('Password'); ?>:<?php echo $g_options["fontend_normal"]; ?></td>
						<td width="55%" bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>"><input type="password" name="authpassword" size=20 maxlength=16 value="<?php echo $this->password; ?>" class="textbox"></td>
					</tr>

					<tr valign="middle">
						<td bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>">&nbsp;</td>
						<td bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>">
							<table border="0" cellspacing="0" cellpadding="0">

							<tr valign="middle">
								<td><input type="checkbox" name="authsavepass" value="1" id="savepassyes"<?php if ($this->savepass) echo " checked"; ?> class="checkbox"></td>
								<td nowrap><?php echo $g_options["font_normal"]; ?><label for="savepassyes"><?php echo l('Save my password'); ?></label><?php echo $g_options["fontend_normal"]; ?></td>
							</tr>

							</table></td>
					</tr>

					</table></td>
				<td align="right">
					<table border="0" cellspacing="0" cellpadding="10">
					<tr>
						<td><input type="submit" value="<?php echo l('Next'); ?> &gt;&gt;" id="authsubmit" class="submit"></td>
					</tr>
					</table></td>
			</tr>

			</table><br>


			<?php echo $g_options["font_normal"]; ?><?php echo l('Please ensure cookies are enabled in your browser security options'); ?>.<br>
			<b><?php echo l('Note'); ?>:</b> <?php echo l('Do not select Save my password if other people will use this computer'); ?>.<?php echo $g_options["fontend_normal"]; ?></td>
	</tr>

	</table><br><br>
	</form>

	<script language="JavaScript">
	<!--
		<?php
			if ($this->savepass) {
				echo "document.forms.auth.authsubmit.focus();";
			}
			elseif ($this->username) {
				echo "document.forms.auth.authpassword.focus();";
			}
			else {
				echo "document.forms.auth.authusername.focus();";
			}
		?>
	-->
	</script>

	<?php echo $g_options["fontend_normal"]; ?></td>
</tr>

</table>

<?php
	pageFooter();
?>
