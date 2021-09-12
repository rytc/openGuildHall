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
  * Manage session variables per namespace
  */
class Session {
	private $_namespace;
	
	public function __construct($namespace = '_default') {
		$this->_namespace = $namespace;
	}
	
	/**
	 * Erase all variables in the namespace
	 */
	public function clear() {
		unset($_SESSION[$this->_namespace]);
	}
	
	public function __set($name, $value) {
		$_SESSION[$this->_namespace][$name] = $value;
	}
	
	public function __get($name) {
		if(isset($_SESSION[$this->_namespace]) && array_key_exists($name, $_SESSION[$this->_namespace])) {
			return $_SESSION[$this->_namespace][$name];
		}
		
        return null;
	}

	public function __isset($name) {
		return isset($_SESSION[$this->_namespace][$name]);
	}


	public function __unset($name) {
		unset($_SESSION[$this->_namespace][$name]);
	}
    
};

