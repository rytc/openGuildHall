<?php

function smarty_function_onlineFlag($params, $template) {
	$lastSeen = $params['lastseen'];

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