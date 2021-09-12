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
 * Simple class for interacting with 
 * MySQL. This is a singleton class.
 * This class will automatically connect
 * to MySQL when query is fist called.
 */ 
class MySQL {
    private static $_instance;
    private $_queryCounter;
    private $_config;
    private $_mysqlConnection;
    private $_errorTicket;
    private $_errorLog;

    /**
     * Opens a new MySQLi connection based on
     * the settings in Config.php
     */ 
    public function openConnection() {
        if(!isset($this->_config)) {
        echo 'Warning: Database config not set. Cannot open a new connection.';
        return;
        }

        $this->_mysqlConnection = new mysqli($this->_config['hostname'], 
        $this->_config['username'],
        $this->_config['password'],
        $this->_config['database']);

        if(mysqli_connect_errno()) {
        throw new Exception(mysqli_connect_error(), mysqli_connect_errno());
        }
    }

    /**
     * Closes the MySQL connnection
     */
    public function closeConnection() {
    	if($this->_mysqlConnection) {
    		$this->_mysqlConnection->close();
    		$this->_mysqlConnection = null;
    	}
    }

    /**
     * Sets the configuration information for connecting/accessing a MySQL database
     * \param $config An associative array of values used for connecting to mysql. The array keys are: hostname, username, password, database and tableprefix
     */
    public function setDbConfig($config) {
    	$this->_config = $config;
    }

    /**
     * Returns the MySQL connection object
     * \return mysqli object
     */
    protected function getConnection() {
        return $this->_mysqlConnection;
    }

    /**
     * Escapes the given string
     * \param $string The string to escape
     * \return The escaped string
     */
    public function escape($string) {
        return $this->_mysqlConnection->real_escape_string($string);
    }

    /**
     * Get the ID of the last inserted row
     * \return The ID of the serted row of the last
     */
    public function getInsertId() {
    	return $this->_mysqlConnection->insert_id;
    }

    /**
     * Sends a query to MySQL
     * This method automatically replaces {prefix} in the query with 
     * the custom table prefix
     * \param $query The query to run against the selected database
     * \return Returns a mysqli_result object
     */
    public function query($query) {
        
        $this->incrementQueryCounter();
        
        $query = str_replace('{prefix}', $this->getTablePrefix(), $query);

        //or die('There was a MySQL Error: '.$this->_mysqlConnection->error.' with the query: '.$query)
        $result = $this->_mysqlConnection->query($query);
        
        if(!$result) {
        	$ticket = time();
            if(DEBUG) {
                die($this->_mysqlConnection->error);
            } else {
                $this->_errorLog->writeLine($ticket.':'.$this->_mysqlConnection->error);
                die('There was a database error. Inform the site admin with the ticket number '.$ticket);
            }
        }
                
        return $result;
    }

    /**
     * This method escapes and quotes the $var
     * into $string where the question mark is
     * \param $string The string that gets returned
     * \param $var The value that gets quoted and inserted where '?' appears
     */
    public function quoteInto($string, $var) {
        $replace = "'".$this->escape($var)."'";
        
        return str_replace('?', $replace, $string);
    }

    /**
     * Returns the instance of the class.
     * \return MySQL object
     */
    public static function getInstance() {
        if(!is_object(self::$_instance)) {
            self::$_instance = new MySQL();
        }
        
        return self::$_instance;
    }

    /**
     * \return The number of queries
     */
    public function getQueryCount() {
        return $this->_queryCounter;
    }

    /**
     * \return The table prefix set by the user during installation
     */
    public function getTablePrefix() {
        return $this->_config['tableprefix'];
    }

    /**
     * Increments query count
     */
    protected function incrementQueryCounter() {
       $this->_queryCounter++;
    }

    private function __construct() { 
    	$this->_queryCounter = 0;
    	$this->_errorLog = new Log(ROOT_PATH.'/Logs/mysqllog.txt');
    }    
};

