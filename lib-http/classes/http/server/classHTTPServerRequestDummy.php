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


class HTTPServerRequestDummy implements IHTTPServerRequest {
	
	/**
	 * @var HTTPCookiesContainer
	 */
	private $oCookies = null;
	
	/**
	 * @var PropertiesMap
	 */
	private $oHeaders = null;
	
	//************************************************************************************
	public function __construct() {
		$this->oCookies = new HTTPCookiesContainer();
		$this->oHeaders = new PropertiesMap();
	}
	
	//************************************************************************************
	public function getRemoteAddr() { return '0.0.0.0'; }
	public function getRemotePort() { return 0; }
	public function getRequestURI() { return ''; }
	public function getRequestURL() { return ''; }
	public function getHost() { return ''; }
	public function isSecure() { return false; }
	public function getMethod() { return HTTPMethod::UNKNOWN; }
	public function getReferer() { return ''; }
	public function getContentType() { return ''; }
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return HTTPRequestFile
	 */
	public function getFile($name) {
		return null;
	}
	
	//************************************************************************************
	public function getPOSTParameter($name) { return ''; }
	public function hasPOSTParameter($name) { return false; }
	
	//************************************************************************************
	public function getPOSTParameters() {
		return new PropertiesMap();
	}
	
	//************************************************************************************
	public function getRequestContent() { return ''; }
	
	//************************************************************************************
	public function getGETParameter($name) { return ''; }
	public function hasGETParameter($name) { return false; }
	
	//************************************************************************************
	public function getGETParameters() {
		return new PropertiesMap();
	}
	
	//************************************************************************************
	public function getHeaders() { return $this->oHeaders; }
	public function getCookies() { return $this->oCookies; }
	
	//************************************************************************************
	/**
	 * @return HTTPSession
	 */
	public function getSession() {
		return null;
	}
	
}

?>