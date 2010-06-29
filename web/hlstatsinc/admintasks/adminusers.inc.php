<?php
/**
 * edit HLStats users
 * @package HLStats
 * @author Johannes 'Banana' Keßler
 * @copyright Johannes 'Banana' Keßler
 */
 
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
 * + 2007 - 2010
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
$return = false;

if(isset($_POST['sub']['edituser'])) {
	$username = trim($_POST['user']['username']);
	$pw = trim($_POST['user']['password']);

	if(!empty($username)) {
		$do = $adminObj->updateLogin($username,$pw);
		if($do === true) {
			header('Location: index.php?mode=admin&task=adminusers');
		}
		else {
			$return['msg'] = l('Error with update');
			$return['status'] = "1";
		}
	}
}

pageHeader(array(l("Admin"),l('Users')), array(l("Admin")=>"index.php?mode=admin",l('Users')=>''));
?>
<div id="sidebar">
	<h1><?php echo l('Options'); ?></h1>
	<div class="left-box">
		<ul class="sidemenu">
			<li>
				<a href="<?php echo "index.php?mode=admin"; ?>"><?php echo l('Back to admin overview'); ?></a>
			</li>
		</ul>
	</div>
</div>
<div id="main">
	<h1><?php echo l('Users'); ?></h1>
	<p>
		<?php echo l('Passwords are encrypted in the database and so cannot be viewed. However, you can change a user\'s password by entering a new plain text value in the Password field'); ?><br />
		<br />
		<?php echo l('Note: After changing the admin password you need to authenticate again.'); ?>
	</p>
	<?php
		if(!empty($return)) {
			if($return['status'] === "1") {
				echo '<div class="error">',$return['msg'],'</div>';
			}
			elseif($return['status'] === "2") {
				echo '<div class="success">',$return['msg'],'</div>';
			}
		}
	?>
	<form method="post" action="">
		<table cellpadding="2" cellspacing="0" border="0">
			<tr>
				<th><?php echo l('Username'); ?></th>
				<td>
					<input type="text" name="user[username]"
						value="<?php echo $adminObj->getUsername(); ?>" />
				</td>
			</tr>
			<tr>
				<th><?php echo l('Password'); ?></th>
				<td>
					<input type="password" name="user[password]" value="" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<button type="submit" title="<?php echo l('Save'); ?>" name="sub[edituser]">
						<?php echo l('Save'); ?>
					</button>
				</td>
			</tr>
		</table>
	</form>
</div>
