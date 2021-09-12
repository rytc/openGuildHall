<?php

function smarty_function_isLoggedIn($params, $template) {
	return CurrentUser::getInstance()->isLoggedIn();
}