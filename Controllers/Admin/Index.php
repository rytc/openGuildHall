<?php
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
 
class Admin_Index extends Action {

	public function init() {
		
		if(Acl::getInstance()->hasAccess('Admin', 'Controls', 'Edit') == DENY) {
			LayoutHelper::displayMessage($this->view, 'Access Denied', 'You do not have the right credentials to access this page.');
			return false;
		}
		
		return true;
		
	}
	
	public function action() {
		
		$this->view->pagetitle = 'Admin Controls';
		
		LayoutHelper::display($this->view, 'Scripts/Admin/Index.phtml', SITE_THEME.'admin.css');
	}
	
}
