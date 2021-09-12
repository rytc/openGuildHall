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
 
class Forum_Index extends Action {
	protected $_model;
	protected $_userModel;
	protected $_acl;
	public function init() {
		Acl::getInstance()->setPageAccessRule(ALLOW);

		return true;
	}

	public function action() {
		$this->_model = new Model_Forum();
		$this->_acl = Acl::getInstance();

		$categories = $this->_model->fetchCategories();
		$boards = array();
		


		for($i = 0; $i < count($categories); $i++) {
			$category = $categories[$i];
			//if($this->_acl->hasAccess('Forum', 'Category', 'View', $category['id']) == DENY) {
			//	unset($categories[$i]);
			//} else {
				$bs = $this->_model->fetchBoardsByCategory($category['id']);
				if($bs) {
					foreach($bs as $b) {
						//if($this->_acl->hasAccess('Forum', 'Board', 'View', $b['id'])) {
							$b['last_post'] = $this->_model->fetchLastPostInBoard($b['id']);
							CurrentUser::getInstance()->removeBoardFromUnreadList($b['id']);
							$boards[$category['id']][] = $b;		
						//}
					}
				}
			//}
		}
		
		$this->view->assign('pagetitle', 'Forum');
		$this->view->assign('categories', $categories);
		$this->view->assign('boards', $boards);
		$this->view->display('forum_index.tpl');

	}

	// Recursive function to fetch the boards and subboards in
	// a category
	/*public function fetchBoards($category, $parent = null) {

		// If we are fetching subboards
		if($parent != null) {
			$boards = $this->_model->fetchBoardsWithParent($parent);
			
			// If there are no sub boards, then move on
			if(empty($boards)) {
				return false;				
			} else {
				// Check access
				for($x = 0; $x < count($boards); $x++) {
					if($this->_acl->hasAccess('Forum', 'Board', 'View', $boards[$x]['id']) == DENY) {
						unset($boards[$x]);
					}
				}
			}
				
			// We shouldn't have more than one layer of subboards
			return $boards;
		
		// Else we are fetching top level boards
		} else {
			// Fetch the boards in the specified category
			$boards = $this->_model->fetchBoardsByCategory($category);
			for($i = 0; $i < count($boards); $i++) {
				// Check if we have access to them
				if($this->_acl->hasAccess('Forum', 'Board', 'View', $boards[$i]['id']) == DENY) {
					unset($boards[$i]);
				} else {
					//$boards[$i]['post_count'] = 0;
					//$boards[$i]['thread_count'] = $this->_model->fetchThreadCount($boards[$i]['id']);
					// Only need to do this if the board is populated
					if($boards[$i]['thread_count'] > 0) {
						//$boards[$i]['post_count'] = $this->_model->fetchReplyCountForBoard($boards[$i]['id']);
						$boards[$i]['last_thread'] = $this->_model->fetchLastThread($boards[$i]['id']);
						$lastpost = $this->_model->fetchLastPost($boards[$i]['last_thread']['id']);
						$boards[$i]['last_post_user'] = $this->_userModel->fetchUserById($lastpost['author_id']);
					}

					// Try to fetch subboards
					$sub = $this->fetchBoards($category, $boards[$i]['id']);

					if($sub != false) {
						// Add the boards to our list
						foreach($sub as $s) {
							$boards[$i]['subboards'][] = $s;
						}
					}
				}
			}
			
			// Returns all the boards and subboards in the category
			return $boards;	
		}
	}*/
	
}
