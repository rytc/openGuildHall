<?php if(!defined('SITE_PATH')) die('No direct access allowed');
/* openGuildHall
 * Copyright (C) 2011 Ryan Capote
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
 * This file is automatically run before the FrontController and should
 * be used instead of modifying the bootstrap script
 */
 
/**
 * Default settings
 */
define('NEWS_CATEGORY', 1);

define('POSTS_PER_PAGE', 15);
define('THREADS_PER_PAGE', 20);

define('DEFAULT_THEME', 'Light');
define('DEFAULT_DATE_FORMAT', 'F j Y');
define('DEFAULT_TIME_FORMAT', 'h:i A');
define('DEFAULT_TIMEZONE', 'UTC');

define('RECAPTCHA_PUBLIC_KEY', '');
define('RECAPTCHA_PRIVATE_KEY', '');

$nbbc = Nbbc::getInstance()->getNbbcObject();
$nbbc->ClearSmileys();
$nbbc->SetSmileyUrl(SITE_PATH.'Application/Views/icons/smileys');
$nbbc->SetSmileyDir(ROOT_PATH.'/Application/Views/icons/smileys');

$nbbc->AddSmiley(':)','smile.png');
$nbbc->AddSmiley(':D','bigsmile.png');
$nbbc->AddSmiley(':(','sad.png');
$nbbc->AddSmiley(':|','uhm.png');
$nbbc->AddSmiley(':P','toung.png');
$nbbc->AddSmiley(':creeper:','creeper.png');


/**
 * Load the helpers
 */
//$view = View::getInstance();
//$view->loadHelper('User.php');
//$view->loadHelper('Site.php');

Autoloader::addPath('Application/Library/');

$acl = Acl::getInstance();
require 'DefaultAccessRules.php';

$router = Router::getInstance();
$router->addRewriteRule('/page/*', '/page/index');

$currentUser = CurrentUser::getInstance();

$user = $currentUser->tryCookieAuthentication();
$currentUser->initialize($user);

/*if(!defined('INSTALLED')) {
	define('SITE_THEME', 'Install/');	
} else {
	define('SITE_THEME', CurrentUser::getInstance()->theme . '/');	
}*/
define('SITE_THEME', 'oghLight');

// Remove global vars
unset($view);
unset($user);
unset($currentUser);

 