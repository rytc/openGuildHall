<?php if(!defined('SITE_PATH')) die('No direct access allowed');
/* openGuildHall
 * Copyright (C) 2011 Ryan Capote <trooper777@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of 
 * the GNU General Public License as published by the Free Software Foundation; either version 
 * 2 of the License, or (at  your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program; 
 * if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */
 
class PageTracker {
	protected static $_dontTrack = false;
	
	public static function track() {

		if(self::$_dontTrack) {
			return;
		}
		
		$session = new Session();
		
		$session->lastUri = $_SERVER['REQUEST_URI'];
	}
	
	public static function dontTrack() {
		self::$_dontTrack = true;
	}
	
	public static function getLastUri() {
		$session = new Session();
		
		if(!isset($session->lastUri)) {
			return '/';
		} else {
			return $session->lastUri;	
		}
		
	}
}
