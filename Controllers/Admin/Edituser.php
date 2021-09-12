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
 
class Admin_Edituser extends Action {
	
	protected $_username;
	protected $_mode;
	
	public function init() {
		
		if(Acl::getInstance()->hasAccess('Admin', 'Controls', 'Edit') == DENY) {
			LayoutHelper::displayMessage($this->view, 'Access Denied', 'You do not have the right credentials to view this page.');
			return false;
		}
		
		if($this->isPost()) {
			$postVars = $this->getPostVariables();
			
			$this->_username = str_replace('-', ' ', $postVars['username']);
			
		} else {
			$this->_username = str_replace('-', ' ', $this->getParam(0));			
		}

		if(!FormValidator::validateUsername($this->_username)) {
			LayoutHelper::displayMessage($this->view, 'Malformed URL', 'The requested URL is malformed.');
			return false;
		}
		
		$this->_mode = $this->getParam(1);
			
		return true;
		
	}
	
	public function action() {
		$model = new Model_User();
		$groupModel = new Model_Groups();
		
		$this->view->pagetitle = 'Edit User';
				
		if(!empty($this->_username)) {
			$this->view->mode = 'edit';
		
			$user = $model->fetchUserByUsername($this->_username);
			
			if(empty($user)) {
				LayoutHelper::displayMessage($this->view, 'Invalid User', 'That user does not exist.');
				return;
			}
			
			if($this->isPost()) {
				$postVars = $this->getPostVariables();
				
				if(array_key_exists('madechanges', $postVars)) {
					
					// Change the user's group
					if(array_key_exists('group', $postVars)) {
						$group = $postVars['group'];
						
						if(Validator::isInt($group)) {
							$model->updateById(array('group' => $group), $user['id']);
						}
						
					}
					
					// Clear the user's avatar
					if(array_key_exists('deletevatar', $postVars)) {
						$model->updateById(array('avatar' => 'none'), $user['id']);
					}
					
					// Clear the user's username
					if(array_key_exists('deletesignature', $postVars)) {
						$model->updateById(array('signature' => ''), $user['id']);
					}
					
					if(array_key_exists('deletebio', $postVars)) {
						$model->updateById(array('about' => ''), $user['id']);
					}
					
					if(array_key_exists('disableaccount', $postVars)) {
						$model->updateById(array('disabled' => 1, 'authkey' => ''), $user['id']);
					} elseif(array_key_exists('enableaccount', $postVars)) {
						$model->updateById(array('disabled' => 0), $user['id']);
					}
					
					$this->view->url = array('Go to the user\'s profile' => SITE_PATH.'user/profile/'.$this->_username);
					LayoutHelper::displayMessage($this->view, 'Edit User', 'That user has been edited!');
					return;
				}
			}
			
			
			$this->view->groups = $groupModel->fetchGroups();
			$this->view->user = $user;
			
		} else {
			$this->view->mode = 'search';
		}
		
		LayoutHelper::display($this->view, 'Scripts/admin/Edituser.phtml', SITE_THEME.'admin.css');
		
	}
}