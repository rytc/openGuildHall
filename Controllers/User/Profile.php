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
 
class User_Profile extends Action {
	protected $_username;
	
	public function init() {
		Acl::getInstance()->setPageAccessRule(ALLOW);
		
		$this->_username = str_replace('-', ' ', $this->getParam(0));
		
		if(!FormValidator::validateUsername($this->_username)) {
			$this->view->displayMessage('Malformed URL', 'The requested URL is malformed');
			return false;
		}
		
		return true;
	}
	
	public function action() {

		try {
			$user = new Model_User($this->_username);
		} catch(Exception $e) {
			$this->view->displayMessage('Invalid User', 'That user does not exist!');
			return;
		}
		
		
		if($user->disabled >= 1 && (Acl::getInstance()->hasAccess('Admin', 'Controls', 'Edit') == DENY)) {
			$this->view->displayMessage('User Profile', 'This user\'s account has been suspended.');
			return;
		}
		
		$this->view->assign('pagetitle', 'User Profile');
		$this->view->assign('user', $user);
		$this->view->display('profile.tpl');
	}
	
}
