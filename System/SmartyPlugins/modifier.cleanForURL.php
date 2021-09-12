<?php

function smarty_modifier_cleanForURL($string) {
	$text = str_replace(" ", "-", $string);

	// Remove all special characters
	$special = "!@#$%^&*()_+?><\":/{}[]';.,";
	$array = preg_split('//', $special, -1, PREG_SPLIT_NO_EMPTY);
	foreach($array as $char) {
		$text = str_replace($char, '', $text);
	}

	return $text;

}