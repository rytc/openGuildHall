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
 
define('SUCCESS', true);
define('ERROR_EMPTY', -1);
define('ERROR_LENGTH', -2);
define('ERROR_CHARACTERS', -3);
define('ERROR_FORMAT', -4);
define('ERROR_RECAPTCHA_KEY', -5);
define('ERROR_RECAPTCHA_COOKIE', -6);
define('ERROR_RECAPTCHA_SOL', -7);

class FormValidator {
	
	public static function validateUsername($username) {
		$errors = array();
		
		if(!empty($username)) {
			
			if(!Validator::checkStringLength($username, 3, 16)) {
				$errors[] = ERROR_LENGTH;
			}
			
			if(Validator::hasSpecialCharacters($username)) {
				$errors[] = ERROR_CHARACTERS;
			}
			
		} else {
			$errors[] = ERROR_EMPTY;
		}
		
		if(empty($errors)) {
			return SUCCESS;
		}
		
		return $errors;
	
	}
	
	public static function validatePassword($password) {
		$errors = array();

		if(!empty($password)) {
			if(!Validator::checkStringLength($password, 6, 20)) {
				$errors[] = ERROR_LENGTH;
			}

			if(self::hasInvalidPasswordCharacters($password)) {
				$errors[] = ERROR_CHARACTERS;
			}
		} else {
			$errors[] = ERROR_EMPTY;
		}

		if(empty($errors)) {
			return SUCCESS;
		}
		
		return $errors;
	}
	
	public static function validateEmail($email) {
		$errors = array();

		if(!empty($email)) {
			if(Validator::isValidEmailAddress($email) == false) {
				$errors[] = ERROR_FORMAT;
			}
		} else {
			$errors[] = ERROR_EMPTY;
		}

		if(empty($errors)) {
			return SUCCESS;
		}
		
		return $errors;
	}
	
	
    public static function validateCaptcha($challenge, $response, $recaptcha) {
        $e = array();
		$errors = array();
		
        if(!empty($challenge) &&
         !empty($response)) {
             if(!$recaptcha->checkAnswer($challenge, $response)) {
                  $e = $recaptcha->getError();                              
             }
        } else {
            $errors[] = ERROR_EMPTY;
        }
		
		if(empty($errors)) {
			return SUCCESS;
		}
	
		if($e == 'invalid-site-public-key' ||
		   $e == 'invalid-site-private-key') {
			$errors[] = ERROR_RECAPTHCA_KEY;
		}
		
		if($e == 'invalid-request-cookie') {
			$errors[] = ERROR_RECAPTCHA_COOKIE;
		}
		
		if($e == 'incorrect-captcha-sol') {
			$errors[] = ERROR_RECAPTCHA_SOL;
		}
	
        return $errors;
    }
    
    public static function validateActivationKey($key) {
        $errors = array();
        
        if(!empty($key)) {
            if(!ctype_alnum($key)) {
                $errors[] = ERROR_FORMAT;
            }
        } else {
            $errors[] = ERROR_EMPTY;
        }
        
        return $errors;
        
    }
	
	public static function isValidUrl($string) {
		return get_headers($string) ? true : false;
	} 

	public static function hasInvalidPasswordCharacters($string) {
        // checking for special characters
        $regex ='%["\'\%*!@#^$=~`+\\\\/\-(){}.,<>[\]]%';
        
        return preg_match($regex, $string) ? true: false;
    }
	
	// Returns the salted hashed password with the salt appended
	public static function getHashedPassword($password) {
		$salt = self::generatePasswordSalt();
		
		$result = $salt.self::hashPassword($salt, $password);
		
		return $result;
	}
	
	// Used to check if the password the user entered is correct
	// Only need to pass the raw password the user entered, and this 
	// function will sal & hash it.
	public static function comparePasswords($password, $hashedPassword) {
		$salt = substr($hashedPassword, 0, 8);
		
		$password = $salt.self::hashPassword($salt, $password);
		
		if($password == $hashedPassword) {
			return SUCCESS;
		} else {
			return false;
		}
		
	}
	
	public static function generatePasswordSalt() {
		return substr(str_pad(dechex(mt_rand()), 8, '0', STR_PAD_LEFT), -8);
	}
	
	public static  function hashPassword($salt, $password) {
		return hash('whirlpool', $salt.$password);
	}
}
