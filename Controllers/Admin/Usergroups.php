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
 
class Admin_Usergroups extends Action {
	protected $_group;
	
	public function init() {
				
		if(Acl::getInstance()->hasAccess('Admin', 'Controls', 'Edit') == DENY) {
			LayoutHelper::displayMessage($this->view, 'Access Denied', 'You do not have the right credentials to access this page.');
			return false;
		}
		
		return true;
	}
	
	public function action() {
		$this->view->pagetitle = 'User Groups';
		$model = new Model_Groups();
		$userModel = new Model_User();
		
		if($this->getParam(0) == 'delete') {
			
			$id = $this->getParam(1);
			
			if(!Validator::isInt($id)) {
				LayoutHelper::displayMessage($this->view, 'User Groups', 'Invalid group id');
				return;
			}
			
			if($userModel->fetchUserCountInGroup($id) > 0) {
				LayoutHelper::displayMessage($this->view, 'User Groups', 'You have to remove all users from that group first.');
				return;
			}
			
			$model->delete($id);
		}
		
		// New user group
		if($this->isPost()) {
			$postVars = $this->getPostVariables();
			
			if(array_key_exists('name', $postVars)) {
				$name = $postVars['name'];
			
				if(!Validator::checkStringLength($name, 3, 20) || 
					Validator::hasSpecialCharacterS($name)) {
					LayoutHelper::displayMessage($this->view, 'User Groups', 'Invalid group name');
					return;
				
				}
			}
			
			
			if(array_key_exists('groupid', $postVars)) {
			// Edit group	
				
				$id = $postVars['groupid'];
				
				if(!Validator::isInt($id)) {
					LayoutHelper::displayMessage($this->view, 'User Groups', 'Invalid group id');
					return;
				}
				
				$model->update($name, $id);

			} elseif(array_key_exists('moveusers', $postVars)) {
				$fromId = $postVars['from'];
				$toId = $postVars['to'];
				
				if(!Validator::isInt($fromId) || !Validator::isInt($toId)) {
					LayoutHelper::displayMessage($this->view, 'User Groups', 'Invalid group id');
					return;
				}
				
				if($fromId != $toId) {
					$userModel->moveAllUserGroups($fromId, $toId);
				}
				
			} elseif(array_key_exists('name', $postVars)) {
			// New group
				$model->insert($name);
			}
		}

		
		$groups = $model->fetchGroups();
		
		foreach($groups as &$group) {
			$group['user_count'] = $userModel->fetchUserCountInGroup($group['id']);
		}
		
		
		$this->view->groups = $groups;
		$this->view->addJavascript('Scripts/admin/Usergroups.js');
		LayoutHelper::display($this->view, 'Scripts/admin/Usergroups.phtml', SITE_THEME.'admin.css');
		
	}
	
	public function ajax() {
		
		if($this->isPost()) {
			PageTracker::dontTrack();
			$model = new Model_Groups();
			$postVars = $this->getPostVariables();
			
			// Group ID and 'delete' means we want to *gasp* delete a group

			if(array_key_exists('groupid', $postVars)) {
			// If groupid exists, then we are editing a group
				$id = $postVars['groupid'];
				$name = $postVars['name'];
				
				if(!Validator::isInt($id)) {
					echo 'Invalid group ID';
					return;
				}
				
				if(!Validator::checkStringLength($name, 3, 20) || 
					Validator::hasSpecialCharacters($name)) {
					echo 'Invalid group name';
					return;
				}
				
				$model->update($name, $id);
				echo '0';
			}
			
		}
		
	}
}
