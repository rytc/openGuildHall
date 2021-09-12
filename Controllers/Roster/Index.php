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

define('USERS_PER_PAGE', 10);

class Roster_Index extends Action {
	public function init() {
		Acl::getInstance()->setPageAccessRule(ALLOW);
		
		return true;
	}
	
	public function action() {
		$model = new Model_User();
		$page = $this->getParam(0);
		
		if(!Validator::isInt($page) || $page == 0) {
			$page = 1;
		}
		
		$users = $model->fetchUserList($page, USERS_PER_PAGE);
		$userCount = $model->fetchNumberOfUsers();
		$numberOfPages = ceil($userCount / USERS_PER_PAGE);
		
		$this->view->assign('pagination', $this->view->createPagination($page, $numberOfPages, 
																SITE_PATH.'roster'));
		$this->view->assign('pagetitle', 'Roster');
		$this->view->assign('users', $users);
		$this->view->display('roster.tpl');
		
	}
}	
