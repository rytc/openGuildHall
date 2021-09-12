<?php

function smarty_function_displayAvatar($params, $template) {
	$avatar = $params['url'];
	if($avatar == 'none' || !$avatar) {
		return '<img src="'.SITE_PATH.'Themes/'.SITE_THEME.'/graphics/noavatar.png" alt="avatar" class="avatar" />';
	} else {
		return '<img src="'.$avatar.'" alt="avatar" class="avatar" />';
	}
}