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

class Log {
	private $_file;
	
	public function __construct($file) {
		$this->_file = fopen($file, 'a');
		
	}
	
	public function write($string) {
		fwrite($this->_file, $string);
	}
	public function writeLine($string) {
		$this->write($string."\n");
	}
	
	function __destruct() {
		fclose($this->_file);
	}
	
}
