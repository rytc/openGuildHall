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
 
class Acl {
	private static $_instance;
	protected $_pageAccessRule = ALLOW;
	protected $_aclModel;
	protected $_currentUser;
	protected $_rules;
	protected $_default = DENY;
	protected $_cache;
	
	// Since we call this method always
	// in the Action's init method, good
	// place to hook in the actual checking
	// and killing
	public function setPageAccessRule($access) {
		$this->_pageAccessRule = $access;
		
		if($access != ALLOW) {
			if(!$this->hasAccessByKey($access)) {
				throw new Exception('You don\'t have access to this page');
			}
		}
	}
	
	public function getPageAccessRule() {
		return $this->_pageAccessRule;
	}
	
	
	/**
	 * This method will check the database for the access
	 * the user has the specific resource. $item is usually
	 * the ID of the exact item such as the forum board or 
	 * category. If $item is null, then it's all of the items.
	 * 
	 * The results from the database are cached in the session. So
	 * if the user's group changes and they gain more permissions,
	 * the user will have to login again.
	 */
	public function hasAccess($page, $action, $item = null) {
		$result = $this->_default;
		
		// Return the cached result so we don't have
		// to query the database every time
		$cachekey = $page.$action.$item;
		if(isset($this->_cache->$cachekey)) {
			return $this->_cache->$cachekey;
		}
		
		// Loop through our rules and get their default setting
		if(!empty($this->_rules)) {
			foreach($this->_rules  as $key => $rule) {
				$r = $rule->getArray();
				if($r['page'] == $page && $r['action'] == $action) {
					$result = $r['defaultAccess'];
				}
			}
		}
		
		$user = $this->getCurrentUser();
		
		// Check if our user has access
		//$userPerm = $this->getAclModel()->fetchUserPermission($page, $action, $user->id, $item);
		
		// If there is not a specific rule set for this specific user
		// Then check the user's groups
		if(!$userPerm) {
			$groupPerm = $this->getAclModel()->fetchGroupPermission($page, $action, $user->group, $item);
			
			if(!empty($groupPerm)) {
				$result = $groupPerm;
			}	
		}
		
		$this->_cache->$cachekey = $result;
		return $result;
	}
	
	public function hasAccessByKey($key) {
		$rule = $this->_rules[$key]->getArray();
		$result = $rule['defaultAccess'];
		
		// Check if our user has access
		// $userPerm = $this->getAclModel()->fetchGroupPermission($model, $page, $action, $user->id, $item);
		
		// If there is not a specific rule set for this specific user
		// Then check the user's groups
		if(!isset($userPerm)) {
			$groupPerm = $this->getAclModel()->fetchGroupPermission($page, $action, $user->group, $item);
			
			if(!empty($groupPerm)) {
				$result = $groupPerm;
			}	
		}/* else {
			if(!empty($userPerm)) {
				$result = $userPerm;
			}
		}*/
		
		
		return $result;
	}
	
	public function addRule($key, $rule) {
		$this->_rules[$key] = $rule;
		
		return $this;
	}
	
	public function getRuleByKey($key) {
		return $this->_rules[$key];
	}
	
	public function getCurrentUser() {
		return CurrentUser::getInstance();
	}
	
	public function getAclModel() {
		if(!is_object($this->_aclModel)) {
			$this->_currentUser = new Model_Acl();
		}
		
		return $this->_currentUser;
	}
	
	public function clearCache() {
		$this->_cache->clear();
	}
	
	public static function getInstance() {
		if(!is_object(self::$_instance)) {
			self::$_instance = new Acl;
			
		}
		
		return self::$_instance;
	}
	
	protected function __construct() {
		$this->_cache = new Session('ACL');
	}
	

}
