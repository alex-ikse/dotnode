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

if(is_numeric($token[2]))
	switch($token[3])
	{
		case 'sent':
			$query = 'DELETE FROM message WHERE id_from=? AND id_mess=? AND box=?';
			$box = 'send';
			break;
		case 'saved':
			$query = 'DELETE FROM message WHERE id=? AND id_mess=? AND box=?';
			$box = 'save';
			break;
		case 'inbox':
		default:
			$query = 'DELETE FROM message WHERE id=? AND id_mess=? AND box=?';
                        $box = 'inbox';
	}
	$db->query($query, array($_SESSION['my_id'], $token[2], $box));

if($token[3])
	header('Location: /messages/'.$token[3]);
else
	header('Location: /messages/inbox');

exit();
?>
