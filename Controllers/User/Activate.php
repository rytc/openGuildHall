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
 
define('ERROR_USERNAME_FAIL', -10);

class User_Activate extends Action {
	public function init() {
		Acl::getInstance()->setPageAccessRule(ALLOW);
		
		return true;
	}
	
	public function action() {
		if($this->isPost()) {
			$errors = array();
	        $postVars = $this->getPostVariables();
	        $model = new Model_User();

	        // Validate the username 
	        $username = $postVars['username'];
	        $u = FormValidator::validateUsername($username);
	        if($u != SUCCESS) {
	        	$errors['username'] = $u;	
	        } else {
	        	if(!$model->isUsernameRegistered($username)) {
	                $errors['username'][] = ERROR_USERNAME_FAIL;
	            }
	        }
        
	        // Validate the activation key
	        $actkey = $postVars['actkey'];
	        $errors['actkey'] = FormValidator::validateActivationKey($actkey);
        
	        if(!Validator::isMultiArrayEmpty($errors)) {
	            $this->view->formValues = $postVars;
	            $this->view->errors = $errors;
	        } else {
	            if($model->activateAccount($username, $actkey)) {
	                $this->displayMessage('Account Activated', 'Your account was successfully 
	                                                            activated. You may now login.');
	            } else {
	                $this->view->formValues = $postVars;
	                $this->view->errormsg = "There was a problem activating
	                                        activating your account. Either
	                                        your key is incorrect, or it did
	                                        not match with that username.";
	            }
	        }
		}
		
		$this->view->pagetitle =  'Activate Account';
		LayoutHelper::display($this->view, 'Scripts/User/Activate.phtml');
	}
}
