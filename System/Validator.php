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
 * This class contains data validation methods.
 * To extend this class, create a subclass in Application/Library
 */
class Validator {

	/** 
	 * Simple method for checking if the length of a 
	 * string is between two values
	 * \param string $string The string to check
	 * \param integer $min The shortest acceptible length
	 * \param integer $max The longest acceptible length
	 * \return bool 
	 */
    public static function checkStringLength($string, $min, $max) {
        if(strlen($string) < $min ||
           strlen($string > $max)) {
              return false;
         }
         return true;
    }
    
	/**
	 * Checks if the string has characters other than alphanumeric
	 * characters.
	 * \param string $string The string to check
	 * \return bool
	 */
    public static function hasSpecialCharacters($string) {
        // checking for special characters
        $regex ='%["\'\%*!@#^$=~`+\\\\/\-(){}.,<>[\]]%';
        
        return preg_match($regex, $string) ? true : false;
    }
    
	/**
	 * Checks if the given email address is valid
	 * @param string $email Email address to validate
	 * @return bool
	 */
    public static function isValidEmailAddress($email) {
        // Regex from regular-expressions.info
        $regex = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}/';
        
        return preg_match($regex, $email) ? true : false;
        
    }
    
    public static function isValidMonth($month) {
        if($month >= 1 &&
           $month <= 12) {
               return true;
        }
        
        return false;
    }
    
    public static function isValidDay($day) {
        if($day >= 1 &&
           $day <= 31) {
               return true;
        }
        
        return false;
    }
    
	/** 
	 * Checks if a two dimentianal array is empty
	 * @return bool
	 */
    public static function isMultiArrayEmpty($array) {
        // We loop through to check if
        // each sub-array is empty
        // this is crap
        foreach($array as $element) {
            if(!empty($element)) {
                return false;
            }
        }
        
        return true;
    }

	/**
	 * Chekcs if the given string is a valid web url
	 * @param string $string URL to validate
	 * @return bool
	 */
	public static function isValidUrl($string) {
		$regex = '/^https?:\/\/(www.)?[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}/';
		
		return preg_match($regex, $string) ? true : false;
	} 
	
	/**
	 * Hardcore function for validating an integer. No decimals, hex, or oct numbers
	 */
	public static function isInt($int) {
		// Is it a number?
		if(is_numeric($int)) {
			// It's a number, but has to be an integer
			if((int)$int == $int) {
				return true;
			} else { // It's a number, but not a integer
				return false;
			}
		} else { // Not a number
			return false;
		}
	}
	
	/**
	 * Checks if a given timezone is a real timezone
	 */
	public static function isValidTimezone($string) {
		$timezones = DateTimeZone::listIdentifiers();
		
		foreach($timezones as $zone) {
			if($string == $zone) {
				return true;
			}
		}
		
		return false;
	}

	/**
	 * Checks if the given URL exists
	 */
	public static function isActiveUrl($string) {
		return get_headers($string) ? true : false;
	} 
	

}

