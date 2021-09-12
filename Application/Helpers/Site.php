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
 * Returns a fortune from the database
 */
function fortune() {
	$quotes = new Model_Quotes();
        
	return $quotes->fetchRandomQuote();
}

/* 
 * Returns HTML formatted and escaped text from BBCode text from the user
 */
function parseUserText($text, $enableSmileys = true, $enableBBCode = true) {
		$nbbc = Nbbc::getInstance();
		
		$text = $nbbc->parse($text, $enableSmileys, $enableSmileys);
		
		// Make URLs linked
		//$text = preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.\-]*(\-\?\S+)?)?)?)@', '<a href="$1">$1</a>', $text);
		
		return nl2br($text);
}

/*
 * A nice wrapper for generating checkbox html, so we don't have to
 * put the if checkbox_is_checked in the layout script
 */
function checkbox($name, $value, $checked) {
	if($checked || $checked >= 1) {
		$result = '<input type="checkbox" name="'.$name.'" value="'.$value.'" checked />';
	} else {
		$result = '<input type="checkbox" name="'.$name.'" value="'.$value.'" />';
	}
	
	return $result;
}

/*
 * This function insterts the singular or plural form
 * of the word depending on the count
 */
function pl($singular, $plural, $count) {
	if($count <> 1) {
		return $plural;
	} else {
		return $singular;
	}
}

/*
 * Returns text cleaned for placement in a URL
 * My forum post!! -> My-forum-post
 */
function cleanTextForURL($text) {
	// Replace white spaces with -
	$text = trim($text);
	$text = str_replace(" ", "-", $text);

	// Remove all special characters
	$special = "!@#$%^&*()_+?><\":/{}[]';.,";
	$array = preg_split('//', $special, -1, PREG_SPLIT_NO_EMPTY);
	foreach($array as $char) {
		$text = str_replace($char, '', $text);
	}

	return $text;
}

/*
 * Removes uneeded text from URLs to make them easier to read
 */
function cleanURLForDisplay($url) {
	$url = str_replace("http://", "", $url);
	$url = str_replace("https://", "", $url);
	$url = str_replace("www.", "", $url);

	return $url;
}

/*
 * Returns an <a href> to clean up code
 */
function alink($url, $text) {
	if(strpos($url, "http") !== false || strpos($url, "www.") !== false) {
		$result = "<a href='".$url."'>".$text."</a>";	
	} else {
		$result = "<a href='".SITE_PATH.$url."'>".$text."</a>";
	}
	return $result;
}

/*
 * Formats a Unix Timestamp into a human readable format
 * Format is set based on user settings
 */
function formatDate($timestamp, $time = false) {
		
		if($timestamp == 0) {
			return 'Never';
		}
	
	
		$dateformat = CurrentUser::getInstance()->dateFormat;
		$timeformat = CurrentUser::getInstance()->timeFormat;
		$timezone = CurrentUser::getInstance()->timezone;
		//$defaultTimezone = date_default_timezone_get();
		//date_default_timezone_get($timezone);
		
		$tz = new DateTimeZone($timezone);
		$date = new DateTime(date('r', $timestamp));
		$date->setTimezone($tz);
		
		// If we are formatting both the time and date
		if($time == true) {  
			$currentDate = new DateTime(date('r', time()));
			$currentDate->setTimezone($tz);
			
			$minute = $date->format('i');
			$hour = $date->format('H');
			$day = $date->format('j');
			$month = $date->format('m');
			$year = $date->format('Y');

			$nowMinute = $currentDate->format('i');
			$nowHour = $currentDate->format('H');
			$nowDay = $currentDate->format('j');
			$nowMonth = $currentDate->format('m');
			$nowYear = $currentDate->format('Y');
			
			// Date is in the past
			if($month <= $nowMonth || $year <= $nowYear) {
				// If it's more than a month ago
				// just display it has normal.
				if($year != $nowYear || $month != $nowMonth) {
					$format = $dateformat.'  '.$timeformat;
					$result = $date->format($format);

				// If it's less than a month ago
				// Display in days
				} elseif($nowDay != $day) {
					$days = $nowDay - $day;
				
					if($days > 7) {
						$format = $dateformat.'  '.$timeformat;
						$result = $date->format($format); 
					} else {
						if($days > 1)
							$result = $days.' days ago';
						else 
							$result = 'Yesterday';
					}

				// If it's less than a day ago
				// display in hours
				} elseif($nowHour != $hour) {
					$span = ($nowHour - $hour);
					if($span > 1)
						$result = $span.' hours ago';
					else
						$result = $span.' hour ago';

				// If it's less than an hour ago
				// display in minutes
				} elseif($nowMinute != $minute) {
					$span = ($nowMinute - $minute);
					if($span > 1)
						$result = $span.' minutes ago';
					else
						$result = $span.' minute ago';

				// If it's less than a minute ago
				// display 'Less than a minute ago' =p
				} elseif($nowMinute == $minute) {
					$result = 'less than a minute ago';
				}
			} else { // Date is in the future
				$format = $dateformat.'  '.$timeformat;
				$result = $date->format($format);
			}

		// We don't want to format the time, just the date
		} else {
			$result = $date->format($dateformat);
		}
		
		//date_default_timezone_set($defaultTimezone);

		return $result;
}

/*
 * Returns an array of timezones
 */
function getTimezones() {
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

function threadIsUnread($boardid, $threadid) {
	return CurrentUser::getInstance()->threadIsUnread($boardid, $threadid);
}

function boardIsUnread($boardid) {
	return CurrentUser::getInstance()->boardIsUnread($boardid);
}

/*
 * Returns the last page that the user was viewing
 */
function getLastUri() {
	return PageTracker::getLastUri();
}

/*
 * Generates a list of pages based on the current page to the goal page number
 */
function generatePageList($currentPage, $goal) {
	$span = $goal - $currentPage;
	$step = abs($span) / $span;
	$list = array();

	for($x = $currentPage; $x !== $goal;) {
		// We want to add the step at the beginning of the loop, instead of at the end
		// Fixes bug when there are only two pages
		$x += $step;
		$list[] = $x;
	}

	return $list;
    
}

/*
 * Returns the number of querys preformed on the page
 */
function getQueryCount() {
	return MySQL::getInstance()->getQueryCount();
}

