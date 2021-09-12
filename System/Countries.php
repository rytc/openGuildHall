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
  * A class to handle countries and country codes
  * List loaded from Library/countries.txt
  * name:code
  * Default list is based on ISO 3166-1 alpha 2 codes
  * @link http://en.wikipedia.org/wiki/ISO_3166-1_alpha-2
  */
class Countries
{
	protected $_countries;
	protected $_path;
	private static $_instance;
	
	/**
	 * Returns the name of the country by it's code
	 * @param string $code The code to look up
	 */
	public function getCountryName($code) {	
		$countries = $this->getCountries();

		$code = strtoupper($code);

		return $countries[$code];
	}
	
	/**
	 * Returns a the whole list of countires.
	 * Results are cached
	 */
	public function getCountries() {
		if(empty($this->_countries))
		{
			$file = fopen('Library/countries.txt', 'r', true);
			
			if(!$file) {
				die('Error loading countries.txt');
			}
				
			while(!feof($file)) {
				$line = fgets($file, 4096);

				list($name, $code) = explode(';', $line);
				$code = strtoupper(trim($code)); 
				$this->_countries[$code] = $name;
			}
			
			fclose($file);
		}
		
		return $this->_countries;

	}
	
	/**
	 * Returns a singleton instance
	 * @return Countries
	 */
	public static function getInstance() {
		if(!is_object(self::$_instance)) {
			self::$_instance = new System_Countries();
		}
			
		return self::$_instance;
	}
	
	private function __construct() { }
	
	private function __clone()
	{ }
	
}
