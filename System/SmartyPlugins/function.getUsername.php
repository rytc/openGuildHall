<?php

function smarty_function_getUsername($param, $template) {
	return CurrentUser::getInstance()->username;
}