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
 * Model for manipulating a forum thread
 */
class Model_Thread extends Model {

	public function __construct($id = NULL) {
		$this->_tableName  = 'forum_threads';

		if($id && Validator::isInt($id)) {
			$table = $this->getTable();

			$where = $table->getMySQL()->insertInto('id = ?', $id);
			$result = $table->select(array(), $where);
			
			$this->_new = false;
			foreach($result as $key => $value) {
				$this->_data[$key] = $value;
			}
		}
	}

	public function save() {
		if(empty($this->_exceptions)) {
			return false;
		}

		$table = $this->getTable();

		if($this->_new) {
			$result = $table->insert($this->_data);
			$this->_data['id'] = $result;
		} else {
			$where = $table->getMySQL()->insertInto('id = ?', $this->id);
			$result = $table->update($this->_data, $where);
		}

		return true;
	}


	protected function setId($id) {
		throw new Exception('ID is immutable');
	}

	protected function setTitle($title) {
		if(!Validator::checkStringLength($title, 4, 60)) {
			throw new Exception('Invalid title');
		}

		return $title;
	}

	protected function setAuthor_id($id) {
		if(!Validator::isInt($id)) {
			throw new Exception('Invalid user ID');
		}

		return $id;
	}

	protected function setBoard_id($id) {
		if(!Validator::isInt($id)) {
			throw new Exception('Invalid board ID');
		}

		return $id;
	}

	protected function setOriginal_date($timestamp) {
		if(!Validator::isInt($timestamp)) {
			throw new Exception('Invalid timestamp. Please inform the admin');
		}

		return $timestamp;
	}

	protected function setDate($timestmap) {
		if(!Validator::isInt($timestamp)) {
			throw new Exception('Invalid timestamp. Please inform the admin');
		}

		return $timestamp;
	}

	protected function setLocked($locked) {
		if(!$locked) {
			return 0;
		} else {
			return 1;
		}
	}

	protected function setSticky($sticky) {
		if(!$sticky) {
			return 0;
		} else {
			return 1;
		}
	}



};
