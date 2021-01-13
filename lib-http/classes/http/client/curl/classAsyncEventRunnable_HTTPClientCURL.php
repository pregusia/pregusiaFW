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

/**
 * 
 * @author pregusia
 * @NeedLibrary lib-async
 *
 */
class AsyncEventRunnable_HTTPClientCURL implements IAsyncEventRunnable {
	
	private $method = 0;
	private $requestURL = "";
	private $requestContent = "";
	private $sslKeyPath = "";
	private $sslCertPath = "";
	private $sslVerify = false;
	private $outgoingInterface = "";
	private $oHeaders = null;
	private $oCookies = null;
	private $oHTTPAuth = null;
	private $oGetParams = null;
	
	/**
	 * @var ICallback
	 */
	private $oCallback = null;
	
	//************************************************************************************
	/**
	 * @return int
	 */
	public function getMethod() { return $this->method; }
	public function setMethod($v) { $this->method = $v; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getRequestURL() { return $this->requestURL; }
	public function setRequestURL($v) { $this->requestURL = $v; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getRequestContent() { return $this->requestContent; }
	public function setRequestContent($v) { $this->requestContent = $v; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getSSLKeyPath() { return $this->sslKeyPath; }
	public function setSSLKeyPath($v) { $this->sslKeyPath = $v; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getSSLCertPath() { return $this->sslCertPath; }
	public function setSSLCertPath($v) { $this->sslCertPath = $v; }
	
	//************************************************************************************
	/**
	 * @return bool
	 */
	public function getSSLVerify() { return $this->sslVerify; }
	public function setSSLVerify($v) { $this->sslVerify = $v; }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getOutgoingInterface() { return $this->outgoingInterface; }
	public function setOutgoingInterface($v) { $this->outgoingInterface = $v; }
	
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
	/**
	 * @return NameValuePair
	 */
	public function getHTTPAuth() { return $this->oHTTPAuth; }
	public function setHTTPAuth($v) { $this->oHTTPAuth = $v; }
	
	//************************************************************************************
	/**
	 * @return PropertiesMap
	 */
	public function getGetParams() { return $this->oGetParams; }

	//************************************************************************************
	/**
	 * @return ICallback
	 */
	public function getCallback() { return $this->oCallback; }
	public function setCallback($v) { $this->oCallback = $v; }
	
	//************************************************************************************
	public function __construct() {
		$this->oHeaders = new PropertiesMap();
		$this->oCookies = new HTTPCookiesContainer();
		$this->oGetParams = new PropertiesMap();
	}
	
	
	//************************************************************************************
	public function jsonSerialize() {
		$arr = array(
			"method" => $this->method,
			"requestURL" => $this->requestURL,
			"requestContent" => $this->requestContent,
			"sslKeyPath" => $this->sslKeyPath,
			"sslCertPath" => $this->sslCertPath,
			"sslVerify" => $this->sslVerify,
			"outgoingInterface" => $this->outgoingInterface,
			"oHeaders" => $this->oHeaders->jsonSerialize(),
			"oCookies" => $this->oCookies->jsonSerialize(),
			"oHTTPAuth" => $this->oHTTPAuth ? $this->oHTTPAuth->jsonSerialize() : null,
			"oGetParams" => $this->oGetParams->jsonSerialize(),
			'callback' => UtilsJSON::serializeAbstraction($this->oCallback)
		);
		
		return $arr;
	}
	
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return self
	 */
	public static function jsonUnserialize($arr) {
		if (!is_array($arr)) return null;
		if ($arr["requestURL"]) {
			$obj = new self();
	
			$obj->method = intval($arr["method"]);
			$obj->requestURL = strval($arr["requestURL"]);
			$obj->requestContent = strval($arr["requestContent"]);
			$obj->sslKeyPath = strval($arr["sslKeyPath"]);
			$obj->sslCertPath = strval($arr["sslCertPath"]);
			$obj->sslVerify = boolval($arr["sslVerify"]);
			$obj->outgoingInterface = strval($arr["outgoingInterface"]);
			$obj->oHeaders = PropertiesMap::jsonUnserialize($arr["oHeaders"]);
			$obj->oCookies = HTTPCookiesContainer::jsonUnserialize($arr["oCookies"]);
			$obj->oHTTPAuth = NameValuePair::jsonUnserialize($arr["oHTTPAuth"]);
			$obj->oGetParams = PropertiesMap::jsonUnserialize($arr["oGetParams"]);
			$obj->oCallback = UtilsJSON::unserializeAbstraction($arr['callback'], CodeBase::getInterface('ICallback'));
			
			return $obj;
		}
		
		return null;
	}
	
	
	
	//************************************************************************************
	public function run() {
		try {
			$oClient = HTTPClient_CURL::Create($this->requestURL);
			$oClient->setMethod($this->method);
			$oClient->setOutgoingInterface($this->outgoingInterface);
			$oClient->setRequestURL($this->requestURL);
			$oClient->setSSLCertPath($this->sslCertPath);
			$oClient->setSSLKeyPath($this->sslKeyPath);
			$oClient->setSSLVerify($this->sslVerify);
			$oClient->setHTTPAuth($this->oHTTPAuth);
			$oClient->getHeaders()->putMultiPairs($this->getHeaders()->getNameValuePairs());
			$oClient->setRequestContent($this->requestContent);
			
			foreach($this->getGetParams()->getNameValuePairs() as $oPair) {
				$oClient->setGETParameter($oPair->getName(), $oPair->getValue());
			}
			
			foreach($this->getCookies()->getCookies() as $oCookie) {
				$oClient->getCookies()->set($oCookie);
			}
			
			$oResponse = $oClient->run();
			
			if ($this->getCallback()) {
				$this->getCallback()->onSuccess($oResponse);
			}
		} catch(Exception $e) {
			Logger::warn("AsyncEventRunnable_HTTPClientCURL::run", $e);
			
			if ($this->getCallback()) {
				$this->getCallback()->onError($e, null);
			}
		}
		
	}
	
	
}

?>