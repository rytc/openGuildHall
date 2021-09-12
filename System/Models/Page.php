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
 
/**
 * Model for site pages
 */
class Model_Page {
    private $_pageTable;
    
    /**
     * Fetches a page by it's title
     * \param $title The title of the page to fetch
     * \return Returns an associative array of page data
     */
    public function fetchPageByTitle($title) {
        $table = $this->getPageTable();
        
        $where = $table->getMySQL()->quoteInto('title = ?', $title);
        $result = $table->select(array(), $where, null, 1);
        
        return $result;
    }
    
    /**
     * Inserts a new page into the database
     * \param $title The title of the page
     * \param $body The body text of the page
     */
	public function insertPage($title, $body) {
		$table = $this->getPageTable();
		
		$data = $this->_prepare($title, $body);
		$result = $table->insert($data);
			
		return $result;
	}
	
	/**
	 * Updates a page by it's ID
	 * \param $title The new title of the page
	 * \param $body The new body text of the page
	 * \param $id The ID of the page to delete
	 */
    public function updatePage($title, $body, $id) {
        $table = $this->getPageTable();
        
		$data = $this->_prepare($title, $body);
        $where = $table->getMySQL()->quoteInto('id = ?', $id);
		
        $result = $table->update($data, $where);
        
        return $result;
    }
    
	protected function _prepare($title, $body) {
		$data['title'] = $title;
		$data['body'] = $body;
		$data['last_update'] = time();
		
		return $data;
	}
	
    protected function getPageTable() {
        if(!isset($this->_pageTable)) {
            $this->_pageTable = new MySQLTable(MySQL::getInstance()->getTablePrefix().'pages');
        }
        
        return $this->_pageTable;
    }
}
