<?php

function smarty_modifier_formatDate($string, $includeTime = false) {
		
		$timestamp = $string;

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
		if($includeTime == true) {  
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