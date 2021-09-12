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
  * A class that allows caching of data
  */
 class Cache {
	protected $_cachePath = 'Application/cache/';
	
	/**
	 * Caches data to a file
	 * @param string $name Name of the data to cache
	 * @param mixed $array Array of data to cache. If it's not an array, then it will convert it into an array.
	 * @param int $cachetime The amount of time in seconds of how long the data should be cached for. 0 for infinity
	 */
	public function cache($name, $array, $cachetime) {
		$cachepath =  $this->_getCachePath($name);
		
		// Because we cache our data with the JSON format
		// We are expecting arrays
		if(!is_array($array)) {
			$data = array();
			$data[] = $array;
		} else {
			$data = $array;
		}
		
		$data['_cacheexpire'] = time() + $cachetime;
		
		$result = json_encode($data);
		
		$file = fopen($cachepath, 'w');
		fwrite($result);
		fclose($file);
	}
	
	/**
	 * Loads cached data
	 * @param string $name Name of the data to load
	 * @param bool $force Force the load of the data even if it's expired
	 * @return mixed Returns the data on success, returns false if the file does not exist or if the file has expired
	 */
	public function load($name, $force = false) {
		$cachepath = $this->_getCachePath($name);
		
		if(!file_exists($cachepath)) {
			return false;
		}
		
		$file = fopen($cachepath, 'r');
		$content = fread($file, filesize($cachepath));
		fclose($file);
		
		$data = json_decode($content, true);
		
		// Only check if the cache has expired if we force the loading
		if($force == false && $this->isCacheExpired($cachepath)) {
			return false;
		} 
		
		// Remove the expiration date before we return the data
		unset($data['_cacheexpire']);
		
		return $data;
	}
	
	/**
	 * @param string $name Name of the data
	 * @return bool Returns true if cache has expired
	 */
	public function isCacheExpired($name) {
		$cachepath = $this->_getCachePath($name);
		
		if(file_exists($cachepath)) {
			$file = fopen($cachepath, 'r');
			$content = fread($file, filesize($cachepath));
			fclose($file);
			
			$data = json_decode($content, true);
			
			$expire = $data['_cacheexpire'];
			if($expire > 0 && $expire < time()) {
				return true;
			}
			
		} else {
			return true;
		}
		
		return false;
	}
	
	protected function _getCachePath($name) {
		return $this->_cachePath.str_replace('/', '_', $name);
	}
 
 }
 