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


class HTTPServerResponsePHP implements IHTTPServerResponse {

	/**
	 * @var PropertiesMap
	 */
	private $oHeaders = null;
	
	/**
	 * @var HTTPCookiesContainer
	 */
	private $oCookies = null;
	
	/**
	 * @var int
	 */
	private $statusCode = 200;
	
	/**
	 * @var Closure[]
	 */
	private $outputFunctions = array();

	//************************************************************************************
	public function __construct() {
		$this->oHeaders = new PropertiesMap();
		$this->oCookies = new HTTPCookiesContainer();
	}
	
	//************************************************************************************
	public function setContentType($v) {
		$this->oHeaders->putSingle('Content-Type', $v);
	}

	//************************************************************************************
	public function getStatusCode() { return $this->statusCode; }
	public function setStatusCode($v) { $this->statusCode = $v; }
	
	//************************************************************************************
	public function pushOutputFunction($func) {
		if (!($func instanceof Closure)) throw new InvalidArgumentException('func is not Closure');
		$this->outputFunctions[] = $func;
	}
	
	//************************************************************************************
	public function getHeaders() { return $this->oHeaders; }
	public function getCookies() { return $this->oCookies; }
	
	
	//************************************************************************************
	public function process() {
		if ($this->statusCode) {
			http_response_code($this->statusCode);
		}
		if ($this->contentType) {
			header(sprintf('Content-Type: %s', $this->contentType));
		}
		foreach($this->oHeaders->getNameValuePairs() as $oPair) {
			if ($oPair->getValue()) {
				header(sprintf('%s: %s', $oPair->getName(), $oPair->getValue()));
			} else {
				header_remove($oPair->getName());
			}
		}
		foreach($this->oCookies->getCookies() as $oCookie) {
			setcookie(
				$oCookie->getName(),
				$oCookie->getValue(),
				$oCookie->getExpire() > 0 ? $oCookie->getExpire() : null,
				$oCookie->getPath() ? $oCookie->getPath() : null,
				$oCookie->getDomain() ? $oCookie->getDomain() : null,
				$oCookie->isSecure() ? true : null,
				$oCookie->isHTTPOnly() ? true : null
			);
		}
		foreach($this->outputFunctions as $func) {
			$func();
		}
	}
	
}

?>