<?php
/****************************************************** Open .node ***
 * Description:   
 * Status:        Stable.
 * Author:        Alexandre Dath <alexandre@dotnode.com>
 * $Id$
 *
 * Copyright (C) 2005 Alexandre Dath <alexandre@dotnode.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 ******************** http://opensource.ikse.net/projects/dotnode ***/

if($token[4] == 'yes')
{
	$db->query('DELETE FROM message WHERE id_from=? AND type=? AND id=?', array($url_id, 'friend_invitation', $_SESSION['my_id']));
	$invitation = $db->getRow('SELECT id,id_invit,level FROM invitation WHERE id=? AND id_invit=?', array($url_id, $_SESSION['my_id']));
	$db->query('DELETE FROM invitation WHERE id=? AND id_invit=?', array($url_id, $_SESSION['my_id']));

	$db->query('INSERT INTO relation SET id=?, id_friend=?, level=?', array($invitation['id'], $invitation['id_invit'], $invitation['level']));
	$db->query('INSERT INTO relation SET id=?, id_friend=?, level=?', array($invitation['id_invit'], $invitation['id'], 'friend'));

	$my_friends_id = $db->getCol('SELECT id FROM relation WHERE id_friend=? ORDER BY last_visit DESC', 0, $_SESSION['my_id']);
        $friend_friends_id = $db->getCol('SELECT id FROM relation WHERE id_friend=? ORDER BY last_visit DESC', 0, $invitation['id']);


	$db->query('UPDATE cache_user SET friends_id=?, nb_friends=? WHERE id=?', array(implode(',',$my_friends_id), count($my_friends_id), $_SESSION['my_id']));

        $db->query('UPDATE cache_user SET friends_id=?, nb_friends=? WHERE id=?', array(implode(',',$friend_friends_id), count($friend_friends_id), $invitation['id']));

	$_SESSION['my_friends_id'] = $my_friends_id;

	$message_values = array(
                'id' => $url_id,
                'id_from' => $_SESSION['my_id'],
                'from_str' => $_SESSION['my_fname'],
                'type' => 'friend_invitation_accepted',
                'dest' => 'one',
                'subject' => strarg(_('%1 %2 has accepted your invitation'), $_SESSION['my_fname'], $_SESSION['my_lname']), 
		'message' => _('Invitation accepted'),
		'box' => 'inbox',
		'date' => time());

	$db->autoExecute('message', $message_values);

	if(get_setting($url_id, 'new_friend_approval') == 'email')
		auto_mail( $_SESSION['my_id'],
				$url_id,
				strarg(_('%1 %2 has accepted your invitation'), $_SESSION['my_fname'], $_SESSION['my_lname']),
				_('Invitation accepted')
			 );


}
elseif($token[4] == 'no')
{
	$db->query('DELETE FROM message WHERE id_from=? AND type=? AND id=?', array($url_id, 'friend_invitation', $_SESSION['my_id']));

	$message_values = array(
			'id' => $url_id,
			'id_from' => $_SESSION['my_id'],
			'from_str' => $_SESSION['my_fname'],
			'type' => 'friend_invitation_refused',
			'dest' => 'one',
			'subject' => strarg(_('%1 %2 has refused your invitation'), $_SESSION['my_fname'], $_SESSION['my_lname']),
			'message' => _('Sorry, but your invitation was refused'),
			'box' => 'inbox',
			'date' => time());

	$db->autoExecute('message', $message_values);

	if(get_setting($url_id, 'new_friend_approval') == 'email')
		auto_mail( $_SESSION['my_id'], 
				$url_id,  
				strarg(_('%1 %2 has refused your invitation'), $_SESSION['my_fname'], $_SESSION['my_lname']),
				_('Sorry, but your invitation was refused')
			 );


}

header('Location: /messages/inbox');

?>
