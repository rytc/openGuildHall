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

/**
 * The index action for the installation controller.
 */
class Install_Index extends Action {
	
	public function init() {
		
		if(defined('INSTALLED')) {
			$this->view->displayMessage('Install', 'openGuildHall has already been installed.');
			return false;
		}

		return true;
	}	

	public function action() {
		
		// Default valies
		$errors = array();

		// Should these be moved to the install template?
		$formValues = array('mysqlusername' => '',
						  'mysqlpassword'   => '',
						  'mysqldatabase'   => '',
						  'mysqlhostname'   => 'localhost',
						  'mysqlprefix'     => 'ogh_',
						  'adminusername'   => '',
						  'adminemail'      => '',
						  'recaptcha_public' => '',
						  'recaptcha_private' => '');

		if($this->isPost()) {
			$formValues = $this->getPostVariables();

			/*
			 * Validate MySQL info
			 */
			$mysqlUsername = $formValues['mysqlusername'];
			$mysqlPassword = $formValues['mysqlpassword'];
			$mysqlDatabase = $formValues['mysqldatabase'];
			$mysqlHostname = $formValues['mysqlhostname'];
			$mysqlTablePrefix = $formValues['mysqlprefix'];
			$recaptcha_public = $formValues['recaptcha_public'];
			$recaptcha_private = $formValies['recaptcha_private'];


			if(!Validator::checkStringLength($mysqlUsername, 3, 16)) {
				$error['mysqlusername'] = 'Invalid username length';
			} elseif(Validator::hasSpecialCharacters($mysqlUsername)) {
				$error['mysqlusername'] = 'Invalid characters';
			}

			if(!Validator::checkStringLength($mysqlPassword, 4, 32)) {
				$error['mysqlpassword'] = 'Invalid password length';
			}

			if(!Validator::checkStringLength($mysqlDatabase, 2, 64)) {
				$error['mysqldatabase'] = 'Invalid database name length';
			} elseif(Validator::hasSpecialCharacters($mysqlDatabase)) {
				$error['mysqldatabase'] = 'Invalid database name.';
			}

			/*
			 * Validate admin account info
			 */
			 $adminUsername = $formValues['adminusername'];
			 $adminPassword = $formValues['adminpassword'];
			 $adminPassword2 = $formValues['adminpassword2'];
			 $adminEmail = $formValues['adminemail'];

			if(FormValidator::validateUsername($adminUsername) != SUCCESS) {
				$errors['adminusername']  = 'Invalid username';
			}

			if($adminPassword != $adminPassword2) {
				$errors['adminpassword'] = 'Passwords did not match';
			} elseif(FormValidator::validatePassword($adminPassword) != SUCCESS) {
				$errors['adminpassword'] = 'Invalid password';
			}

			if(FormValidator::validateEmail($adminEmail) != SUCCESS) {
				$errors['adminemail'] = 'Invalid email address';
			}

			if(empty($errors)) {
				$dbConfig['username'] = $mysqlUsername;
				$dbConfig['password'] = $mysqlPassword;
				$dbConfig['hostname'] = $mysqlHostname;
				$dbConfig['database'] = $mysqlDatabase;
				$dbConfig['tableprefix'] = $mysqlTablePrefix;
				$dbConfig['recaptcha_public'] = $recaptcha_public;
				$dbConfig['recaptcha_private'] = $recaptcha_private;

				MySQL::getInstance()->setDBConfig($dbConfig);

				try {
					MySQL::getInstance()->openConnection();
				} catch(Exception $e) {
					LayoutHelper::displayMessage($this->view, 'Install', 'Unable to connect to MySQL: '.$e->getMessage());
					return;
				}

				$model = new Model_Install();

				$model->createTables();

				$groupModel = new Model_Groups();
				$aclModel = new Model_Acl();
				$userModel = new Model_User();

				$guestGroup = $groupModel->insert('Guest');
				$registeredGroup = $groupModel->insert('Registered');
				//$memberGroup = $groupModel->insert('Member');
				$adminGroup = $groupModel->insert('Admin');

				// Registered group
				$aclModel->insertGroupPermission('Forum', 'ViewBoard', NULL, $registeredGroup, ALLOW);
				$aclModel->insertGroupPermission('Forum', 'ViewCategory', NULL, $registeredGroup, ALLOW);
				$aclModel->insertGroupPermission('Forum', 'Reply', NULL, $registeredGroup, ALLOW);
				$aclModel->insertGroupPermission('Forum', 'NewThread', NULL, $registeredGroup, ALLOW);

				// Member permissions
				/*$aclModel->insertGroupPermission('Forum', 'ViewBoard', NULL, $adminGroup, ALLOW);
				$aclModel->insertGroupPermission('Forum', 'ViewCategory', NULL, $adminGroup, ALLOW);
				$aclModel->insertGroupPermission('Forum', 'Reply', NULL, $adminGroup, ALLOW);
				$aclModel->insertGroupPermission('Forum', 'NewThread', NULL, $adminGroup, ALLOW);*/

				// Admin permissions
				$aclModel->insertGroupPermission('Forum', 'Moderate', NULL, $adminGroup, ALLOW);
				$aclModel->insertGroupPermission('Forum', 'ViewBoard', NULL, $adminGroup, ALLOW);
				$aclModel->insertGroupPermission('Forum', 'ViewCategory', NULL, $adminGroup, ALLOW);
				$aclModel->insertGroupPermission('Forum', 'Reply', NULL, $adminGroup, ALLOW);
				$aclModel->insertGroupPermission('Forum', 'NewThread', NULL, $adminGroup, ALLOW);
				$aclModel->insertGroupPermission('AdminControls', 'Edit', NULL, $adminGroup, ALLOW);

				$user = array(
						'username' => $adminUsername,
						'password' => FormValidator::getHashedPassword($adminPassword),
						'email'	   => $adminEmail,
						'birthday' => time(),
						'group'    => $adminGroup,
						);

				$userModel->registerUser($user);
				
				$this->writeConfigFile($dbConfig);

				$this->view->displayMessage($this->view, 'Install', 'The install was successful!', array('Home', '/'));
				return;
			}

		}
		$this->view->assign('pagetitle', 'Install');
		$this->view->assign('errors', $errors);
		$this->view->assign('formValues', $formValues);
		$this->view->display('install.tpl');
	}

	protected function writeConfigFile($dbConfig) {
		$configFile = fopen(ROOT_PATH.'/Config/Config.php', 'w');

		if(!$configFile) {
			die('Failed to write the config file. Do you have write permissions? Is the path setup correctly?');
		}

		fwrite($configFile, "<?php\n\n");
		fwrite($configFile, '$dbConfig[\'username\'] = \''.$dbConfig['username']."';\n");
		fwrite($configFile, '$dbConfig[\'password\'] = \''.$dbConfig['password']."';\n");
		fwrite($configFile, '$dbConfig[\'database\'] = \''.$dbConfig['database']."';\n");
		fwrite($configFile, '$dbConfig[\'hostname\'] = \''.$dbConfig['hostname']."';\n");
		fwrite($configFile, '$dbConfig[\'tableprefix\'] = \''.$dbConfig['tableprefix']."';\n");
		fwrite($configFile, 'define(\'RECAPTCHA_PUBLIC\',\''.$dbConfig['recaptcha_public'].'\');');
		fwrite($configFile, 'define(\'RECAPTCHA_PRIVATE\',\''.$dbConfig['recaptcha_private'].'\');');
		fwrite($configFile, 'define(\'INSTALLED\', true);');

		fclose($configFile);
	}

};

