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
 
class Model_PrivateMessages {
	private $_table;
	
	public function fetchMessages($userId, $page, $limit) {
		$table = $this->getTable();
		$limit = (($page - 1) * PMS_PER_PAGE).', '. $limit;
		
		$where = $table->getMySQL()->quoteInto('receiver = ?', $userId);
		$result = $table->select(array(), $where, null, $limit);
		
		return $result;
	}
	
	public function fetchSavedMessages($userId, $page, $limit) {
		$table = $this->getTable();
		$limit = (($page - 1) * PMS_PER_PAGE).', '. $limit;
		
		$where = $table->getMySQL()->quoteInto('author = ?', $userId);
		$where .= $table->getMySQL()->quoteInto(' AND saved = ?', 1);
		$result = $table->select(array(), $where, null, $limit);
		
		return $result;
	}
	
	public function fetchSentMessages($userId, $page, $limit) {
		$table = $this->getTable();
		$limit = (($page - 1) * PMS_PER_PAGE).', '. $limit;
		
		$where = $table->getMySQL()->quoteInto('author = ?', $userId);
		$result = $table->select(array(), $where, null, $limit);
		
		return $result;
	}
	
	public function fetchMessage($messageId) {
		$table = $this->getTable();
		
		$where = $table->getMySQL()->quoteInto('id = ?', $messageId);
		$result = $table->select(array(), $where, null, 1);
		
		return $result;
	}
	
	public function insertMessage($subject, $author, $receiver, $copy, $draft, $disableSmileys, $disableBBcode) {
		$table = $this->getTable();
		
		$data['author'] = $author;
		$data['receiver'] = $receiver;
		$data['subject'] = $subject;
		$data['copy'] = $copy;
		$data['saved'] = $draft;
		$data['date'] = time();
		$data['disable_smileys'] = $disableSmileys;
		$data['disable_bbcode'] = $disableBBcode;
		
		$table->insert($data);
	}
	
	public function deleteMessage($messageId) {
		$table = $this->getTable();
		
		$table->deleteId($messageId);
	}
	
	// returns an array with three keys: read, unread, total
	public function fetchMessageCount($userId) {
		$result = array();
		$table = $this->getTable();

		$whereRead = $table->getMySQL()->quoteInto('receiver = ? AND viewed = 1', $userId);
		$whereUnread = $table->getMySQL()->quoteInto('receiver = ? AND viewed = 0', $userId);
		
		$resultRead = $table->count($whereRead);
		$resultUnread = $table->count($whereUnread);

		$result['read'] = $resultRead[0];
		$result['unread'] = $resultUnread[0];
		$result['total'] = $resultRead[0] + $resultUnread[0];
		
		return $result;
	}
	
	public function markMessageAsRead($messageId) {
		$table = $this->getTable();
		
		$where = $table->getMySQL()->quoteInto('id = ?', $messageId);
		$table->updateCell('viewed', 1, $where);
	}
	
	protected function getTable() {
		if(!isset($this->_table)) {
			$this->_table = new System_MySQLTable('r_privatemessages');
		}
		
		return $this->_table;
	}
	
};
