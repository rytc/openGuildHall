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
 * Model for accessing user data
 */
class Model_User extends Model {
    /** 
     * @param $id 
     *      To load information from the database, include either the user's
     *      id or the user's username
     */
    public function __construct($id = NULL) {
        $this->_tableName = 'users';

        if($id != NULL) {
            $table = $this->getTable();
            $result = array();

            if(Validator::isInt($id)) {
                // fetch data from database based on numerical ID

                $where = $table->getMySQL()->quoteInto('id = ?', $id);
                $result = $table->select(array(), $where, null, '1');

            } elseif(is_string($id)) {
                // fetch data from database based on username
                $this->username = $id;

                if(!$this->_exceptions) {
                    $where = $table->getMySQL()->quoteInto('username = ?', $id);    
                    $result = $table->select(array(), $where, null, '1');
                } else {
                    throw new Exception('Malformed username');
                }               
                
            }

            if(!$result) {
                throw new Exception('That user doesn\'t exist');
            }
                
            $this->_new = false;
            foreach($result as $key => $value) {
                $this->_data[$key] = $value;
            }
        }

    }

    public function save() {
        if(!empty($this->_exceptions)) {
            return false;
        }

        $table = $this->getTable();

        if($this->_new) {
            $result = $table->insert($this->_data);
        } else {
            $where = $table->getMySQL()->quoteInto('id = ?',$this->id);

            // Little  trick to only update what has been changed
            $data = array();
            foreach($this->_changes as $changedKey) {
                $data[] = $this->_data[$changedKey];
            }

            $result = $table->update($data, $where);
        }

        return true;
    }

    public function authenticate($remember) {
        if($remember) {
            $key = substr(md5(uniqid(mt_rand())),0,16);
            
            $this->authkey = $key;
            $this->save();

            Cookie::set('oghauth', $this->id.':'.$key, time()+60*60*24*30);
        }

        // Manage the current sessio
        CurrentUser::getInstance()->_authenticated = true;
        CurrentUser::getInstance()->_initialized = false;
        CurrentUser::getInstance()->initialize($user);
        
        // Clear the 'guest' permissions from the session
        Acl::getInstance()->clearCache();
    }

    public function updateLastSeen($username) {
        $table = $this->getTable();

        $where = $table->getMySQL()->quoteInto('username = ?', $username);
        $result = $table->update(array('last_seen' => time()), $where);

        return $result;
    }

    public function comparePassword($password) {
        if(!$this->password) {
            return false;
        }

        $pw = $this->_data['password'];
        $salt = substr($pw, -8);
        $password = $salt.$this->hashPassword($salt, $password);

        if($pw != $password) {
            return false;
        }

        return true;
    }

    protected function setId($id) {
        throw new Exception('ID is immutable.');
    }

    protected function setUsername($username) {
        if(empty($username)) {
            throw new Exception('Please enter a username');
        }

        if(!Validator::checkStringLength($username, 3, 16)) {
            throw new Exception('Invalid username length');
        }

        if(Validator::hasSpecialCharacters($username)) {
            throw new Exception('Invalid characters in username');
        }

        // If we are creating a new User,
        // And we set the username, check to see if the 
        // username is already registered
        if($this->_new) {
            if($this->pokeUsername($username)) {
                throw new Exception('Username is already registered');
            }
        }

        return $username;
    }

    protected function setPassword($password) {
        if(empty($password)) {
            throw new Exception('Please enter a password');
        }

        if(!Validator::checkStringLength($password, 6, 32)) {
            throw new Exception('Invalid password length');
        }

        if(preg_match('%["\'\%*!@#^$=~`+\\\\/\-(){}.,<>[\]]%', $password)) {
            throw new Exception('Invalid characters in password');
        }

        $salt = $this->generatePasswordSalt();
        $password = $salt.$this->hashPassword($salt, $password);

        return $password;

    }

    protected function getPassword() {
        return ''; // Keep the password hidden
    }

    protected function setEmail($email) {
        
        if(empty($email)) {
            throw new Exception('Please enter an email address');
        }

        if(!Validator::isValidEmailAddress($email)) {
            throw new Exception('Invalid email address');
        }

        return $email;

    }

