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

class Forum_Newthreada extends Action {
	protected $_acl;
	protected $_boardId;
	protected $_forumModel;
	protected $_userModel;

	public function init() {
		$this->_boardId = $this->getParam(0);
		Acl::getInstance()->setPageAccessRule(ALLOW);

		if(!Validator::isInt($this->_id)) {
			$this->view->displayMessage('Invalid ID', 'An error has occured. Please go back and try again.');
			return false;
		}

		return true;
	} 	

	public function action() {
		$this->view->assign('pagetitle', 'New Thread');

		if($this->isPost()) {
			$formValues = $this->getPostVariables();
			$post = new Model_Post();

			// Assign the values to the model
			foreach($formValues as $key => $value) {
				$thread->$key = $value;
			}

			if(array_key_exists('preview', $formValues)) {
				// The user only wants to preview their post
				$this->view->assign('post', $post);
				$this->view->display('form_postform.tpl');
			} else {
			
				$post->thread_id = $thread->id;
				if(!$post->save()) {
					$exceptions = $thread->getExceptions();
					$this->view->assign('exceptions', $exceptions);
					$this->view->assign('post', $post);
					$this->view->display('forum_postform.tpl');
				}
				
			}

		}

		$this->view->display('forum_postform.tpl')
	}

};
