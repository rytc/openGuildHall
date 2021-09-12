<?php if(!defined('SITE_PATH')) die('No direct access allowed');
/* openGuildHall
 * Copyright (C) 2011 Ryan Capote <trooper777@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of 
 * the GNU General Public License as published by the Free Software Foundation; either version 
 * 2 of the License, or (at  your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program; 
 * if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */
 
 
/**
 * This class helps create JSON formatted responses
 * for interacting with Javascript.
 */
class JsonResponse {
	
	protected $_data;
	
	/**
	 * Adds data to the return.
	 * @param string $key The key to store the data under
	 * @param string|array $value The data to store
	 */
	public function addData($key, $value) {
		$this->_data[$key] = $value;
	}
	
	/**
	 * Adds an error to the response.
	 * JSON Error:
	 * @param string $element The element that has caused the error. ['error']['element']
	 * @param int $code The error code. ['error']['code']
	 * @param string $message Optional error message. ['error']['message']
	 */ 
	public function addError($element, $code, $message='') {
		$this->_data['error'] = array('element'   => $element,
										'code'    => $error,
										'message' => $message);
	}
	
	/**
	 * Returns the data formatted
	 */
	public function getJson() {
		$result = json_decode($this->_data);
		return $result;
	}
	
	
}
