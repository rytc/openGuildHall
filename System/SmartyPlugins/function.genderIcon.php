<?php

function smarty_function_genderIcon($params, $template) {
	$gender = $params['gender'];

	if($gender == 'male') {
		return '<div class="sprite male">&nbsp;</div>';
	} elseif($gender == 'female') {
		return '<div class="sprite female">&nbsp;</div>';
	} else {
		return '';
	}
}