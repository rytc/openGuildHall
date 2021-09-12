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
 
class CurrentUser {
	private $_data;
	private $_model;
	//private $_initialized;
	private static $_instance;
    
	public function initialize($user = null) {
		if(!$this->_initialized) {
			
			// If the user is logged in
			if($this->_authenticated == true) {
				
				if($user != null) {
					$this->_user = $user;
				} else {
					$user = $this->_user;
				}

				$this->username = $user['username'];
				$this->id = $user['id'];
				$this->group = $user['group'];
				$this->lastSeen = $user['last_seen']; // This gets updated in the FrontController
				$this->timezone = $user['timezone'];
				
				// If it's not set, use the default
				$this->theme = ($user['theme'] != null) ? $user['theme'] : DEFAULT_THEME;
				$this->dateformat = ($user['dateformat']) ? $user['dateformat'] : DEFAULT_DATE_FORMAT;
				$this->timeformat = ($user['timeformat']) ? $user['timeformat'] : DEFAULT_TIME_FORMAT;
				
				$this->buildUnreadList($this->lastSeen);
			// Otherwise we set the defaults	
			} else {
				
				$this->username = 'guest';
				$this->id = null;
				$this->group = 1;
				$this->lastSeen = time();
				
				$this->theme = DEFAULT_THEME;
				$this->dateFormat = DEFAULT_DATE_FORMAT;
				$this->timeFormat = DEFAULT_TIME_FORMAT;
				$this->timezone = DEFAULT_TIMEZONE;
			}
			
			$this->_initialized = true;
		}
		
	}
	
	/*
	 * This method simply tries to authenticate
	 * the user through a cookie. The cookie is 
	 * set if the user selects remember when they
	 * login
	 */
	public function tryCookieAuthentication() {
		
		// If the cookie isn't set
		// then we can't login
		if(!Cookie::exists('rauth')) {
		    return;
		}

		$cookie = Cookie::get('rauth');
		$userModel = new Model_User();

		// Cookie is setup like this:
		// id:key
		//
		// id is the user's id
		// key is a randomly generated and hashed mess
		list($cid, $ckey) = explode(':', $cookie);

		// Fetch the user based on the id in the cookie
		$user = $userModel->fetchUserById($cid);

		if($user === 0) {
		    return;
		}

		// Compare the auth keys
		if($ckey != $user['authkey']) {
		    return null;
		} else {
			$this->_authenticated = true;
			$this->_initialized = false;
			return $user;
		}

 
	}
	
	public function buildUnreadList($since) {
		// Build a "new since last visit" list
		$forumModel = new Model_Forum();
		$newThreads = $forumModel->fetchThreadsSinceDate($since);
		$unreadThreadList = array();
		foreach($newThreads as $thread) {
			$unreadThreadList[$thread['board']][$thread['id']] = 1;				
		}
		
		$this->unreadThreadsList = $unreadThreadList;
	}
	
	public function threadIsUnread($boardid, $threadid) {
		$threads = $this->unreadThreadsList;
		
		if(is_array($threads) && array_key_exists($boardid,$threads) && array_key_exists($threadid, $threads[$boardid])) {
			return true;	
		}
		return false;
	}
	
	public function removeThreadFromUnreadList($boardid, $threadid) {
		$threads = $this->unreadThreadsList;
		
		if(is_array($threads) && array_key_exists($boardid,$threads) && array_key_exists($threadid, $threads[$boardid])) {
			unset($threads[$boardid][$threadid]);
			$this->unreadThreadsList = $threads;	
		}
	}
	
	public function boardIsUnread($boardid) {
		$threads = $this->unreadThreadsList;
		if(is_array($threads) && isset($threads[$boardid])) {
			return true;
		}
		return false;
	}
	
	public function removeBoardFromUnreadList($boardid) {
		$threads = $this->unreadThreadsList;
		
		if(is_array($threads) && array_key_exists($boardid, $threads) && empty($threads[$boardid])) {
			unset($threads[$boardid]);
			$this->unreadThreadsList = $threads;
		}
		
		
	}
	
	public function isLoggedIn() {
	    // We are logged in if these 
	    // variables are set
	    if($this->_authenticated) {
			return true;
		}
    
	    return false;
	}
	
	public function updateLastSeen() {

		$this->_model->updateLastSeen($this->username);
		$this->lastSeen = time();
	}
		
	public function clear() {
		$this->_data->clear();
		unset($this->_unreadThreads);
	}
	
	public function __set($name, $value) {
		$this->_data->$name = $value;
	}
	
	public function __isset($name) {
		return isset($this->_data->$name);
	}
	
	public function __get($name) {
		if(isset($this->_data->$name)) {
			return $this->_data->$name;
		}
		
        return null;
	}
	
	public static function getInstance() {
		 if(!is_object(self::$_instance)) {
			self::$_instance = new CurrentUser;
		}
		
		return self::$_instance;
	}
	
	private function __construct() {
		$this->_data = new Session('current_user');
		$this->_model = new Model_User();
	}

};
