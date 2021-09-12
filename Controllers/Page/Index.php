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
 
class Page_Index extends Action {
	private $_title;
	
	public function init() {
		$this->_title = $this->getParam(0);

		if(empty($this->_title)) {
			$this->view->displayMessage('View Page', 'This page cannot be found.');
			return false;
		}
		
		PageTracker::track();
		$this->_title = ucfirst(str_replace('-', ' ', $this->_title));

		Acl::getInstance()->setPageAccessRule(ALLOW);
	
		return true;
	}
	
	public function action() {
		$model = new Model_Page();
   
	    $pageData = $model->fetchPageByTitle($this->_title);

	    if($pageData['hidden'] >= 1) {
	        $this->view->displayMessage('View Page', 'This page has been marked as hidden.');
	        return;
	    } 
	
		if(empty($pageData)) {
		    $this->view->displayMessage('View Page', 'This page cannot be found.');
	        return;
		}
   
	    $this->view->assign('pagetitle', $pageData['title']);
	    $this->view->assign('pageData', $pageData);
		
		$this->view->display('page.tpl');
	}
}