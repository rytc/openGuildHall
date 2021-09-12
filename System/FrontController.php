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
 * The FrontController is responsible for
 * calling the methods in the action loaded
 * by the Router.
 *
 * \see Router
 */
class FrontController {

	/**
	 * \var Router
	 */
    protected $_router;
    
	/**
	 * \var array of strings
	 */
	protected $_plugins;
	
	/**
	 * Singleton instance
	 * \var Router
	 */
    private static $_instance;
	
	/**
	 * This method starts the system,
	 * causes the action to be loaded,
	 * and to run the correct method in
	 * the action.
	 */
    public function dispatchRequest() {
		
        try {
            $action = $this->_router->handleRequest();
		} catch(Exception $e) {
			die('Error '.$e->getCode().': '.$e->getMessage());
        }
		
		if(!defined('INSTALLED') && $this->_router->getController() != 'Install') {
			die('You must run the installer before you can use openGuildHall. <a href="install/">Install openGuildHall</a>');
		}

		// Preform action initialization.
		// This is called for all action methods
		try {
			if(!$action->init()) {
				//$action->displayMessage('Failure', 'Action initialization failed.');
				return;
			}
		} catch(Exception $e) {
			// We use exceptions here to stop the front controller
			// from moving on if we have a problem, and is a good
			// mechanism for passing an error message
			$action->displayMessage('Error', $e->getMessage);
			return;
		}
			
		// If want to run some code such as
		// checking user permissions, we will
		// do that in a plugin.
		if($action->pluginsEnabled()) {
			foreach($this->_plugins as $plugin) {
				call_user_func($plugin.'::pre', $action);
			}
		}
		
		// If it's an ajax request, call the ajax method
		// otherwise call the normal action
		if($this->isAjaxRequest()) {
			$action->ajax();
		} else {
			$action->action();
		} 

		if($action->pluginsEnabled()) {
			foreach($this->_plugins as $plugin) {
				call_user_func($plugin.'::post', $action);
			}
		}
        
    }
	
	/**
	 * Returns true if the request is from AJAX
	 * \return bool 
	 */
    public function isAjaxRequest() {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
    }
	
	/**
	 * Adds a plugin to the list
	 * \return FrontController Returns $this to allow stacking method calls.
	 */
	public function addPlugin($name) {
		$this->_plugins[] = $name;
		return $this;
	}
    
	/**
	 * Singleton
	 * \return FrontController
	 */
    public static function getInstance() {
        if(!is_object(self::$_instance)) {
            self::$_instance = new FrontController();
        }
        
        return self::$_instance;
    }
	
	private function __construct() { 
        $this->_router = Router::getInstance();
		$this->_plugins = array();
		$this->_pluginsEnabled = true;
    }
	
}

