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

class HTTPClientRequest_CURL implements IHTTPClientRequest {
	
	private $method = 1;
	private $requestURL = '';
	private $requestContent = '';
	private $sslKeyPath = '';
	private $sslCertPath = '';
	private $sslVerify = true;
	private $outgoingInterface = '';
	
	/**
	 * @var PropertiesMap
	 */
	private $oHeaders = null;
	
	/**
	 * @var HTTPCookiesContainer
	 */
	private $oCookies = null;
	
	/**
	 * @var NameValuePair
	 */
	private $oHTTPAuth = null;
	
	/**
	 * @var PropertiesMap
	 */
	private $oGetParams = null;
	
	
	//************************************************************************************
	public function __construct() {
		$this->oHeaders = new PropertiesMap();
		$this->oCookies = new HTTPCookiesContainer();
		$this->oGetParams = new PropertiesMap();
	}
	
	//************************************************************************************
	/**
	 * @param int $method
	 */
	public function setMethod($method) {
		$this->method = $method;
	}
	
	//************************************************************************************
	/**
	 * @param string $url
	 */
	public function setRequestURL($url) {
		if (!filter_var($url, FILTER_VALIDATE_URL)) throw new InvalidArgumentException('Invalid URL');
		$this->requestURL = $url;
	}
	
	//************************************************************************************
	public function getHeaders() { return $this->oHeaders; }
	public function getCookies() { return $this->oCookies; }	
	
	//************************************************************************************
	/**
	 * @param string $type
	 */
	public function setContentType($type) {
		$this->oHeaders->remove('Content-Type');
		$this->oHeaders->putMulti('Content-Type', $type);
	}

	//************************************************************************************
	/**
	 * @param string $content
	 */
	public function setRequestContent($content) {
		$this->requestContent = $content;
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @param string $value
	 */
	public function setPOSTParameter($name, $value) {
		if ($this->method != HTTPMethod::POST) throw new IllegalStateException('Request method is not POST');
		if (!$name) throw new InvalidArgumentException('Empty name');
		
		if (!$this->oHeaders->contains('Content-Type')) {
			$this->oHeaders->putMulti('Content-Type', 'application/x-www-form-urlencoded');
		} else {
			$val = $this->oHeaders->getOne('Content-Type');
			if ($val != 'application/x-www-form-urlencoded') {
				throw new IllegalStateException('Content type is not application/x-www-form-urlencoded');
			}
		}
		
		if ($this->requestContent) {
			$this->requestContent .= sprintf('&%s=%s', $name, urlencode($value));
		} else {
			$this->requestContent = sprintf('%s=%s', $name, urlencode($value));
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @param string $value
	 */
	public function setGETParameter($name, $value) {
		if (!$name) throw new InvalidArgumentException('Empty name');
		$value = trim($value);
		
		$this->oGetParams->putSingle($name, $value);
	}
	
	//************************************************************************************
	/**
	 * @param string $path
	 */
	public function setSSLKeyPath($path) {
		$this->sslKeyPath = $path;
	}
	
	//************************************************************************************
	/**
	 * @param string $path
	 */
	public function setSSLCertPath($path) {
		$this->sslCertPath = $path;
	}
	
	//************************************************************************************
	/**
	 * @param bool $v
	 */
	public function setSSLVerify($v) {
		$this->sslVerify = $v ? true : false;
	}
	
	//************************************************************************************
	/**
	 * @param NameValuePair $oUserAndPassword
	 */
	public function setHTTPAuth($oUserAndPassword) {
		if ($oUserAndPassword) {
			if (!($oUserAndPassword instanceof NameValuePair)) throw new InvalidArgumentException('oUserAndPassword is not NameValuePair');
			$this->oHTTPAuth = $oUserAndPassword;
		} else {
			$this->oHTTPAuth = null;
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $iface
	 */
	public function setOutgoingInterface($iface) {
		$this->outgoingInterface = $iface;
	}
	
	//************************************************************************************
	private function getFinalRequestURL() {
		if (!$this->requestURL) throw new IllegalStateException('requestURL not set');
		
		$queryVars = array();
		$query = parse_url($this->requestURL, PHP_URL_QUERY);
		if ($query) {
			parse_str($query, $queryVars);
		}
		
		foreach($this->oGetParams->getNameValuePairs() as $oPair) {
			$queryVars[$oPair->getName()] = $oPair->getValue();
		}
		
		$url = $this->requestURL;
		if (strpos($url, '?') !== false) {
			$url = strstr($url, '?', true);
		}
		
		if ($queryVars) {
			$arr = array();
			foreach($queryVars as $k => $v) {
				$arr[] = sprintf('%s=%s', urlencode($k), urlencode($v));
			}
			
			$url .= '?' . implode('&', $arr);
		}
		 
		return $url;
	}
	
	//************************************************************************************
	/**
	 * @param int $timeoutSecs
	 * @return IHTTPClientResponse
	 */
	public function run($timeoutSecs=0) {
		//Logger::debug(sprintf('[HTTPClientRequest_CURL] %s finalUrl=%s', $this->method, $this->getFinalRequestURL()));
		
		$ch = curl_init($this->getFinalRequestURL());
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		
		if (!$this->oHeaders->isEmpty() || !$this->oCookies->isEmpty()) {
			$headers = array();
			foreach($this->oHeaders->getNameValuePairs() as $oPair) {
				$headers[] = sprintf('%s: %s', $oPair->getName(), $oPair->getValue());
			}
			if (!$this->oCookies->isEmpty()) {
				$headers[] = sprintf('Cookie: %s', $this->oCookies->getHeaderString());
			}
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}
		
		if ($this->sslCertPath) {
			curl_setopt($ch, CURLOPT_SSLCERT, $this->sslCertPath);
		}
		if ($this->sslKeyPath) {
			curl_setopt($ch, CURLOPT_SSLKEY, $this->sslKeyPath);
		}
		if ($this->sslVerify) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);			
		} else {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		}
		if ($this->outgoingInterface) {
			curl_setopt($ch, CURLOPT_INTERFACE, $this->outgoingInterface);
		}
		if ($this->oHTTPAuth) {
			curl_setopt($ch, CURLOPT_USERPWD, sprintf('%s:%s', $this->oHTTPAuth->getName(), $this->oHTTPAuth->getValue()));
		}
		
		
		if ($this->method == HTTPMethod::POST) {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->requestContent);
		}
		elseif ($this->method == HTTPMethod::GET) {
			
		}
		elseif ($this->method == HTTPMethod::DELETE) {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		}
		elseif ($this->method == HTTPMethod::HEAD) {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'HEAD');
			curl_setopt($ch, CURLOPT_NOBODY, true);
		}
		elseif ($this->method == HTTPMethod::PUT) {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->requestContent);
		}
		elseif ($this->method == HTTPMethod::PATCH) {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->requestContent);
		}
		else {
			throw new IllegalStateException('Invalid HTTP method');
		}
		
