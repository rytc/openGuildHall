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
 * A model for manipulating forum posts
 */
class Model_Post extends Model {
	
	public function __construct($id = NULL) {
		$this->_tableName = 'forum_posts';

		if($id && Validator::isInt($id)) {
			$table = $this->getTable();

			$where = $table->getMySQL()->insertInto('id = ?', $id);
			$result = $table->select(array(), $where);
			
			$this->_new = false;
			foreach($result as $key => $value) {
				$this->_data[$key] = $value;
			}
		}
	}

	public function save() {
		if(empty($this->_exceptions)) {
			return false;
		}

		$table = $this->getTable();

		if($this->_new) {
			
			// If there is no thread id, 
			// then we assume that we are
			// creating a new thread
			if(!array_key_exists('thread_id', $this->_data)) {
				$thread = new Model_Thread();
				$thread->title = $post->title;
				$thread->author_id = CurrentUser::getInstance()->id;
				$thread->board_id = $this->_boardId;
				$thread->original_date = time();
				$thread->date = $thread->original_date;
				$thread->sticky = $sticky;

				$result = $thread->save();
				if(!$result) {
					return false;
				} else {
					$this->thread_id = $result;
				}
			}
			
			$result = $table->insert($this->_data);
		} else {
			$where = $table->getMySQL()->insertInto('id = ?', $this->id);
			$result = $table->update($this->_data, $where);
		}

		return true;
	}

	protected function setId($id) {
		throw new Exception('ID is immutable');
	}

	protected function setTitle($title) {
		if(!Validator::checkStringLength($title, 4, 60)) {
			throw new Exception('Invalid title');
		}

		return $title;
	}

	protected function setCopy($copy) {
		if(!Validator::checkStringLength($title, 12, 1024)) {
			throw new Exception('Invalid post length');
		}

		return $copy;
	}

	protected function setDate($timestamp) {
		if(!Validator::isInt($timestamp)) {
			throw new Exception('Invalid timestamp. Please inform the admin');
		}

		return $timestamp;
	}

	protected function setDisable_smileys($smileys) {
		if(!$smileys) {
			return 0;
		} else {
			return 1;
		}
	}

	protected function setDisable_bbcode($bbcode) {
		if(!$bbcode) {
			return 0;
		} else {
			return 1;
		}
	}

	protected function setAuthor_id($id) {
		if(!Validator::isInt($id)) {
			throw new Exception('Invalid author ID');
		}

		return $id;
	}

	protected function setThread_id($id) {
		if(!Validator::isInt($id)) {
			throw new Exception('Invalid thread ID');
		}

		return $id;
	}

};
