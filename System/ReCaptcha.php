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
 
require_once 'Library/recaptchalib.php';

/**
 * ReCAPTCHA wrapper
 * @link http://www.google.com/recaptcha
 */
class ReCaptcha {

    protected $_publicKey;
    protected $_privateKey;
    protected $_error;
    /**
	 * Constructor
	 * @param string $publicKey Your ReCaptcha public key
	 * @param string $privateKey Your ReCaptcha private key
	 */
    function __construct($publicKey, $privateKey) {
        $this->_publicKey = $publicKey;
        $this->_privateKey = $privateKey;
        $this->_error = '';
    }
    
	/**
	 * @return string Returns the html for the captcha image/field
	 */
    function getHtml() {
        return recaptcha_get_html($this->_publicKey);
    }
    
	/**
	 * Checks the captcha answer
	 * @param string $challenge The value from the recapthca_challenge_field
	 * @param string $response The value from the recaptcha_response_field
	 * @return bool True on success, false on error. Error is set on failure.
	 * @see ReCaptcha::getError()
	 */
    function checkAnswer($challenge, $response) {
        $result = recaptcha_check_answer($this->_privateKey,
                                         $_SERVER['REMOTE_ADDR'],
                                         $challenge,
                                         $response);
                                         
        if(!$result->is_valid) {
            $this->_error = $result->error;
            return false;
        } else {
            return true;
        }
        
    }
    
	/**
	 * @return strung Returns the error straight from ReCapthca
	 */
	public function getError() {
		return $this->_error;
	}
	
	/**
	 * @return string Returns an error message
	 */
    public function getErrorMessage() {

        switch($this->_error) {
          case 'invalid-site-public-key':
          case 'invalid-site-private-key':
                return 'Invalid public or private key';
                break;
          case 'invalid-request-cookie':
                return 'Invalid challenge';
                break;
          case 'incorrect-captcha-sol':
                return 'Captcha did not match';
                break;

        };
    }
};

