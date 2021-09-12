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
 * Base class for all controller actions.
 * \ingroup System
 */

abstract class Action {
    
    private $_params;
    private $_paramMap;
    private $_postVars;
    private $_getVars;
    protected $view;
    
	/**
	 * Flag for enabling plugins
	 */
	protected $_pluginsEnabled;
	
	/**
	 * This method is called before any other method in the action.
	 * \return Returns true on success, and false on failure
	 */
	abstract public function init();
	
	/**
	 * This method is where the buisness logic is located
	 */
	abstract public function action();
	
	/**
	 * This method is called for AJAX requests to this action.
	 * All AJAX requests get sent to this method instead of to the action() method.
   * Any output from this method get's returned to the AJAX call. 
	 */
	public function ajax() { }
	
	/**
	 * Constructor
	 * 
	 */
    public function __construct($params = null) {
        
        if(!empty($_POST)) {
            $this->_postVars = $_POST;
        }
        
        if(!empty($_GET)) {
            $this->_getVars = $_GET;
        }
        
    		$this->defaultAccess = false;
    		$this->_pluginsEnabled = true;
        $this->_params = $params;
        $this->_paramMap = null;
        $this->view = new View(SITE_THEME);
    		$this->limitedAction = false;
    }
	
    /**
     * This method is to create a map of English words to params passed to 
     * the action via the URL.
     *
     * To do this, we pass atleast one array to mapParams
     * The first array simply assigns a name to the param's position. 
     *
     * Example:
     * 0 => 'username'
     * 1 => 'page'
     *
     * Then, if the $defaults array is set, we set the params to 
     * their defaults if they haven't already been set. 
     * Defaults are set based on their english word
     *
     * Example:
     * 'username' => 'none'
     * 'page' => 0
     *
     * \param $paramMap An array mapping strings to IDs
     * \param $defaults An array to attach default values to the params
     */
    protected function mapParams(array $paramMap, array $defaults = array()) {
       
       // If we have no params then we don't have to do anything
       if($this->_params == null) {
           return;
       }
       
       // Loop through the parameters in order
       foreach($this->_params as $position => $value) {
           // We get the name of the parameter we set
           // in our param map based on the position we set.
           // Set the name as the key, and the value as the value
           $this->_paramMap[$paramMap[$position]] = $value;
       }
       
       // If we have defaults set
       if(!empty($defaults)) {
           // For each default
           foreach($defaults as $key => $value) {
               // Check to see if it has already been set
               if(!isset($this->_paramMap[$key])) {
                   $this->_paramMap[$key] = value;
               }
           }
       }
       
    }
    
	/**
	 * Returns the specified param.
	 * \param param Returns a parameter passed to the action via the URL. The
   * param is accessed either through number identified by the order in the URL.
   * if the params were mapped by mapParams() then access that params through the
   * english name.
	 */
  protected function getParam($param) {
      // If we don't setup a param map
      // we can still accesss the parameters
      // through their position in the array
      if($this->_paramMap == null) {
          if(isset($this->_params[$param]))
              return $this->_params[$param];
          else
              return null;
      } else {
          if(isset($this->_paramMap[$param]))
              return $this->_paramMap[$param];
          else
              return null;
      }
  }
    
  /**
   * Plugin enable flag
   * \return True or false depending if plugins are enabled for this action
   */
  public function pluginsEnabled() {
  	return $this->_pluginsEnabled;
  }

	/**
   * HTTP POST flag
	 * \return bool Returns true if the request is POST
	 */
  public function isPost() {
      return (!empty($_POST));
  }
	
  /**
   * \return Returns all of the params in an array.
   */
  protected function getAllParams() {
      if($this->_paramMap == null) {
          return $this->_params;
      } else {
          return $this->_paramMap;
      }
  }
    
	/**
	 * \return POST variables
	 */
  protected function getPostVariables() {
      return $this->_postVars;
  }
    
	/**
	 * \return GET variables
	 */
  protected function getGetVariables() {
      return $this->_getVars;
  }
    
	/**
	 * \return The action's View object
	 */
  public function getView() {
      return $this->view;
  }
    
    
};
