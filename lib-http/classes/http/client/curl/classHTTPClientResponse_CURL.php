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

class HTTPClientResponse_CURL implements IHTTPClientResponse {
	
	private $statusCode = 0;
	private $content = '';
	
	/**
	 * @var PropertiesMap
	 */
	private $oHeaders = null;
	
	/**
	 * @var HTTPCookiesContainer
	 */
	private $oCookies = null;
	
	//************************************************************************************
	/**
	 * @param int $statusCode
	 * @param string $content
	 * @param PropertiesMap $oHeaders
	 */
	public function __construct($statusCode, $content, $oHeaders) {
		if (!($oHeaders instanceof PropertiesMap)) throw new InvalidArgumentException('oHeaders is not PropertiesMap');
		$this->statusCode = intval($statusCode);
		$this->content = $content;
		$this->oHeaders = $oHeaders;
		
		$this->oCookies = new HTTPCookiesContainer();
		// TODO: parse cookies
		$this->oHeaders->remove('Cookie');
	}

	//************************************************************************************
	/**
	 * @return int
	 */
	public function getStatusCode() {
		return $this->statusCode;
	}
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getContentType() {
		return $this->oHeaders->getOne('Content-Type');
	}
	
	//************************************************************************************
	/**
	 * @return int
	 */
	public function getContentLength() {
		return strlen($this->content);
	}
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getContentString() {
		return $this->content;
	}
	
	//************************************************************************************
	/**
	 * @return array
	 */
	public function getContentJSON() {
		return @json_decode($this->content, true);
	}

	//************************************************************************************
	/**
	 * @return BinaryData
	 */
	public function getContentBinary() {
		return new BinaryData($this->content);
	}
	
	//************************************************************************************
	public function getHeaders() { return $this->oHeaders; }
	public function getCookies() { return $this->oCookies; }
	
}

?>