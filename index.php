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

/*
 * Bootstrap file. All requests get routed through this page. This page does a lot of heavy lifting setting
 * up the router, database configuration, and the front controller.
 */

/**
 * \mainpage
 *
 * \section Introduction Introduction
 * This is the developer documentation for openGuildHall. If you would like information on 
 * installing, customizing, or using openGuildHall please refer to the main website.
 *
 *
 */

define('VERSION', '0.2a');
define('VERSION_STRING', 'openGuildHall v0.2a');

define('SITE_PATH', '/');
define('ROOT_PATH', dirname(__FILE__));
define ('DEBUG', true);

define('ALLOW', 'ALLOW');
define('DENY', 'DENY');

session_start();

if(file_exists('Config/Config.php')) {
	require 'Config/Config.php';	
}

require 'System/Autoloader.php';

Autoloader::registerAutoloader();
Autoloader::addPath('System/');
Autoloader::addPath('Application/Plugins/');

if(isset($dbConfig)) {
	MySQL::getInstance()->setDbConfig($dbConfig);
	MySQL::getInstance()->openConnection();
}

$front = FrontController::getInstance();

require 'Config/Autoload.php';

$front->addPlugin('UpdateLastSeen');

$front->dispatchRequest();

// Closes the open connection if one exists
MySQL::getInstance()->closeConnection();


