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
 
 /**
  * Model for accessing ACL data from the database
  */
class Model_Acl {
	private $_groupsTable;
	private $_groupPermissionsTable;
	private $_userPermissionsTable;
	
	/**
	 * Fetches the permission based on the given criteria.
	 * \param $page The page for the permission
	 * \param $action The action for the permission
	 * \param $groupId The ID of the group that the permissions belong to.
	 * \param $itemId The ID for a specific item for the permission. NULL value means all of the items.
	 * \param $allowNull True means that it will include NULL results
	 */
	public function fetchGroupPermission($page, $action, $groupId, $itemId = NULL, $allowNull = true) {
		$table = $this->getGroupPermissionsTable();
		
		$where .= $table->getMySQL()->quoteInto(' `page` = ?', $page);
		$where .= $table->getMySQL()->quoteInto(' AND `action` = ?', $action);
		$where .= $table->getMySQL()->quoteInto(' AND `group_id` = ?', $groupId);
		
		if($allowNull == true && $itemId != NULL) {
			$where .= $table->getMySQL()->quoteInto(' AND (`item` = ? OR `item` IS NULL)', $itemId);
		} elseif($itemId == NULL) {
			$where .= ' AND `item` IS NULL';
		} else {
			$where .= $table->getMySQL()->quoteInto(' AND `item` = ?', $itemId);
		}
		
		$result = $table->select(array('access'), $where, NULL, '1');

		return $result['access'];
	}
	
	/*
	 * Fetch All of the permissions for a page for a group
	 * \param $page The page to fetch the permissions for
	 * \param $group
	 *
	public function fetchGroupPermissions($page, $groupId, $itemId = NULL, $allowNull = true) {
		$table = $this->getGroupPermissionsTable();
		
		$where .= $table->getMySQL()->quoteInto(' AND `page` = ?', $page);
		$where .= $table->getMySQL()->quoteInto(' AND `group_id` = ?', $groupId);
		
		/*if($allowNull == true && $item != NULL) {
			$where .= $table->getMySQL()->quoteInto(' AND (`item` = ? OR `item` IS NULL)', $item);
		} elseif($item == NULL) {
			$where .= ' AND `item` IS NULL';
		} else {
			$where .= $table->getMySQL()->quoteInto(' AND `item` = ?', $item);
		}
		echo $where;
		$result = $table->select(array(), $where);

		return $result;
	}*/
	
	/**
	 * Return all of the "global" permissions for the group
	 * \param $groupId The ID of the group to that the permissions belong to
	 */
	public function fetchGroupGlobalPermissions($groupId) {
		$table = $this->getGroupPermissionsTable();
		
		$where = $table->getMySQL()->quoteInto('group_id = ? AND `item` IS NULL ', $groupId);
		$result = $table->select(array(), $where);
		
		return $result;
	}
	
	/*
	 *
	 *
	public function fetchGroupSpecificPermissions($groupId) {
		$table = $this->getGroupPermissionsTable();
		
		$where = $table->getMySQL()->quoteInto('group_id = ? AND `item` IS NOT NULL ', $groupId);
		$result = $table->select(array(), NULL);
		
		return $result;
	}*/
	
	/*
	public function fetchUserPermission($page, $action, $userId, $item = NULL) {
		return NULL;
	}
	
	public function insertUserPermission($module, $page, $action, $item, $userId) {
		$table = $this->getUserPermissionTable();
		
		$data['module'] = $module;
		$data['paeg'] = $page;
		$data['action'] = $action;
		$data['item'] = $item;
		$data['user_id'] = $userId;
		
		$table->insert($data);
	}
	
	public function deleteUserPermission($module, $page, $action = NULL, $item = NULL, $userId = NULL) {
		$table = $this->getUserPermissionsTable();
		
		$where = $table->getMySQL()->quoteInto('`module` = ?', $module);
		$where .= $table->getMySQL()->quoteInto(' AND `page` = ?', $page);
		
		// We can delete all actions if we want
		if($action != NULL) {
			$where .= $table->getMySQL()->quoteInto(' AND `action` = ?', $action);
		}
		
		if($item == NULL) {
			$where .= $table->getMySQL()->quoteInto(' AND `item` IS NULL', NULL);
		} else {
			$where .= $table->getMySQL()->quoteInto(' AND `item` = ?', $item);
		}
		
		if($userId != NULL) { 
			$where .= $table->getMySQL()->quoteInto(' AND `user_id` = ?', $userId);
		}
		
		$table->deleteAllWhere($where);
	}*/
	
