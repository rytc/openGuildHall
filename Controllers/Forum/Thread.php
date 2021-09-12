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
 
class Forum_Thread extends Action {
	
	protected $_threadId;
	protected $_page;
	protected $_acl;
	
	public function init() {
		$this->_acl = Acl::getInstance();
		
		$threadurl = preg_split('/-/', $this->getParam(0));
		$this->_threadId = end($threadurl);
		
		if(!Validator::isInt($this->_threadId)) {
			LayoutHelper::displayMessage($this->view, 'Invalid Thread', 'Invalid  Thread URL');
			return false;
		}
		
		$this->_page = $this->getParam(1);
		
		// If the last param is 'last', keep going
		if($this->_page == null || !Validator::isInt($this->_page) 
			&& $this->_page != 'last') {
			$this->_page = 1;
		}
		
		$this->_acl->setPageAccessRule(ALLOW);
		
		return true;
	}
	
	public function action() {
		$model = new Model_Forum();
		$userModel = new Model_User();
			
		$thread = $model->fetchThread($this->_threadId);
		
		if(empty($thread)) {
			LayoutHelper::displayMessage($this->view, 'Invalid Thread', 'That thread does not exist!');
			return;
		}
		
		$model->incrementThreadViews($thread['id']);
		$board = $model->fetchBoard($thread['board']);
		$category = $model->fetchCategory($board['category']);
		$numOfPages = ceil($model->fetchPostCount($thread['id']) / POSTS_PER_PAGE);
		
		CurrentUser::getInstance()->removeThreadFromUnreadList($board['id'], $thread['id']);
		
		// We want to go to the last page
		if($this->_page == 'last') {
			$this->_page = $numOfPages;
		}
		
		$posts = $model->fetchPosts($this->_threadId, $this->_page, POSTS_PER_PAGE);
		if($this->_acl->hasAccess('Forum', 'Board', 'View', $thread['board']) == DENY) {
			LayoutHelper::displayMessage($this->view, 'Access Denied', 'You do not have the right credentials to view this thread');
			return;
		}
		
		if($this->_acl->hasAccess('Forum', 'Board', 'Moderate', $thread['board']) == ALLOW) {
			$this->view->isMod = true;
		}
		
		$users = array();
		foreach($posts as $post) {
			if(isset($users[$post['author_id']]))
				continue;
				
			$user = $userModel->fetchUserById($post['author_id']);
			$users[$user['id']] = $user;
		}
		
		$this->view->pagenum = $this->_page;
		$this->view->pagetitle = $thread['title'];
		$this->view->thread = $thread;
		$this->view->board = $board;
		$this->view->category = $category;
		$this->view->posts = $posts;
		$this->view->users = $users;
		
		$paginatorurl = SITE_PATH.'forum/thread/'.cleanTextForUrl($thread['title'].'-'.$thread['id']).'/';
		$this->view->pagination = LayoutHelper::createPagination($this->view, $this->_page, 
																$numOfPages, $paginatorurl);
		
		LayoutHelper::display($this->view, 'Scripts/Forum/thread.phtml', SITE_THEME.'forum.css');
	}
}
