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
 

class Access_Rule {
	protected $_page;
	protected $_action;
	protected $_defaultAccess;
	protected $_rules;
	
	public function __construct($page, $action, $defaultAccess = ALLOW) {
		$this->_page = $page;
		$this->_action = $action;
		$this->_defaultAccess = $defaultAccess;
	}
	
	public function getArray() {
		return array('page' => $this->_page,
					 'action' => $this->_action,
					 'defaultAccess' => $this->_defaultAccess);
	}
	
	public function getPage() {
		return $this->_page;
	}
	
	public function getAction() {
		return $this->_action;
	}
	
	public function getDefaultAccess() {
		return $this->_defaultAccess;
	}
}
