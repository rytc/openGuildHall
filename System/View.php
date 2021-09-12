<?php if(!defined('SITE_PATH')) die('No direct access allowed');
require 'Library/smarty/Smarty.class.php';
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
 * This class handles the controller output through Smarty
 */
class View extends Smarty {
	
	function __construct($theme) {
		parent::__construct();
		$this->template_dir = 'Themes/'.$theme;

		if(!is_dir('Themes/'.$theme.'/compiled')) {
			if(!mkdir('Themes/'.$theme.'/compiled')) {
				die('Server does not have write permissions for theme directory: '.$theme);
			}
		}
		$this->compile_dir = 'Themes/'.$theme.'/compiled';

		if(!is_dir('Themes/'.$theme.'/cache')) {
			if(!mkdir('Themes/'.$theme.'/cache')) {
				die('Server does not have write permissions for theme directory: '.$theme);
			}
		}
		$this->cache_dir = 'Themes/'.$theme.'/cache';

		$this->assign('SITE_PATH', SITE_PATH);
		$this->assign('theme_path', SITE_PATH.'Themes/'.$theme.'');

		$this->plugins_dir[] = 'System/SmartyPlugins';
}
	/**
	 * Displays a message with links to the next page. Automatically adds a 
	 * link to the next page.
	 * \param $title The title of the message
	 * \param $text The text of the message
	 * \param $links An associative array of links. Ex. {'Index' => '/index', 'Roster' => '/roster'}
	 */
	function displayMessage($title, $text, $links = array()) {
		
		$links['Go Back'] = PageTracker::getLastURI();
		$this->assign('pagetitle', $title);
		$this->assign('text', $text);
		$this->assign('links', $links);
		$this->display('message.tpl');

	}

	/**
	 * Generate pagination HTML from pagination.tpl
	 * \param $currentPage The current page number
	 * \param $totalPages The total number of pages
	 * \param $url The URL to the controller ex. SITE_PATH.'roster/'
	 */
	function createPagination($currentPage, $totalPages, $url) {
		$this->assign('currentPage', $currentPage);
		$this->assign('totalPages', $totalPages);
		$this->assign('url', $url);
		return $this->fetch('pagination.tpl');
	}
}