    protected function setCountry($country) {
        $countries = Countries::getInstance()->getCountries();
        if(!array_key_exists($country, $countries)) {
            throw new Exception('Invalid country');
        }

        return $country;
    }

    protected function setBirthday($birthday) {
        
        if(!is_array($birthday)) {
            throw new Exception('Malformed birthday data');
        }

        $day = $birthday['day'];
        if(!Validator::isValidDay($day)) {
            throw new Exception('Invalid birth day');
        }

        $month = $birthday['month'];
        if(!Validator::isValidMonth($month)) {
            throw new Exception('Invalid birth month');
        }

        $year = $birthday['year'];
        if(!Validator::isInt($year)) {
            throw new Exception('Invalid birth year');
        }

        return mktime(0, 0, 0, $month, $day, $year);
    }

    protected function setGender($gender) {
        if($gender != 'male' && $gender != 'female' && $gender != 'none') {
            throw new Exception('Invalid gender');
        }

        return $gender;
    }

    protected function setGroup($group) {
        if(!Validator::isInt($group)) {
            throw new Exception('Invalid group');
        }

        return $group;
    }

    protected function getGroup_name() {
        $table = new MySQLTable(MySQL::getInstance()->getTablePrefix().'groups');

        $where = $table->getMySQL()->quoteInto('id = ?', $this->_data['group']);
        $result = $table->select(array('name'), $where);

        return $result[0]['name'];
    }

    protected function setWebsite($website) {
        if(empty($website) || Validator::isActiveUrl($website)) {
            return $website;
        } else  {
            throw new Exception('Invalid website');
        }

    }

    protected function setAvatar($avatar) {
        if(empty($avatar)) {
            return $avatar;
        }

        if(Validator::isValidUrl($avatar)) {
            list($width, $height) = getimagesize($avatar);

            if(!getimagesize($avatar) || ($width > 80 || $height > 80)) {
                throw new Exception('Invalid image (Max size is 80x80');
            } else {
                return $avatar;
            }
        } else {
            throw new Exception('Invalid URL');
        }
        
    }

    protected function setContact_privacy($value) {
        if(!Validator::isInt($value)) {
            throw new Exception('Invalid privacy setting');
        }

        return $value;
    }

    protected function setTimezone($timezone) {
        if(!Validator::isValidTimezone($timezone)) {
            throw new Exception('Invalid timezone');
        }

        return $timezone;
    }

    protected function setAbout($about) {
        if(!Validator::checkStringLength($about, 0, 500)) {
            throw new Exception('Invalid biography length');
        }

        return $about;
    }

    protected function setSignature($signature) {
        if(!Validator::checkStringLength($signature, 0, 100)) {
            throw new Exception('Invalid signature length (Max: 100)');
        }

        return $signature;
    }

    protected function setTwitter($twitter) {
        if(!Validator::checkStringLength($twitter, 0, 50) || Validator::hasSpecialCharacters($twitter)) {
            throw new Exception('Invalid Twitter username');
        }

        return $twitter;
    }

    protected function setAim($aim) {
        if(!Validator::checkStringLength($aim, 0, 50) || Validator::hasSpecialCharacters($aim)) {
            throw new Exception('Invalid AIM username');
        }

        return $aim;
    }

    protected function setXbl($xbl) {
        if(!Validator::checkStringLength($xbl, 0, 50) || Validator::hasSpecialCharacters($xbl)) {
            throw new Exception('Invalid XBox Live username');
        }

        return $xbl;
    }

    protected function setSteam($steam) {
        if(!Validator::checkStringLength($steam, 0, 50) || Validator::hasSpecialCharacters($steam)) {
            throw new Exception('Invalid Steam username');
        }

        return $steam;
    }

    /**
     * Checks if the username is already registered 
     * @param $username
     *      The username to check
     * @return 
     *      True if taken, false if available
     */
    private function pokeUsername($username) {
        $where = $this->getTable()->getMySQL()->quoteInto('username = ?', $username);
        $result = $this->getTable()->select(array('id'), $where, 1);

        if(!$result) {
            return false;
        }

        return true;
    }

    private function generatePasswordSalt() {
        return substr(str_pad(dechex(mt_rand()), 8, '0', STR_PAD_LEFT), -8);
    }

    private function hashPassword($salt, $password) {
        return hash('whirlpool', $salt.$password);
    }

};