	/**
	 * Insert a permission into the database for a specific group
	 * \param $page The page that the permission is for
	 * \param $action The action that the permission is for
	 * \param $itemId The ID for the specific item that the permission belongs to. NULL value means all items.
	 * \param $groupId The ID for the group that the permission belongs to.
	 * \param $access The access for the permission. Either ALLOW or DENY
	 */
	public function insertGroupPermission($page, $action, $itemId, $groupId, $access) {
		$table = $this->getGroupPermissionsTable();
		
		$data['page'] = $page;
		$data['action'] = $action;
		
		if($itemId != NULL) {
			$data['item'] = $itemId;
		}
		
		$data['group_id'] = $groupId;
		$data['access'] = $access;
		
		$table->insert($data);
	}
	
	/**
	 * Update a permission based on the given criteria for a specific group.
	 * \param $page The page that the permission belongs to
	 * \param $action The action that the permission belongs to
	 * \param $groupId The ID of the group that the permission belongs to.
	 * \param $access The access of the permission. Either ALLOW or DENY
	 */
	public function updateGroupPermission($page, $action, $item, $groupId, $access) {
		$table = $this->getGroupPermissionsTable();
		
		$where = $table->getMySQL()->quoteInto(' `page` = ?', $page);
		$where .= $table->getMySQL()->quoteInto(' AND `action` = ?', $action);
		
		
		if($item == NULL) {
			$where .= $table->getMySQL()->quoteInto(' AND `item` IS NULL', NULL);
		} else {
			$where .= $table->getMySQL()->quoteInto(' AND `item` = ?', $item);
		}
		
		$where .= $table->getMySQL()->quoteInto(' AND `group_id` = ?', $groupId);
		
		$data['access'] = $access;
		
		$table->update($data, $where);
	}
	
	/**
	 * Delete a group based on the given criteria for a specific group, or all groups.
	 * \param $page The name of the page of the permissions to be deleted.
	 * \param $action The name of the action of the permissions to be deleted. Can be NULL for all actions.
	 * \param $item The ID of the item for of the permissions to be deleted. Can be NULL for all items.
	 * \param $groupId The ID of the group of the permissions to be deleted. Can be NULL for all groups.
	 */
	public function deleteGroupPermission($page, $action = NULL, $item = NULL, $groupId = NULL) {
		$table = $this->getGroupPermissionsTable();
		
		$where = $table->getMySQL()->quoteInto('`page` = ?', $page);
		
		// We can delete all actions if we want
		if($action != NULL) {
			$where .= $table->getMySQL()->quoteInto(' AND action = ?', $action);
		}
		
		if($item == NULL) {
			$where .= $table->getMySQL()->quoteInto(' AND item IS NULL', NULL);
		} else {
			$where .= $table->getMySQL()->quoteInto(' AND item = ?', $item);
		}
		
		$where .= $table->getMySQL()->quoteInto(' AND group_id = ?', $groupId);
		
		$table->deleteAllWhere($where);
	}
	
	/**
	 * Returns the MySQLTable for the groups table.
	 */
	protected function getGroupsTable() {
		if(!isset($this->_groupsTable)) {
            $this->_groupsTable = 
            			new MySQLTable(MySQL::getInstance()->getTablePrefix().'groups');
        }
        
        return $this->_groupsTable;
	}
	
	/**
	 * Returns the MySQLTable for the group permissions table
	 */
	protected function getGroupPermissionsTable() {
		if(!isset($this->_groupPermissionsTable)) {
			$this->_groupPermissionsTable = 
						new MySQLTable(MySQL::getInstance()->getTablePrefix().'group_perms');
		}
		
		return $this->_groupPermissionsTable;
	}
	
	/*
	 * Returns the MySQLTable for the user permissions table
	 *
	protected function getUserPermissionsTable() {
		if(!isset($this->_userPermissionsTable)) {
			$this->_userPermissionsTable = 
						new MySQLTable(MySQL::getInstance()->getTablePrefix().'user_perms');
		}
		
		return $this->_userPermissionsTable;
	}*/
	
};
