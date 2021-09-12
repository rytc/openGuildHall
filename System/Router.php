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
 * \page how_controllers_work How Controllers and Actions work
 * openGuildHall follows the Model-View-Controller paradigm in it's code organization.
 * In some frameworks, controllers are organized by class, and actions performed by those 
 * controllers are contained within methods of that controller. The structure of this code
 * is a little different. First we will look at how the URI translates into the controller
 * and action, then look into how they are organized
 *
 * As with most MVC frameworks, the URI is setup in two parts. The first part specifies the controller
 * and the second part specifies the Action. For example: if we wanted the category of the forum, the address
 * would be forum/category/1. The 'forum' is the controller, 'category' is the action and the '1' is an additional
 * parameter passed to the action to specify which category to display.
 *
 * Unlike most MVC frameworks, there is no controller class. Controllers are only represented by directories, in the URI,
 * and in the Action's names. For example: the class for the 'category' action for the 'forum' controller would be named
 * Forum_Category. This makes it easy for the code to find the class by knowing the controller and the action.
 *
 * As was said before, controllers are not in code but rather directories and names. Actions are located in directories named
 * after their controller. For example: the 'category' action for the 'forum' controller would be located in Controllers/Forum/Category.php.
 * As you can see, the location of the action and the name of the action's class is derrived from the action's name and the controller's name.
 *
 * \see Autoloader
 * \note This documentation is located in Router.php
 */

/**
 * The Router is responsible for loading the action specified in the URL or in
 * the rewrite rules. 
 * \ingroup System
 */
class Router {
    
	/**
	 * The default controller to load if none is specificed
	 * \var string
	 */
    private $_defaultController = 'Index';
	
	/**
	 * The default action to run if none is specified
	 * \var string
	 */
    private $_defaultAction = 'Index';
	
	/**
	 * The path to the controllers.
	 * \var string
	 */
	private $_controllerDir = 'Controllers/';
	
	/**
	 * An array of rewrite ruls
	 * \var array of strings
	 */
    private $_rewriteRules;
	
	/**
	 * The controller that has been loaded
	 * \var string
	 */
    protected $_controller;
	
	/**
	 * The action that has been loaded
	 * \var string
	 */
    protected $_action;
    
	/**
	 * Singleton instance
	 * \var Router
	 */
    private static $_instance;
    
