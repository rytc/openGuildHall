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
 
class User_Controls extends Action {

	public function init() {
		Acl::getInstance()->setPageAccessRule(ALLOW);

		if(!CurrentUser::getInstance()->isLoggedIn()) {
			$this->view->displayMessage('Not logged in', 'You must be logged in to view this page.');
			return false;
		}

		return true;
	}

	public function action() {
		$user = new Model_User(CurrentUser::getInstance()->username);

		$this->view->assign('timezones', $this->getTimezoneList());
		$this->view->assign('countries', Countries::getCountries());
		$this->view->assign('pagetitle',"User Controls");
		$this->view->assign('user', $user);
		$this->view->display('usercontrols.tpl');
	}

	public function ajax() {	
		$user = new Model_User(CurrentUser::getInstance()->username);

		$postVars = $this->getPostVariables();
		//print_r($postVars);
		//echo 'Loop through post vars...';
		foreach($postVars as $name => $value) {
			$user->$name = $value;
		}

		if(!$user->save()) {
			$exceptions = $user->getExceptions();
			
			echo '<ol class="error">';
			foreach($exceptions as $exception) {
				echo '<li>'.$exception->getMessage().'</li>';
			}
			echo '</ol>';
		} else {
			echo 'SUCCESS';
		}
	}	

	protected function getTimezoneList() {
		$timezones = DateTimeZone::listIdentifiers();
		$i = 0;
		foreach($timezones as $zones)
		{
			$zone = explode('/', $zones);
			$zone2[$i]['continent'] = isset($zone[0]) ? $zone[0] : '';
			$zone2[$i]['city'] = isset($zone[1]) ? $zone[1] : '';
			$zone2[$i]['subcity'] = isset($zone[2]) ? $zone[2] : '';
			$i++;
		}
		
		asort($zone2);
		$options = array();
		$options['UTC'] = 'UTC';
		foreach($zone2 as $zone)
		{
			extract($zone);
			
			if($continent === 'Africa' || $continent === 'America' || $continent === 'Antarctica' ||
			   $continent === 'Arctic' || $continent === 'Asia'    || $continent === 'Atlantic'   ||
			   $continent === 'Australia' || $continent === 'Europe' || $continent === 'Indian' ||
			   $continent === 'Pacific')
			{
				if(isset($city) != '')
				{
					if($subcity != '')
						$options[$continent][$continent.'/'.$city.'/'.$subcity] = 
											str_replace('_', ' ', $city.'/'.$subcity);
					else
						$options[$continent][$continent.'/'.$city] = str_replace('_', ' ', $city);
				}
			}

		}
		
		return $options;
	}
}
