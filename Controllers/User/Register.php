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
 
define('ERROR_USERNAME_EXISTS', -10);
define('ERROR_PASSWORDS_NOT_SIMILAR', -11);
define('ERROR_EMAIL_REGISTERED', -12);
define('ERROR_BDAY_DAY', -13);
define('ERROR_BDAY_MONTH', -14);
define('ERROR_BDAY_YEAR', -15);


class User_Register extends Action {
	
	public function init() {
		Acl::getInstance()->setPageAccessRule(ALLOW);
		
		return true;
	}
	
	public function action() {
		$this->view->assign('pagetitle', 'Register');

		// Should find a better way to do this :P
        $months = array(
          0 => '-',
          1 => 'January',
          2 => 'February',
          3 => 'March',
          4 => 'April',
          5 => 'May',
          6 => 'June',
          7 => 'July',
          8 => 'August',
          9 => 'September',
          10 => 'October',
          11 => 'November',
          12 => 'December'
          );
        $this->view->assign('months', $months);

		if($this->isPost()) {
            $postVars = $this->getPostVariables();
            
            $user = new Model_User();
            $user->username = $postVars['username'];
            $user->password = $postVars['password'];
            $user->email = $postVars['email'];
            $user->birthday = array('day'   => $postVars['birthday_day'], 
            						'month' => $postVars['birthday_month'],
            						'year'  => $postVars['birthday_year']);
            $user->actkey = substr(md5(uniqid(mt_rand())), 0, 16);
            $user->register_date = time();

            $errors = array();
            if($postVars['password'] != $postVars['password2']) {
				$errors[] = 'Passwords do not match';
            }

            $exceptions = $user->getExceptions();
            if(!empty($exceptions)) {
            	foreach($exceptions as $exception) {
            		$errors[] = $exception->getMessage();
            	}
            }
			
			// Check for errors
			if($errors) {
				$this->view->assign('formValues', $postVars);
				$this->view->assign('errors', $errors);
			} else {

				// Success! We registered!
				if(!$user->save()) {
					$this->view->displayMessage('Register', 'There was a problem saving your account. Please contact the admin.');
					return;
				}
                
                // Send confirmation email
                $title = 'Thank you for registering!';
                $this->view->assign('username', $user->username);
                $this->view->assign('actkey', $user->actkey);
                $text = $this->view->fetch('activation_email.tpl');
                                                    
                $model->registerUser($data);
                
                $this->view->displayMessage('Account Registered', 'Your account has been successfully 
                                                            registered. Please check your email
                                                            for instructions on activating your
                                                            account');
                                                            
				mail($data['email'], $title, $text, 'From: noreply@fakeemailaddress.org') or die('Failed to send mail');
			}

		}
		
		$this->view->display('register.tpl');	

	}
	
}