		if ($timeoutSecs != 0) {
			curl_setopt($ch,CURLOPT_TIMEOUT,$timeoutSecs); 
		}
		
		$res = curl_exec($ch);
		if ($res === false) {
			$errStr = curl_error($ch);
			$errNr = curl_errno($ch);
			curl_close($ch);
			throw new IOException(sprintf('CURL erro %d - %s', $errNr, $errStr));			
		}
		
		
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$headersSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$headersString = substr($res, 0, $headersSize);
		$content = substr($res, $headersSize);
		
		//Logger::debug('HTTPClientRequest_CURL',null,array(
		//	'requestURL' => $this->getFinalRequestURL(),
		//	'responseCode' => $httpCode,
		//	'responseContent' => $res
		//));
		
		$oResponseHeaders = new PropertiesMap();
		foreach(explode("\n", $headersString) as $line) {
			$line = trim($line);
			if (!$line) continue;
			list($a, $b) = explode(':',$line,2);
			$a = trim($a);
			$b = trim($b);
			
			if ($a && $b) {
				$oResponseHeaders->putMulti($a, $b);
			}
		}
		
		return new HTTPClientResponse_CURL($httpCode, $content, $oResponseHeaders);
	}
	
	//************************************************************************************
	public function runAsync($oCallback) {
		if (!CodeBase::hasLibrary('lib-async')) throw new RequirementException('lib-async is required for async operations');
		
		if ($oCallback) {
			if (!($oCallback instanceof ICallback)) throw new InvalidArgumentException('oCallback is not ICallback');
		}
		
		$oEvent = new AsyncEventRunnable_HTTPClientCURL();
		$oEvent->setCallback($oCallback);
		$oEvent->setHTTPAuth($this->oHTTPAuth);
		$oEvent->setMethod($this->method);
		$oEvent->setOutgoingInterface($this->outgoingInterface);
		$oEvent->setRequestContent($this->requestContent);
		$oEvent->setRequestURL($this->requestURL);
		$oEvent->setSSLCertPath($this->sslCertPath);
		$oEvent->setSSLKeyPath($this->sslKeyPath);
		$oEvent->setSSLVerify($this->sslVerify);
		$oEvent->getGetParams()->putMultiPairs($this->oGetParams->getNameValuePairs());
		$oEvent->getHeaders()->putMultiPairs($this->getHeaders()->getNameValuePairs());
		
		foreach($this->getCookies()->getCookies() as $oCookie) {
			$oEvent->getCookies()->set($oCookie);
		}
		
		$oAsyncComponent = ApplicationContext::getCurrent()->getComponent('async');
		false && $oAsyncComponent = new AsyncEventsApplicationComponent();
		
		$oAsyncComponent->dispatchEvent(5, $oEvent);
	}
	
}

?>