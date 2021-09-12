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
 * This class abstracts the concept of tables
 * in MySQL. Operations are performed on tables
 * not the database connection, after all. This
 * is mainly a way to abstract the most common
 * MySQL functions to increase readability and
 * reduce code duplication.
 *
 * Complex queries can still be performed
 * through the System_MySQL::query() method
 */
class MySQLTable {
    private $_tableName;
    private $_mysql;

	/** 
	 * Constructor
	 * @param string $tableName The name of the table in the database
	 */
    public function __construct($tableName) {
        if(empty($tableName)) {
            throw new Exception('Table name not set');
        }
        
        $this->_tableName = $tableName;
        $this->_mysql = MySQL::getInstance();
    }
    
	/**
	 * Peform a straight up query
	 * @param string $query The query to run
	 */
    public function query($query) {
		//echo $query."<br />";
        return $this->_mysql->query($query);
    }

    /*
     * Select row(s) from the table.
	 * @param string $what An array of columns to fetch
	 * @param string $where Where clause for the query
	 * @param string $order Order clause for the query
	 * @param integer $limit Max number of rows to return
     */
    public function select(array $what, $where = null, $order = null, $limit = null) {
        $query = 'SELECT ';
        
        // If we have an array list of columns we want to select
        // build the query like that
        if(is_array($what) && !empty($what)) {
            for($c = 0; $c <= count($what) - 1; $c++) {
                // If it is the first item in the array
                // don't add a comma, else do add a comma
                if($c == 0) {
                    $query .= $this->getMySQL()->escape($what[$c]);
                } else {
                    $query .= ','.$this->getMySQL()->escape($what[$c]);
                }
            }
        } else {
            $query .= '*';
        }
        
        $query .= ' FROM '.$this->_tableName;
        
        if($where != null) {
            $query .= ' WHERE '.$where;
        }

        if($order != null) {
            $query .= ' ORDER BY '.$order;
        }
        
        if($limit != null) {
           $query .= ' LIMIT '.$limit;
        }
        
        
        //echo $query."<br />";
        
       	$queryResults = $this->_mysql->query($query);

       	if(!$queryResults) {
       		return false;
       	}
       	
        // If we want to get an associative
        // array of each row, we have to
        // loop through each row and fetch_assoc
        // on it.
        if($limit == 1) {
            return $queryResults->fetch_assoc();
        }
        
        $result = array();
        while($row = $queryResults->fetch_assoc()) {
            $result[] = $row;
        }
        
        foreach($result as $row2) {
            foreach($row2 as &$column) {
                stripslashes($column);
            }
        }
        
        return $result;

        
        
    }
    
    /**
     * Update a row in the database
	 * @param string $what An associative array of what to update. column=>data
	 * @param string $where Clause for where to update the row
	 * @param integer $limit Number of rows to update
     */
    public function update(array $what, $where, $limit = null) {
        $query = 'UPDATE '.$this->_tableName.' SET ';
        
        if(empty($what)) {
            throw new Exception('Expecting array for "what" in System_MySQLTable::update');
        }
        
        $lastkey = end(array_keys($what));
        foreach($what as $column => $value) {
            $query .= '`'.$column.'`';
            $query .= "='".$this->getMySQL()->escape($value)."'";
            
            if($column == $lastkey) {
                break;
            } else {
                $query .= ', ';
            }
        }
        
        $query .= ' WHERE '.$where;        
        
        if($limit != null) {
            $query .= ' LIMIT '.$limit;
        }
        
        $result = $this->_mysql->query($query);
        return $result;
    }
    
	/**
	 * Update a row with the specific ID
	 * @param string $what And associative array of what columns to update. column=>data
	 * @param integer $id The id of the row to update
	 */
	public function updateId(array $what, $id) {
		
		$where = $this->getMySQL()->quoteInto('id = ?', $id);
		$result = $this->update($what, $where);
		
		return $result;
	}
	
	/**
	 * Increment an integer row
	 * @param string $key Which column to increment
	 * @param string $where Where clause for which row to update
	 */
	public function incrementCell($key, $where) {
		$query = 'UPDATE '.$this->_tableName.' SET '.$key.'='.$key.'+1 WHERE '.$where;

		$result = $this->_mysql->query($query);

		return $result;
	}

	/**
	 * Decrement an integer row
	 * @param string $key Which column to increment
	 * @param string $where Where clause for which row to update
	 */
	public function decrementCell($key, $where) {
		$query = 'UPDATE '.$this->_tableName.' SET '.$key.'='.$key.'-1 WHERE '.$where;
		$result = $this->_mysql->query($query);
		return $result;
	}

	/**
	 * Update a single cell
	 * @param string $key The column to change
	 * @param string $value The value to change in the row
	 * @param string $where Where clause for which row to update 
	 */
	public function updateCell($key, $value, $where) {
		$data[$key] = $value;
		return $this->update($data, $where);
	}

	/**
	 * Instert data into the table
	 * @param string $what And associative array of columns and data. column=>data
	 */
    public function insert(array $what) {
        $query = 'INSERT INTO '.$this->_tableName.' ';
        
        $part1 = '(';
        $part2 = '(';
        $lastkey = end(array_keys($what));
        foreach($what as $column => $value) {
            $part1 .= "`".$column."`";
            $part2 .= "'".$this->getMySQL()->escape($value)."'";
            
            if($column == $lastkey) {
                break;
            } else {
                $part1 .= ',';
                $part2 .= ',';
            }
        }
        $part1 .= ')';
        $part2 .= ')';
        
        $query .= $part1 . ' VALUES ' . $part2;
        
        return $this->_mysql->query($query);
        
    }

	/**
	 * Delete a single row in the table
	 * @param integer $id The id of the row to delete
	 */
	public function deleteId($id) {
		$query = 'DELETE FROM '.$this->_tableName." WHERE id='".$this->_mysql->escape($id)."'";
		
		return $this->_mysql->query($query);
	}
	
	/**
	 * Delete all the rows that meet the $where condition
	 * @param string $where Where clause for which row to update
	 */
	public function deleteAllWhere($where) {
		$query = 'DELETE FROM '.$this->_tableName.' WHERE '.$where;
		
		return $this->_mysql->query($query);
	}

	/**
	 * Get the id of the last inserted row
	 */
	public function getInsertId() {
		return $this->_mysql->getInsertId();
	}

	/**
	 * Count the number of rows meeting the $where condition
	 * @param string $where Where clause for which row to update
	 */
	public function count($where) {
		$query = 'SELECT COUNT(*) FROM '.$this->_tableName.' WHERE '.$where;
		//echo $query;
		$result = $this->_mysql->query($query);
		
		if(!$result) {
			return false;
		}
		
		return $result->fetch_array(MYSQLI_NUM);
	}
	
	public function getMySQL() {
		return $this->_mysql;
	}

};
