<?php

function smarty_function_hasAccess($params, $template) {

	if(Acl::getInstance()->hasAccess($params['controller'], 
									 $params['page'] , 
									 $param['action']) == ALLOW) {
		return true; 
	} else {
		return false;
	}
}