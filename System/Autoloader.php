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
  * This class is for autoloading classes so
  * we don't have to put 'includes' all over 
  * the place.
  */
class Autoloader {
    
	private static $_paths;
	private static $_modelPath = 'System/Models/';
	
	/**
	 * The function that performs loading of the class
	 * script. It searches through all of the $_paths
	 * looking for a file with a similar class name.
	 * Throws an exception on failure.
	 * 
	 * For example, if I needed the class MySQLTable,
	 * it would look for a file named MySQLTable.php
	 *
	 * Model classes are searched for in the $_modelPath.
	 * By default, Model_Forum would be located in
	 * Application/Models/Forum.php
	 *
	 * This method is called by PHP when a class is required.
	 * \see
	 * \ref how_controllers_work "How Controllers and Actions work"
	 * \throw Exception Could not find the class
	 * \param string $classname class to load
	 */
	public static function _loadClass($classname) {
	
		if(substr($classname, 0, 6) == 'Smarty') {
			return;
		}
		
		// Search for models in the model path
		if(substr($classname, 0, 6) == 'Model_') {
			// Model_Forum => Application/Models/Forum.php
			$p = self::$_modelPath.substr($classname, 6, strlen($classname)).'.php';
			
			if(file_exists($p)) {
				require $p;
				
				if(class_exists($classname)) {
					return;
				}
			}
		}
	
		// Loop through the paths
		foreach(self::$_paths as $path) {
		
			// If there is no slash at the end
			if(substr($path, -1) != '/') {
				$path .= '/';
			}
		
			$p = $path.$classname.'.php';
			if(file_exists($p)) {
				require $p;
				
				if(class_exists($classname)) {
					return;
				}
			}
		}
		
		throw new Exception('Class '.$classname.' could not be found by the autoloader', -1);
		
	}
	
	/**
	 * Sets the path for the Models. Any class prefixed with Model_
	 * will be loaded from this directory. Default is Application/Models
	 * \param $path The path to search for Model classes
	 */
	public static function setModelPath($path) {
		$this->_modelPath = $path;
	}
  
	/**
	 * This method adds a path to the list of paths.
	 * \param $path path to add to the list
	 */
	public static function addPath($path) {
		self::$_paths[] = $path;
	}
  
	/**
	 * This method registers loadClass with PHP
	 */
	public static function registerAutoloader() {
		spl_autoload_register(array('Autoloader', '_loadClass'));
	}
    
}
