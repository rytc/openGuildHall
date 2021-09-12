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
 
class Forum_Category extends Action {
	protected $_categoryId;
	protected $_acl;
	
	public function init() {
		$this->_acl = Acl::getInstance();

		$categoryurl = preg_split('/-/', $this->getParam(0));
		$this->_categoryId = end($categoryurl);
		
		if(!Validator::isInt($this->_categoryId)) {
			$this->view->displayMessage('Invalid Category', 'Invalid category URL');
			return false;
		}
		
		$this->_acl->setPageAccessRule(ALLOW);
		
		return true;
	}
	
	public function action() {
		$model = new Model_Forum();
		
		$category = $model->fetchCategory($this->_categoryId);

		if(empty($category)) {
			$this->view->displayMessage('Invalid Cateogry', 'That category does not exist');
			return;
		}
		
		//if($this->_acl->hasAccess('Forum', 'Category', 'View', $this->_categoryId) == DENY) {
		//	$this->view->displayMessage('Access Denied', 'You do not have the credentials to view this category');
		//	return;
		//}
		
		$boards = $model->fetchBoardsByCategory($this->_categoryId);
		
		// Remove any boards the user doesn't have access to
		for($i = 0; $i < count($boards); $i++) {
		//	if(!$this->_acl->hasAccess('Forum', 'Board', 'View', $boards[$i]['id'])) {
		//		unset($boards[$i]);
		//	} else {
				$boards[$i]['last_post'] = $model->fetchLastPostInBoard($boards[$i]['id']);
		//	}
		}
		
		$this->view->assign('pagetitle', $category['title']);
		$this->view->assign('category', $category);
		$this->view->assign('boards', $boards);
		$this->view->display('forum_category.tpl');
		
	}

}