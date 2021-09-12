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
 
class Model_Quotes {
    private $_quotesTable;
    
    public function fetchRandomQuote() {
        $table = $this->getQuotesTable();
        
        // Get the max number of quotes
        // NOTE: This code will kill the server if there are
        // around 10,000 entries or more. We most likely won't
        // have that many quotes.
        $quoteQuery = 'SELECT * FROM r_quotes ORDER BY RAND() LIMIT 1';
        
        $result = $table->query($quoteQuery);
        return $result->fetch_assoc();
        
    }
    
    protected function getQuotesTable() {
        if(!isset($this->_quotesTable)) {
            $this->_quotesTable = new MySQLTable('r_quotes');
        }
        
        return $this->_quotesTable;
    }
}