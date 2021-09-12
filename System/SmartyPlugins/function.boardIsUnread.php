<?php

function smarty_function_boardIsUnread($params, $template) {
	
	$id = $params['id'];

	return CurrentUser::getInstance()->boardIsUnread($id);

}