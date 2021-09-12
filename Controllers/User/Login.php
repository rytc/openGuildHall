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
 
define('ERROR_USERNAME_FAIL', -10);

class User_Login extends Action {
	
	public function init() {
	
		Acl::getInstance()->setPageAccessRule(ALLOW);
		
		if(CurrentUser::getInstance()->authenticated) {
			$this->view->displayMessage('Login', 'You are already logged in!');
			return false;
		}
		
		PageTracker::dontTrack();
		
		return true;
	}
	
	public function action() {
		
		
		if($this->isPost()) {
			$errors = array();
			$postVars = $this->getPostVariables();

			try {
				$user = new Model_User($postVars['username']);	
			} catch(Exception $e) {
				$this->view->displayMessage('Login', 'Invalid username or password');
				return;
			}
			
			if(!$user->comparePassword($postVars['password'])) {
				$this->view->displayMessage('Login', 'Invalid username or password');
				return;
			}
			
			
			if($user->disabled >= 1) {
				$this->view->displayMessage('Suspended', 'Sorry, your account has been suspended.');
				return;
			}

			$user->authenticate(isset($postvars['remember']));


			$this->view->displayMessage('Login', 'You have been successfully logged in!');
			
		} else {
			$this->view->assign('pagetitle', 'Login');
			$this->view->display('login.tpl');	
		}
	
	}
	
	public function ajax() {
		
	}
	
}
