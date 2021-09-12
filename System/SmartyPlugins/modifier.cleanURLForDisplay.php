<?php

function smarty_modifier_cleanURLForDisplay($string) {
	$string = str_replace("http://", "", $string);
	$string = str_replace("https://", "", $string);
	$string = str_replace("www.", "", $string);

	return $string;

}