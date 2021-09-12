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
 
class Forum_Admin extends Action {
	protected $_mode;
	protected $_id;
	protected $_model;
	
	public function init() {
		
		if(Acl::getInstance()->hasAccess('Forum', 'Board', 'Moderate') == DENY ||
			!CurrentUser::getInstance()->isLoggedIn()) {
			LayoutHelper::displayMessage($this->view, 'Access Denied', 'You do not have the right credentials to access this page.');
			return false;		
		}
		
		$this->_mode = $this->getParam(0);
		$this->_id = $this->getParam(1);
		
		if(!Validator::isInt($this->_id) || Validator::hasSpecialCharacters($this->_mode)) {
			LayoutHelper::displayMessage($this->view, 'Error', 'Malformed URL');
			return false;
		}
		
		PageTracker::dontTrack();
		
		return true;
		
	}
	
	public function action() {
		$this->_model = new Model_Forum();
		$this->view->mode = $this->_mode;
		$this->view->id = $this->_id;
		
		switch($this->_mode) {
			case 'deletethread':
				$this->deleteThread();
				break;
			case 'lockthread':
				$this->lockThread();
				break;
			case 'movethread':
				$this->moveThread();
				break;
			case 'stickythread':
				$this->stickyThread();
				break;
			default:
				LayoutHelper::displayMessage($this->view, 'Invalid mode', 'Invalid admin mode');
				break;
			
		}		
	}
	
	private function deleteThread() {
		$thread = $this->_model->fetchThread($this->_id);
		$this->view->pagetitle = 'Delete Thread';
		
		if(empty($thread)) {
			LayoutHelper::displayMessage($this->view, 'Error', 'There was a problem deleting the specified thread.');
			return;
		}
		
		if(!$this->isPost()) {
			$this->view->message = 'Are you sure you want to delete \''.$thread['title'].'\'? This action is not reversable!';
			LayoutHelper::display($this->view, 'Scripts/Forum/AdminConfirm.phtml');
		} else {
			$this->_model->deleteThread($thread['id']);
			LayoutHelper::displayMessage($this->view, 'Thread Deleted', 'The thread was successfully deleted.');
		}
		
		
	}
	
	private function lockThread() {
		$thread = $this->_model->fetchThread($this->_id);
		$this->view->pagetitle = 'Lock Thread';
		
		if(empty($thread)) {
			LayoutHelper::displayMessage($this->view, 'Error', 'There was a problem with the specified thread.');
			return;
		}
		if($thread['locked']>=1) {
						$this->view->pagetitle = 'Unlock Thread';
			$this->_model->unlockThread($thread['id']);
			LayoutHelper::displayMessage($this->view, 'Thread Unlocked', 'The thread was successfully unlocked.');
		} else {
			$this->_model->lockThread($thread['id']);
			LayoutHelper::displayMessage($this->view, 'Thread Locked', 'The thread was successfully locked.');
			
		}		
	}
	
	private function moveThread() {
		$thread = $this->_model->fetchThread($this->_id);
		$boardList = $this->_model->fetchBoards();
		$this->view->pagetitle = 'Move Thread';
		
		if(empty($thread)) {
			LayoutHelper::displayMessage($this->view, 'Error', 'There was a problem with the specified thread.');
			return;
		}
		
		if(!$this->isPost()) {
			$this->view->boardList = $boardList;
			LayoutHelper::display($this->view, 'Scripts/Forum/AdminSelectBoard.phtml');
		} else {
			$destBoardId = $_POST['board'];
			
			if(!Validator::isInt($destBoardId)) {
				LayoutHelper::displayMessage($this->view, 'What do you think you are doing?', 'Common bro, don\'t try to hack us.');
				return;
			}
			
			
			$this->_model->moveThread($this->_id, $destBoardId);
			LayoutHelper::displayMessage($this->view, 'Thread Moved', 'The thread was successfully moved.');
			
		}
	}
	
	private function stickyThread() {
		$thread = $this->_model->fetchThread($this->_id);
		$this->view->pagetitle = 'Sticky Thread';
		
		if(empty($thread)) {
			LayoutHelper::displayMessage($this->view, 'Error', 'There was a problem with the specified thread.');
			return;
		}
		
		if($thread['sticky']>=1) {
			$this->_model->unstickyThread($this->_id);
			LayoutHelper::displayMessage($this->view, 'Unsticky Thread', 'The thread was successully unstuck\'d');
		} else {
			$this->_model->stickyThread($this->_id);
			LayoutHelper::displayMessage($this->view, 'Sticky Thread', 'The thread was successully stuck\'d');
		}
	}
}