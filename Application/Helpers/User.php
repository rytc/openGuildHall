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
 * Set of helpers for Users
 */
 
function isLoggedIn() {
	return CurrentUser::getInstance()->isLoggedIn();
}
 
function getUsername() {
 	return CurrentUser::getInstance()->username;
}

function profileLink($username) {
	$u = str_replace(' ', '-', $username);
	
	return '<a href="'.SITE_PATH.'user/profile/'.$u.'">'.$username.'</a>';
}

function privateMessageLink() {
	$content = '<a href="'.SITE_PATH.'pvtmsg">';
	//$pmcount = getPrivateMessageCount();
	$pmcount = 0;
	
	if($pmcount == 0) {
		$content .= 'You have no new private messages.';
	} elseif($pmcount == 1) {
		$content .= 'You have 1 new private message!';
	} else {
		$content .= 'You have '.$pmcount.' new private messages!';
	}
	
	$content .= '</a>';
	return $content;
}

function userHasAccessByKey($key) {
	return true;
}

function checkUserPrivacy($setting) {
	if($setting == 2) {
		return true;
	}
	
	$isLoggedIn = CurrentUser::getInstance()->authenticated;  
	if($setting == 1 && $isLoggedIn) {
		return true;
	}
	
	$userGroup = CurrentUser::getInstance()->group;
	if($setting == 0 && $isLoggedIn && $userGroup >= 3) {
		return true;
	}
	
	return false;
}

function genderIcon($gender) {
	if($gender == 'male') {
		return '<div class="sprite male">&nbsp;</div>';
	} elseif($gender == 'female') {
		return '<div class="sprite female">&nbsp;</div>';
	} else {
		return '';
	}
	
}

function countryIcon($country) {
	if(!$country)
		$country = "0";

	return '<img src="'.SITE_PATH.'Views/countries/'.$country.'.png" class="cntyicon" />';
}

function displayAvatar($avatar) {
	if($avatar == 'none' || !$avatar) {
		return '<img src="'.SITE_PATH.'Views/'.SITE_THEME.'Graphics/noavatar.png" alt="avatar" class="avatar" />';
	} else {
		return '<img src="'.$avatar.'" alt="avatar" class="avatar" />';
	}
}

function onlineFlag($lastSeen) {
	$diff = time() - $lastSeen;
	
	// Seen within the last 10 minutes, they are online
	if($diff < 600) {
		return '<div class="sprite greenflag"><acronym title="Online">&nbsp;</acronym></div>';
	} elseif($diff < 900) { // 15 minutes they are away
		return '<div class="sprite orangeflag"><acronym title="Away">&nbsp;</acronym</div>';
	} 
	
	 // Offline
	return '<div class="sprite redflag"><acronym title="Offline">&nbsp;</acronym></div>';
	
}



