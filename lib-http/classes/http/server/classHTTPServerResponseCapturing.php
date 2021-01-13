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


class HTTPServerResponseCapturing implements IHTTPServerResponse, IHTTPClientResponse {
	
	private $isFinished = false;
	private $oHeaders = null;
	private $oCookies = null;
	private $statusCode = 200;
	private $outputFunctions = array();
	private $output = '';
	
	//************************************************************************************
	public function __construct() {
		$this->oHeaders = new PropertiesMap();
		$this->oCookies = new HTTPCookiesContainer();
	}
	
	//************************************************************************************
	public function getStatusCode() {  return $this->statusCode; }
	public function setStatusCode($v) {
		if ($this->isFinished) throw new IllegalStateException('HTTPServerResponseCapturing has been finished');
		$this->statusCode = $v;
	}
	
	//************************************************************************************
	public function pushOutputFunction($func) {
		if (!($func instanceof Closure)) throw new InvalidArgumentException('func is not Closure');
		if ($this->isFinished) throw new IllegalStateException('HTTPServerResponseCapturing has been finished');
		$this->outputFunctions[] = $func;
	}
	
	//************************************************************************************
	/**
	 * @return PropertiesMap
	 */
	public function getHeaders() { return $this->oHeaders; }
	
	//************************************************************************************
	/**
	 * @return HTTPCookiesContainer
	 */
	public function getCookies() { return $this->oCookies; }
	
	//************************************************************************************
	public function getContentType() { return $this->oHeaders->getOne('Content-Type'); }
	public function setContentType($v) {
		if ($this->isFinished) throw new IllegalStateException('HTTPServerResponseCapturing has been finished');
		$this->oHeaders->putSingle('Content-Type', $v);
	}	
	
	//************************************************************************************
	public function getContentLength() {
		if (!$this->isFinished) throw new IllegalStateException('HTTPServerResponseCapturing is not finished');
		return strlen($this->output); 
	}
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getContentString() {
		if (!$this->isFinished) throw new IllegalStateException('HTTPServerResponseCapturing is not finished');
		return $this->output;
	}
	
	//************************************************************************************
	/**
	 * @return array
	 */
	public function getContentJSON() {
		return @json_decode($this->getContentString(), true);
	}
	
	//************************************************************************************
	/**
	 * @return BinaryData
	 */
	public function getContentBinary() {
		return new BinaryData($this->getContentString());
	}
	
	//************************************************************************************
	public function finish() {
		if ($this->isFinished) throw new IllegalStateException('HTTPServerResponseCapturing has been already finished');
		
		$this->isFinished = true;
		ob_start();
		
		foreach($this->outputFunctions as $func) {
			$func();
		}
		
		$this->output = ob_get_contents();
		ob_end_clean();
	}
		
}

?>