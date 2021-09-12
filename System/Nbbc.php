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

require_once 'Library/nbbclib/nbbc.php';

/**
 * Simple wrapper for Nbbc
 * @link http://nbbc.sourceforge.net/
 */
class Nbbc {
	private static $_instance;
	private $_nbbc;
	
	/**
	 * @return BBCode returns the Nbbc object
	 */
	public function getNbbcObject() {
		return $this->_nbbc;
	}
	
	/**
	 * Parses the given text
	 * @param bool $enableSmileys
	 * @param bool $enableBBCode
	 * @return string Returns the parsed text
	 */
	public function parse($text, $enableSmileys = true, $enableBBCode = true) {
		$this->_nbbc->SetEnableSmileys($enableSmileys);
		
		if($enableBBCode == true) {
			$result = $this->_nbbc->Parse($text);
		} else {
			$result = $this->_nbbc->HTMLEncode($text);
		}
		
		return $result;
	}
	
	/**
	 * Singleton class
	 * @return Nbbc
	 */
	public static function getInstance() {
		if(!is_object(self::$_instance)) {
			self::$_instance = new Nbbc();
		}

		return self::$_instance;
	}

	protected function __construct() {
		$this->_nbbc = new BBCode;
	}
	
}