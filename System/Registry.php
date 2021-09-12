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
 * A place to store system settings
 *
 * The registry is set on a per-request basis, unlike
 * sessions where data is stored persistently
 */
class Registry {
    private static $_variables;
    
    public static function get($name) {
        if(!isset(self::$_variables[$name])) {
            throw new Exception($name.' not set in the registry.');
        }
            
        return self::$_variables[$name];
    }
    
    public static function set($name, $value) {
        self::$_variables[$name] = $value;
    }
}
