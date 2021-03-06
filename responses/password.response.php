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

$todo = $db->getRow('SELECT id_todo, param, id, date FROM todo WHERE id=? AND robot=? AND status=?', array($url_id, 'send_password', 'doing'));
error_log('todo[id]: '.$todo['id']);
if(isset($todo['id']))
{
	if($todo['date']>time()-3600)
		if( $user =& $db->getRow('SELECT id, login, fname, lname, status FROM user WHERE id=?', array($todo['id'])) )
		{
			session_destroy();

			session_set_save_handler ('_sess_open', '_sess_close', '_sess_read', '_sess_write', '_sess_destroy', '_sess_gc');
			session_start();

			$_SESSION['old_password'] = 1;

			$_SESSION['status'] = 'member';

			srand(time());
			$SecID = md5(rand(1,10000000));
			setcookie('SecID', $SecID, time()+31536000, '/');
			$_SESSION['SecID'] = $SecID;


			$_SESSION['my_id'] = $user['id'];
			$_SESSION['my_login'] = $user['login'];
			$_SESSION['my_fname'] = $user['fname'];
			$_SESSION['my_lname'] = $user['lname'];
			if($user['status']=='jail')
			{
				session_unset();
				session_destroy();
				header('Location: /pub/join');
			}

			$_SESSION['my_status'] = $user['status'];
			$_SESSION['my_ip'] = $_SERVER['REMOTE_ADDR'];

			$_SESSION['my_photo'] = build_image_url($user['id']);

			$cache_user = get_cache_user_info($user['id'], 'country, friends_id, communities_id');
			$_SESSION['my_country'] = $cache_user['country'];
			$_SESSION['my_friends_id'] = $cache_user['friends_id'];
			$_SESSION['my_communities_id'] = $cache_user['communities_id'];

			$_SESSION['nb_new_messages'] = $db->getOne('SELECT COUNT(id_mess) FROM message WHERE id=? AND flag=? AND box=?', array($_SESSION['my_id'], 'new', 'inbox'));
			$_SESSION['nb_new_messages_timestamp'] = time();

			$db->query('UPDATE user SET last_visite=? WHERE id=?', array(time(), $_SESSION['my_id']) );
			$db->query('UPDATE todo SET status=? WHERE robot=? AND id=? AND status=?', array('done', 'send_password', $user['id'], 'doing'));

			header('Location: /my/password');
		}
		else
		{
			$db->query('DELETE FROM todo WHERE  robot=? AND id=?', array('send_password', $todo['id']));
			header('Location: /error/bad_link/nouser');
		}
	else
	{
		$db->query('DELETE FROM todo WHERE  robot=? AND id=?', array('send_password', $todo['id']));
		header('Location: /error/bad_link/date_expire');
	}
}
else
{
	header('Location: /error/bad_link/not_found');
}
?>

