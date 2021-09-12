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
 
define('ERROR_TITLE_EMPTY', -10);
define('ERROR_TITLE_LENGTH', -11);

class Forum_Post extends Action {
	protected $_acl;
	protected $_mode;
	protected $_model;
	protected $_userModel;
	protected $_defaultFormValues;
	protected $_formValues;
	protected $_isPreview;
	protected $_errors;
	
	// The ID is the thread's id when the mode is newreply
	// The ID is the board's id when the mode is newthread
	// The ID is the post's id when the mode is edit
	protected $_id;
	
	public function init() {
		
		$this->_mode = $this->getParam(0);
		$this->_id = $this->getParam(1);
		
		// Validate the mode
		if($this->_mode != "newthread"  && 
			$this->_mode != "newreply" && 
			$this->_mode != "edit" &&
			$this->_mode != 'delete') {
			LayoutHelper::displayMessage($this->view, 'Invalid Mode', 'An error has occured. You\'re not trying to hack us are you?');
			return false;
		}
		
		
		// Validate the id
		if(!Validator::isInt($this->_id)) {
			LayoutHelper::displayMessage($this->view, 'Invalid Id', 'An error has occured. You\'r not trying to hack us are you?');
			return false;
		}
			
		
		$this->_acl = Acl::getInstance();
		$this->_acl->setPageAccessRule(ALLOW);
		
		return true;
	}
	
	public function action() {
		$this->_model = new Model_Forum();
		$this->_userModel = new Model_User();
		
		// Stupid annoying PHP can't handle null variables
		// I guess I should come up with a slightly more refined way
		// of doing this
		$this->_defaultFormValues = array('copy' => '',
										'title' => '',
										'disable_bbcode' => 0, 
										'disable_smileys' => 0);
		
		$this->isPreview = false;
			
		if($this->_mode == 'newthread') {
			if(!$this->newThread()) {
				return;
			}
			
		} elseif($this->_mode == 'newreply') {
			if(!$this->newReply()) {
				return;
			}
					
		} elseif($this->_mode == 'edit') {
			if(!$this->edit()) {
				return;
			}
						
		} elseif($this->_mode == 'delete') {
			if(!$this->delete()) {
				return;
			}			
		}
		
		if(!empty($this->_errors)) {
			$this->view->errors = $this->_errors;
		}
		
		$this->view->formValues = $this->_formValues;
		$this->view->mode = $this->_mode;
		$this->view->id = $this->_id;
		LayoutHelper::display($this->view, 'Scripts/Forum/Post.phtml', SITE_THEME.'forum.css');	
	}
	
	protected function newThread() {
		$board = $this->_model->fetchBoard($this->_id);
		$this->view->pagetitle = "New Thread";
		
		if(empty($board)) {
			LayoutHelper::displayMessage($this->view, 'Invalid Board', 'The specified board does not exist!');
			return false;
		}
		
		$category = $this->_model->fetchCategory($board['category']);
		
		if($this->_acl->hasAccess('Forum', 'Board', 'Post', $this->_id) == DENY) {
			LayoutHelper::displayMessage($this->view, 'Access Denied', 'You do not have the right credentials to view this board');
			return false;
		}
				
		$this->view->board = $board;
		$this->view->category = $category;
		
		if($this->isPost()) {
			$data = $this->validatePostVariables();
			
			if(empty($this->_errors) && !$this->_isPreview) {
				$date = time();
				$author = CurrentUser::getInstance()->id;
				$threadid = $this->_model->insertNewThread($data['title'], $date, $author, $board['id']);
				$this->_model->insertNewPost($data['title'], $data['copy'], $date, $author, $threadid, $data['disable_smileys'], $data['disable_bbcode']);
				$this->_userModel->incrementPostCount(CurrentUser::getInstance()->id);
				LayoutHelper::displayMessage($this->view, 'New Thread', 'You\'re thread has been successfully posted.');
				return false;
			} else {
				$this->_formValues = $data;
			}
		} else {
			$this->_formValues = $this->_defaultFormValues;
		}
		
		return true;
	}
	
	protected function newReply() {
		$thread = $this->_model->fetchThread($this->_id); 
		$this->view->pagetitle = "Reply to Thread";
		
		if(empty($thread)) {
			LayoutHelper::displayMessage($this->view, 'Invalid Thread', 'The specified thread does not exist!');
			return false;
		}
		
		$board = $this->_model->fetchBoard($thread['board']);
		$category = $this->_model->fetchCategory($board['category']);
		
		// If the user is quoting another user
		if(array_key_exists('quote', $_GET)) {
			$quotePostId = $_GET['quote'];
			if(Validator::isInt($quotePostId)) {
				$quotePost = $this->_model->fetchPost($quotePostId);
				
				if($quotePost) {
					$this->_defaultFormValues['title'] = '';
					$this->_defaultFormValues['copy'] = '[quote='.$quotePost['author'].']'.$quotePost['copy'].'[/quote]';
					$this->_defaultFormValues['disable_bbcode'] = 0;
					$this->_defaultFormValues['disable_smileys'] = 0;
					$this->_formData = $this->_defaultFormValues;
				}			 
			}
		}
		
		$this->view->board = $board;
		$this->view->category = $category;
		$this->view->thread = $thread;
		
		if($this->_acl->hasAccess('Forum', 'Board', 'Reply', $thread['board']) == DENY) {
			LayoutHelper::displayMessage($this->view, 'Access Denied', 'You do not have the right credentials to post in this board.');
			return false;
		}
		
		if($this->isPost()) {
			$data = $this->validatePostVariables();
			
			if(empty($this->_errors) && !$this->_isPreview) {
				$date = time();
				$author = CurrentUser::getInstance()->username;
				
				$this->_model->insertNewPost($data['title'], $data['copy'], $date, $author, $thread['id'], $data['disable_smileys'], $data['disable_bbcode']);
				$this->_model->updateThreadDate($date, $thread['id']);
				$userModel->incrementPostCount(CurrentUser::getInstance()->id);
				LayoutHelper::displayMessage($this->view, 'New Reply', 'You\'re reply has been posted.');
			} else {
				$this->_formValues = $data;
			}
		} else {
			$this->_formValues = $this->_defaultFormValues;
		}
		
		return true;
	}
	
