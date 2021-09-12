<?php

function smarty_function_countryIcon($params, $template) {
	$country = $params['country'];
	if(!$country)
		$country = "0";

	return '<img src="'.SITE_PATH.'System/countries/'.$country.'.png" class="cntyicon" />';
}