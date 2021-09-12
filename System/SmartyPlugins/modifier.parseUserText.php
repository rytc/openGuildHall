<?php

function smarty_modifier_parseUserText($string, $enableSmileys = true, $enableBBcode = true) {
	$nbbc = Nbbc::getInstance();
	$text = $nbbc->parse($string, $enableSmileys, $enableBBcode);
		
	// Make URLs linked
	//$text = preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.\-]*(\-\?\S+)?)?)?)@', '<a href="$1">$1</a>', $text);
		
	return nl2br($text);

}