	protected function edit() {
		$post = $this->_model->fetchPost($this->_id);
		$this->view->pagetitle = 'Edit Post';
		
		if(empty($post)) {
			LayoutHelper::displayMessage($this->view, 'Invalid Post', 'The specified post does not exist!');
			return false;				
		}
		
		// Fetch some info
		$thread = $this->_model->fetchThread($post['thread_id']);
		$board = $this->_model->fetchBoard($thread['board']);
		$category = $this->_model->fetchCategory($board['category']);
		
		// Make sure the user has access to edit the post
		// IF the current user is not the author OR
		// the current user is not a moderator AND the current user is not the author
		// THEN deny access
		if($post['author_id'] != CurrentUser::getInstance()->id &&
			$this->_acl->hasAccess('Forum', 'Board', 'Moderate') == DENY) {
			LayoutHelper::displayMessage($this->view, 'Access Denied', 'You do not have the right credentials to edit that post.');
			return;	
		}
		
		if(!$this->isPost()) {
			$this->_defaultFormValues['title'] = $post['title'];
			$this->_defaultFormValues['copy'] = $post['copy'];
			$this->_defaultFormValues['disable_bbcode'] = $post['disable_bbcode'];
			$this->_defaultFormValues['disable_smileys'] = $post['disable_smileys'];
			$this->_formValues = $this->_defaultFormValues;
		} else {
			$data = $this->validatePostVariables();
			$date = time();
			
			if(empty($this->_errors)) {			
				$this->_model->updatePost($data['title'], $data['copy'], $date, $data['disable_smileys'], $data['disable_bbcode'], $this->_id);
				$this->view->url = array('Back to thread' => SITE_PATH.'forum/thread/'.$post['thread_id']);
				LayoutHelper::displayMessage($this->view, 'Post Edited', 'Your post was successfully edited.');
				return false;
			} else {
				$this->_formValues = $data;
			}
		}
		
		$this->view->board = $board;
		$this->view->category = $category;
		$this->view->thread = $thread;
		
		return true;
	} 
	
	protected function delete() {
		$post = $this->_model->fetchPost($this->_id);
		$this->view->pagetitle = 'Delete Post';
		
		if(empty($post)) {
			LayoutHelper::displayMessage($this->view, 'Invalid Post', 'The specified post does not exist!');
			return false;
		}
		
		if(Acl::getInstance()->hasAccess('Forum', 'Board', 'Moderate') == DENY) {
			LayoutHelper::displayMessage($this->view, 'Access Denied', 'You do not have the right credentials to delete posts.');
			return false;
		}
		
		if(!$this->isPost()) {
			$this->view->url = SITE_PATH.'forum/post/delete/'.$post['id'];
			$this->view->message = 'Are you sure you want to delete that post? This action is not reversable!';
			PageTracker::dontTrack();
			LayoutHelper::display($this->view, 'Scripts/Forum/AdminConfirm.phtml');
			return true;
		} else {
			$model->deletePost($post['id']);
			LayoutHelper::displayMessage($this->view, 'Delete Post', 'The post was deleted.');
			return true;
		}
		
		return true;
	}
	
	protected function validatePostVariables() {
		$formValues = $this->getPostVariables();
		$formValues['copy'] = trim($formValues['copy']);
		$formValues['title'] = trim($formValues['title']);
		
		if(!array_key_exists('disable_bbcode', $formValues)) {
			$formValues['disable_bbcode'] = 0;
		} else {
			$formValues['disable_bbcode'] = 1;
		}
		
		if(!array_key_exists('disable_smileys', $formValues)) {
			$formValues['disable_smileys'] = 0;
		} else {
			$formValues['disable_smileys'] = 1;
		}
		
		if(array_key_exists('Preview', $formValues)) {
			$this->_isPreview = true;
		}
		
		if($this->_mode == 'newthread') {
			$min = 4;
		} else {
			$min = 0;
		}
		
		if(!Validator::checkStringLength($formValues['title'], $min, 60)) {
			$this->_errors['title'][] = 'Invalid length';
		}
		
		if(empty($formValues['copy'])) {
			$this->_errors['copy'][] = 'Invalid length';
		}
		
		return $formValues;
	}
	
}