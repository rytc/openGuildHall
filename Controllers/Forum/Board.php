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
 
class Forum_Board extends Action {
	protected $_boardId;
	protected $_page;
	protected $_acl;

	public function init() {
		$this->_acl = Acl::getInstance();
		
		$boardurl = preg_split('/-/', $this->getParam(0));
		$this->_boardId = end($boardurl);

		if(!Validator::isInt($this->_boardId)) {
			$this->view->displayMessage('Invalid Board', 'Invalid board URL');
			return false;
		}

		$this->_page = $this->getParam(1);
		if($this->_page != null || !Validator::isInt($this->_page)) {
			$this->_page = 1;
		}

		//if($this->_acl->hasAccess('Forum', 'Board', 'View', $this->_boardId) == ALLOW) {
			$this->_acl->setPageAccessRule(ALLOW);	
		//} else {
		//	$this->_acl->setPageAccessRule(DENY);
		//}
		
		return true;
	}

	public function action() {
		$model = new Model_Forum();
		$userModel = new Model_User();

		$board = $model->fetchBoard($this->_boardId);
		$category = $model->fetchCategory($board['category']);
		$threads = $model->fetchThreads($board['id'], 0, THREADS_PER_PAGE);
		
		// This method only removes the board if there are no unread threads in it
		CurrentUser::getInstance()->removeBoardFromUnreadList($board['id']);
		
		$numberOfPages = ($model->fetchThreadCount($board['id']) / THREADS_PER_PAGE);
		
		if(!empty($threads)) {
			foreach($threads as &$thread) {
				$lastpost = $model->fetchLastPost($thread['id']);
				$thread['last_post'] = $lastpost;
			}
		}
			
		$this->view->assign('pagetitle', $board['title']);
		$this->view->assign('board', $board);
		$this->view->assign('category', $category);
		$this->view->assign('threads', $threads);
		
		/* Problem here, need to figure out how to clean the url. see smarty_modifier_cleanForURL */
		$paginatorurl = SITE_PATH.'forum/board/'.$board['title'].'-'.$board['id'];
		$this->view->assign('pagination', $this->view->createPagination($this->_page, $numberOfPages, $paginatorurl));
		$this->view->display('forum_board.tpl');

	}
}
