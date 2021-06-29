<?php
/**
 *  This file is part of PREGUSIA-PHP-FRAMEWORK.
 *  PREGUSIA-PHP-FRAMEWORK is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published by
 *  the Free Software Foundation; either version 2.1 of the License, or
 *  (at your option) any later version.
 *  
 *  PREGUSIA-PHP-FRAMEWORK is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU Lesser General Public License for more details.
 *  
 *  You should have received a copy of the GNU Lesser General Public License
 *  along with PREGUSIA-PHP-FRAMEWORK; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 *  
 *  @author pregusia
 *
 */


class HTTPSession {
	
	private $loaded = false;
	private $oRequest = null;
	private $oConfig = null;
	
	//************************************************************************************
	/**
	 * @return Configuration
	 */
	public function getConfig() { return $this->oConfig; }
	
	//************************************************************************************
	/**
	 * @param IHTTPServerRequest $oRequest
	 * @param Configuration $oConfig
	 */
	public function __construct($oRequest=null,$oConfig=null) {
		$this->oRequest = $oRequest;
		$this->oConfig = $oConfig;
		$this->loaded = false;
	}
	
	//************************************************************************************
	private function maybeLoad() {
		if ($this->loaded) return;
		
		$oRequest = $this->oRequest;
		$oConfig = $this->oConfig;
		$this->loaded = true;
		
		if ($oRequest) {
			$setCookieParams = true;
			$cookieTime = 3600;
			$cookiePath = '/';
			
			if ($oConfig) {
				if ($oConfig->hasKey('setCookieParams')) $setCookieParams = $oConfig->getValue('setCookieParams');
				if ($oConfig->hasKey('cookieTime')) $cookieTime = $oConfig->getValue('cookieTime');
				if ($oConfig->hasKey('cookiePath')) $cookiePath = $oConfig->getValue('cookiePath');
			}
			
			if ($setCookieParams) {
				session_set_cookie_params($cookieTime, $cookiePath, $oRequest->getHost(), $oRequest->isSecure());
			}
		}
		
		session_start();
		
		// sprawdzamy czy IP sie nie zmienilo
		if ($oConfig && $oRequest && $oConfig->getValue('ensureSameIP')) {
			$currentIP = $oRequest->getRemoteAddr();
			if (isset($_SESSION['__check_ip'])) {
				
				if ($_SESSION['__check_ip'] != $currentIP) {
					session_unset();
					return;
				}
				
			} else {
				$_SESSION['__check_ip'] = $currentIP;
			}
		}
		
		// sprawdzamy czy agent sie nie zmienil
		if ($oConfig && $oRequest && $oConfig->getValue('ensureSameAgent')) {
			$currentAgent = $oRequest->getHeaders()->getOneIgnoringCase('User-Agent');
			if (isset($_SESSION['__check_agent'])) {
				
				if ($_SESSION['__check_agent'] != $currentAgent) {
					session_unset();
					return;
				}
				
			} else {
				$_SESSION['__check_agent'] = $currentAgent;
			}
		}
		
		// regenerate ID time
		if ($oConfig && $oConfig->getValue('regenerateIDInterval')) {
			$interval = intval($oConfig->getValue('regenerateIDInterval'));
			
			if (isset($_SESSION['__regen_id_time'])) {
				
				$t = $_SESSION['__regen_id_time'];
				if (time() - $t > $interval) {
					$_SESSION['__regen_id_time'] = time();
					session_regenerate_id();
				}
				
			} else {
				$_SESSION['__regen_id_time'] = time();
			}
		}
	}
	
	//************************************************************************************
	public function getSessionID() {
		$this->maybeLoad();
		return session_id();
	}
	
	//************************************************************************************
	public function get($key) {
		$this->maybeLoad();
		return $_SESSION[$key];
	}
	
	//************************************************************************************
	public function set($key, $val) {
		$this->maybeLoad();
		$_SESSION[$key] = $val;
	}
	
	//************************************************************************************
	public function remove($key) {
		$this->maybeLoad();
		unset($_SESSION[$key]);
	}

	
}

?>