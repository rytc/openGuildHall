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

/**
 * The model for accessing the groups table
 */
class Model_Groups {
	private $_groupsTable;
	
	/**
	 * Returns the row for the specified group.
	 * \param $id ID of the group
	 */
	public function fetchGroup($id) {
		$table = $this->getGroupsTable();
		
		$where = $table->getMySQL()->quoteInto('id = ?', $id);
		$results = $table->select(array(), $where);
		
		return $results[0];
	}
	
	/**
	 * Returns all of the groups
	 */
	public function fetchGroups() {
		$table = $this->getGroupsTable();
		
		$results = $table->select(array());
		
		return $results;
	}
	
	/**
	 * Updates the name of the specified group
	 * \param $name New name for the group
	 * \param $id ID of the group to change
	 */
	public function update($name, $id) {
		$table = $this->getGroupsTable();
		
		$result = $table->updateId(array('name' => $name), $id);
		
		return $result;
	}
	
	/**
	 * Inserts a new group into the database
	 * \return ID of the newly created group
	 */
	public function insert($name) {
		$table = $this->getGroupsTable();
		
		$result = $table->insert(array('name' => $name));
		
		return $table->getMySQL()->getInsertID();
	
	}
	
	/**
	 * Deletes the specified group
	 * \param $id ID of the group to change
	 */
	public function delete($id) {
		$table = $this->getGroupsTable();
		
		$table->deleteId($id);
	}
	
	/**
	 * \return Returns the table to perform the operations on
	 */
	protected function getGroupsTable() {
		if(!isset($this->_groupsTable)) {
            $this->_groupsTable = new MySQLTable(MySQL::getInstance()->getTablePrefix().'groups');
        }
        
        return $this->_groupsTable;
	}
}