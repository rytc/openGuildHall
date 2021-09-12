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
 * Base class for model objects
 */
abstract class Model {
	protected $_data = array();
	protected $_changes = array();
	protected $_exceptions = array();
	protected $_new = true;
	protected $_tableName;
	private $_table;

	/**
	 * When retrieving a variable directly from the object, it will first
	 * try to call getVARIABLENAME(). For example, if we called $model->username
	 * Model would try to call the protected method getUsername(). This allows
	 * us to perform any required formatting before we return the field.
	 */
	public function __get($key) {
		if(method_exists($this, $method = 'get'.ucfirst($key))) {
			return $this->$method();
		}

		return $this->_data[$key];
	}

	/**
	 * When assigning a variable directly to the object, it will first try 
	 * to call setVARIABLENAME($var). For example, if we called 
	 * $model->username = 'Bob', Model would call setUsername('Bob').
	 * This allows us to perform validation on the fields. If there
	 * is an error when validating, the method should throw and exception
	 */
	public function __set($key, $value) {
		if(method_exists($this, $method = 'set'.ucfirst($key))) {
			try {
				$this->_data[$key] = $this->$method($value);	
				$this->_changes[] = $key;
				return;
			} catch(Exception $e) {
				$this->_exceptions[] = $e;	
			}
		}

		$this->_data[$key] = $value;
	}

	public function getExceptions() {
		return $this->_exceptions;
	}

	/**
	 * This method should be overridden by the child class to
	 * save the data.
	 */
	public function save() {
		if($this->_exceptions) {
			return false;
		}

		return true;
	}

	protected function getTable() {
		if(!isset($this->_table)) {
            $this->_table = new MySQLTable(MySQL::getInstance()->getTablePrefix().$this->_tableName);
        }

        return $this->_table;

	}

};


