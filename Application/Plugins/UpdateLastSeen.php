<?php
/* openGuildHall
 * Copyright (C) 2011 Ryan Capote <trooper777@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of 
 * the GNU General Public License as published by the Free Software Foundation; either version 
 * 2 of the License, or (at  your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
 * without even the implied warranty of MERCHANATBILITY or FITNESS FOR A PARTICULAR PURPOSE. 
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program; 
 * if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */
 
class UpdateLastSeen {
 
	public static function pre($action) {
		
		if(CurrentUser::getInstance()->isLoggedIn()) {
			//CurrentUser::getInstance()->buildUnreadList(CurrentUser::getInstance()->lastSeen);
		}		
	}	
	
	public static function post($action) {

		if(!$action->isPost()) {
			PageTracker::track();
		} 
		
		if(CurrentUser::getInstance()->isLoggedIn()) {
			// This method checks if the user us logged in
			CurrentUser::getInstance()->updateLastSeen();
		}
		

	}
}
