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


class HTTPServerRequestPHP implements IHTTPServerRequest {
	
	private $remoteAddr = '';
	private $remotePort = 0;
	private $requestURI = '';
	private $requestURL = '';
	private $isSecure = false;
	private $host = '';
	private $method = 0;
	private $referer = '';
	private $contentType = '';
	private $requestContent = '';

	/**
	 * @var Configuration
	 */
	private $oSessionConfig = null;
	
	/**
	 * @var PropertiesMap
	 */
	private $oPostParams = null;
	
	/**
	 * @var PropertiesMap
	 */
	private $oGetParams = null;

	/**
	 * @var HTTPCookiesContainer
	 */
	private $oCookies = null;
	
	/**
	 * @var PropertiesMap
	 */
	private $oHeaders = null;
	
	/**
	 * @var HTTPRequestFile[]
	 */
	private $files = array();
	
	/**
	 * @var HTTPSession
	 */
	private $session = null;
	
	//************************************************************************************
	public function getRemoteAddr() { return $this->remoteAddr; }
	public function getRemotePort() { return $this->remotePort; }
	public function getRequestURI() { return $this->requestURI; }
	public function getRequestURL() { return $this->requestURL; }
	public function getHost() { return $this->host; }
	public function isSecure() { return $this->isSecure; }
	public function getMethod() { return $this->method; }
	public function getReferer() { return $this->referer; }
	
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return HTTPRequestFile
	 */
	public function getFile($name) {
		return $this->files[$name];
	}
	
	//************************************************************************************
	public function getPOSTParameter($name) { return $this->oPostParams->getOne($name); }
	public function hasPOSTParameter($name) { return $this->oPostParams->contains($name); }
	public function getPOSTParameters() { return $this->oPostParams; }
	
	//************************************************************************************
	public function getRequestContent() { return $this->requestContent; }
	public function getContentType() { return $this->getHeaders()->getOne('Content-Type'); }
	
	//************************************************************************************
	public function getGETParameter($name) { return $this->oGetParams->getOne($name); }
	public function hasGETParameter($name) { return $this->oGetParams->contains($name); }
	public function getGETParameters() { return $this->oGetParams; }
	
	//************************************************************************************
	public function getHeaders() { return $this->oHeaders; }
	public function getCookies() { return $this->oCookies; }
	
	//************************************************************************************
	/**
	 * @return HTTPSession
	 */
	public function getSession() {
		if (!$this->session) {
			$this->session = new HTTPSession($this, $this->oSessionConfig);
		}
		return $this->session;
	}	
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return string
	 */
	private static function normalizeHeaderName($name) {
		$name = trim($name);
		$res = array();
		foreach(explode('-', $name) as $v) {
			$v = strtolower($v);
			$res[] = ucfirst($v);
		}
		return implode('-',$res);
	}
	
	//************************************************************************************
	/**
	 * @param Configuration $oConfig
	 */
	public function __construct($oConfig=null) {
		$serverRemoteAddr = $_SERVER['REMOTE_ADDR'];
		$serverRemotePort = $_SERVER['REMOTE_PORT'];
		$serverHttps = $_SERVER['HTTPS'];
		$serverPort = $_SERVER['SERVER_PORT'];
		$serverName = $_SERVER['HTTP_HOST'] ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];		
		
		if ($oConfig) {
			if ($oConfig->getValue('handleProxyHeaders')) {
				
				if ($_SERVER['HTTP_X_REAL_IP']) {
					$serverRemoteAddr = $_SERVER['HTTP_X_REAL_IP']; 
				}
				if ($_SERVER['HTTP_X_FORWARDED_SSL']) {
					$serverHttps = $_SERVER['HTTP_X_FORWARDED_SSL'];
				}
				if ($_SERVER['HTTP_X_FORWARDED_PORT']) {
					$serverPort = $_SERVER['HTTP_X_FORWARDED_PORT'];
				}
				if ($_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
					$serverHttps = 'on';
					$serverPort = 443;
				}
			}
			
			if ($oConfig->hasKey('session')) {
				$this->oSessionConfig = $oConfig->getSubConfig('session');
			}
		}
		
		
		
		$this->remoteAddr = $serverRemoteAddr;
		$this->remotePort = $serverRemotePort;
		$this->requestURI = $_SERVER['REQUEST_URI'];
		$this->host = $_SERVER['HTTP_HOST'];
		$this->isSecure = ($serverHttps == "on");
		$this->referer = $_SERVER['HTTP_REFERER'];
		$this->contentType = $_SERVER['CONTENT_TYPE'];
		$this->oCookies = new HTTPCookiesContainer();
		$this->oHeaders = new PropertiesMap();
		$this->oPostParams = new PropertiesMap();
		$this->oGetParams = new PropertiesMap();
		
		foreach($_POST as $k => $v) {
			$this->oPostParams->putMulti($k, $v);
		}
		
		foreach($_GET as $k => $v) {
			$this->oGetParams->putMulti($k, $v);
		}
		

		foreach($_COOKIE as $k => $v) {
			$this->oCookies->set(new HTTPCookie($k, $v));
		}
		
		foreach(getallheaders() as $k => $v) {
			$this->oHeaders->putMulti(self::normalizeHeaderName($k), $v);
		}
		
		switch($_SERVER['REQUEST_METHOD']) {
			case 'GET': $this->method = HTTPMethod::GET; break;
			case 'POST': $this->method = HTTPMethod::POST; break;
			case 'PUT': $this->method = HTTPMethod::PUT; break;
			case 'HEAD': $this->method = HTTPMethod::HEAD; break;
			case 'DELETE': $this->method = HTTPMethod::DELETE; break;
			case 'PATCH': $this->method = HTTPMethod::PATCH; break;
			case 'OPTIONS': $this->method = HTTPMethod::OPTIONS; break;
			default: $this->method = HTTPMethod::UNKNOWN; break;
		}
		
		if (true) {
	 		$url = $this->isSecure() ? 'https' : 'http';
	 		$port = $this->isSecure() ? 443 : 80;
	 		
	 		$url .= "://";
	 		if ($serverPort != $port) {
	  			$url .= $serverName.":".$serverPort.$_SERVER["REQUEST_URI"];
	 		} else {
	  			$url .= $serverName.$_SERVER["REQUEST_URI"];
	 		}
	 		$this->requestURL = $url;
		}
		
		foreach($_FILES as $name => $file) {
			$oFile = HTTPRequestFile::Create($file);
			if ($oFile) {
				$this->files[$name] = $oFile;
			}
		}
		
		if ($this->method == HTTPMethod::POST || $this->method == HTTPMethod::PUT || $this->method == HTTPMethod::PATCH) {
			$this->requestContent = file_get_contents('php://input');
		}
	}
	

	
}

?>