	/**
	 * This method handles the request, figuring out which action should be loaded.
	 * \throw Exception
	 * -1 if the Action cannot be found 
	 * -2 if the file cannot be found.
	 * \return Either the appropriate Action class or false on failure
	 */
    public function handleRequest() {
        $originalURI = $this->getRequestURI();
        $uri = null;
		
        // Remove any $_GET variables from our request
        if($chunks = explode('?', $originalURI)) {
            $originalURI = $chunks[0];
        }
		
		// If the SITE_PATH is something other than /
		// And it precedes the URI
		// Remove it
		if(strlen(SITE_PATH) > 1 && substr($originalURI, 0, strlen(SITE_PATH)) == SITE_PATH) {
			$originalURI = substr($originalURI, strlen(SITE_PATH)-1, strlen($originalURI));
		}
        
		// Just incase the user hasn't set up modrewrite
		// If index.php precedes the URI
		// Remove it
		if(substr($originalURI, 0, 8) == "index.php") {
			$originalURI = substr($originalURI, 8, strlen($originalURI));
		}
		
        // If we have special rewrite rules we want to use
        if(is_array($this->_rewriteRules) && !empty($this->_rewriteRules)) {
            foreach($this->_rewriteRules as $match => $route) {
				
                // Create a regex based on the "match" portion
                // of the array
                $regex = '#^'.str_replace('*', '[a-zA-Z0-9-_]*$', $match).'#';
                if(preg_match($regex, $originalURI)) {
                    $uri = explode('/', $route);
					
                    array_shift($uri);
        
                    // We need to extract the parameters
                    // from our url
                    $request = explode('/', $originalURI);
                    $match = explode('/', $match);
                    
                    // Remove any parts that are not a wildcard in the "match"
					// from our originalURL
                    foreach($match as $key => $value) {
                        if($value != '*') {
                            array_shift($request);
                        }
                    }
					
					// Prepend  the actual route to the beginning
					// of our uri
					foreach($request as $param) {
						array_push($uri, $param);
					}
        
                }
            }
        }
        
        if(empty($uri)) {
            $uri = explode('/', $originalURI);
            // The first element in the array is always empty
            if(is_array($uri)) {
                array_shift($uri);
            }
        } 
        
		// If there is no URI set, load the default controller & action
        if(empty($uri[0])) {
            // The default route
			
			$this->loadAction($this->_defaultController, $this->_defaultAction);
			
			// class Controller_Action 
            $classname = $this->_defaultController.'_'.$this->_defaultAction;
        
		// If only the controller is specified, load the default action for that controller
        } elseif(empty($uri[1])) {

            // Capitalize the first letter
            $uri[0] = ucwords($uri[0]);
        
			$action = $this->_defaultAction;
        
            $this->_controller = $uri[0];
            $this->_action = $action;
        
			$this->loadAction($this->_controller, $this->_action);
			
            $classname = $this->_controller.'_'.$this->_action;
        
            // Pop it off the array
            array_shift($uri);
        
		// All other cases where controller, action, and params are specfied
        } else {
        
            // Capitalize the first letter
            $uri[0] = ucwords($uri[0]);
            $uri[1] = ucwords($uri[1]);
        
            // We want to support .rss and other file types
            // the action will just be called something like
            // 
            // /controller/action.rss = Controller_Action_RSS
            $action = explode('.', $uri[1]);
        
			// If there is a .whatever proceding the action
            if(isset($action[1])) {
                $action = $action[0].strtoupper($action[1]);
            } else {
                $action = $uri[1];
            }
        
            $this->_controller = $uri[0];
            $this->_action = $action;
        
			$this->loadAction($this->_controller, $this->_action);
		
            $classname = $uri[0].'_'.$action;
        
            // Pop it off the array
            array_shift($uri);
            array_shift($uri);
        }
        
        
        // The rest of the items in the uri array
		// are parameters we want to pass to the action
        foreach($uri as &$param) {
            $param = str_replace(' ', '-', $param);
            $param = strip_tags($param);
        }
		
        // We want the params to begin at 0
        if(empty($uri[0])) {
            array_shift($uri);
        }
        
		// We already required the script
        if(class_exists($classname)) {
            if(!empty($uri)) {
                // We pass the remaining parts of the URI
                // for use by the page
                $class = new $classname($uri);
            } else {
                $class = new $classname();
            }
            return $class;
        } else {
            throw new Exception($classname.' cannot be found.', -1);
        }
    }
	
	/**
	 * Adds a rule to the list of rewrite rules
	 * \param @match The url to match
	 * \param @route The url to treat the request as
	 * \return Route Returns $this to allow for stacking method calls
	 */
	public function addRewriteRule($match, $route) {
		$this->_rewriteRules[$match] = $route;
		return $this;
	}
    
	/**
	 * Returns which controller has been loaded.
	 * Should only be called after handleRequest()
	 * \return string The name of the controller
	 */
    public function getController() {
        return $this->_controller;
    }
    
	/**
	 * Returns which action has been loaded
	 * Should only be called after handleRequest()
	 * \return The name of the action
	 */
    public function getAction() {
        return $this->_action;
    }
    
	/**
	 * Loads the specified Action for the Controller
	 * \param $controller string Name of the controller
	 * \param $action string Name of the action
	 * \throw exception -2 if it can't find the file
	 */
	protected function loadAction($controller, $action) {
		// ROOT_PATH/Controller/Action.php
		$url = $this->_controllerDir.$controller.'/'.$action.'.php';
		
		if(!file_exists($url)) {
			throw new Exception('File '.$url.' not found by the router.', -2);	
		} else {
			require $url;
		}
	}
	
    /* Just in case the server doesn't support this, we can
    * implement functionality here
    * Only Apache on Linux supports $_SERVER['REQUEST_URI']
    */
    public function getRequestURI() {
        return $_SERVER['REQUEST_URI'];
    }
    
	/**
	 * Singleton
	 * \return Router
	 */
    public static function getInstance() {
    
        if(!is_object(self::$_instance)) {
            self::$_instance = new Router();
        }
    
        return self::$_instance;
    }
	
	    
    // Prevent creation of a new class
    private function __construct() { }
    
}
