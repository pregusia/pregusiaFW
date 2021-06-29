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


class APIApplicationComponent extends ApplicationComponent {
	
	const STAGE = 90;
	
	/**
	 * @var RemoteServiceSupplier[]
	 */
	private $suppliers = array();
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getName() { return "api"; }
	
	//************************************************************************************
	/**
	 * @return int[]
	 */
	public function getStages() { return array(self::STAGE); }

	//************************************************************************************
	/**
	 * @return RemoteServiceSupplier[]
	 */
	public function getSuppliers() { return $this->suppliers; }
	
	//************************************************************************************
	/**
	 * @param int $stage
	 */
	public function onInit($stage) {
		if ($stage == self::STAGE) {
			foreach(CodeBase::getClassesExtending('RemoteServiceSupplier') as $oClass) {
				if ($oClass->isAbstract()) continue;
				$this->suppliers[] = $oClass->getInstance();
			}
			
			$this->initRegistry();
		}
	}

	//************************************************************************************
	/**
	 * @param int $stage
	 */
	public function onProcess($stage) {
		if (!$this->getApplicationContext()->isEnvironmentWeb()) return;
		if (!$this->getBaseLocations()) return;
		
		if ($stage == self::STAGE) {
			$oHttpRequest = $this->getService('IHTTPServerRequest');
			false && $oHttpRequest = new IHTTPServerRequest();
			
			$oHttpResponse = $this->getService('IHTTPServerResponse');
			false && $oHttpResponse = new IHTTPServerResponse();
			
			if ($oHttpRequest && $oHttpResponse) {
				
				if ($this->processApiCall($oHttpRequest, $oHttpResponse)) {
					// jesli api przetworzylo request, to web.core nie ma sensu uruchamiac
					$this->getApplicationContext()->disableComponent('web.core');
				}
				
			}
		}
		
	}
	
	//************************************************************************************
	/**
	 * Zwraca polozenia dla ktorych API ma byc aktywne
	 * @return string[]
	 */
	public function getBaseLocations() {
		$arr = array();
		$v = $this->getConfig()->getValue('server.location');
		
		if (is_array($v)) {
			foreach($v as $e) {
				$tmp = rtrim($e,'/');
				if ($tmp) {
					$arr[] = $tmp;
				}
			}
		} else {
			$tmp = rtrim(trim($v),'/');
			if ($tmp) {
				$arr[] = $tmp;
			} 
		}
		
		return $arr;
	}
	
	//************************************************************************************
	/**
	 * @return RemoteServiceAuthAcceptor
	 */
	public function getDefaultAuthAcceptor() {
		$val = $this->getConfig()->getValue('server.authAcceptor.default');
		if ($val) {
			if (!is_array($val)) {
				throw new ConfigurationException('Config value api.authAcceptor.default is not string[]');
			}
			return RemoteServiceAuthAcceptor::CreateFromJSON($val);
		} else {
			// nie jest sprecyzowane, wiec tworzymy taki, ktory przyjmuje wsio
			return RemoteServiceAuthAcceptor::CreateAcceptingAll();
		} 		
	}
	
	//************************************************************************************
	/**
	 * @param RemoteServiceSupplier $oSupplier
	 * @return RemoteServiceAuthAcceptor
	 */
	public function getAuthAcceptorForSupplier($oSupplier) {
		if (!($oSupplier instanceof RemoteServiceSupplier)) throw new InvalidArgumentException('oSupplier is not RemoteServiceSupplier');
		
		$ifaceName = $oSupplier->getSuppliedInterfaceName();
		if ($ifaceName) {
			$confKey = sprintf('server.authAcceptor.%s', $ifaceName);
			$val = $this->getConfig()->getValue($confKey);
			if ($val) {
				if (!is_array($val)) {
					throw new ConfigurationException(sprintf('Config value %s is not string[]', $confKey));
				}
				return RemoteServiceAuthAcceptor::CreateFromJSON($val);
			} else {
				// nie jest sprecyzowane -> zwracamy domyslny
				return $this->getDefaultAuthAcceptor();
			}
			
		} else {
			return $this->getDefaultAuthAcceptor();
		}
	}
	
	//************************************************************************************
	/**
	 * @return IRemoteServiceAuthData
	 */
	public function getDefaultClientAuth() {
		if ($v = $this->getConfig()->getValue('client.defaultAuth')) {
			return RemoteServiceAuthDataFactory::UnserializeJSON($v);
		} else {
			return null;
		}
	}
	
	//************************************************************************************
	/**
	 * Tworzy link do uslugi API
	 * @param string $p1
	 * @param string $p2
	 * @param string $p3
	 * @param string $p4
	 * @param string $p5
	 * @param string $p6
	 * @return string
	 */
	public function createLink($p1='',$p2='',$p3='',$p4='',$p5='',$p6='') {
		$arr = array();
		$arr[] = UtilsArray::getFirst($this->getBaseLocations());
		if ($p1) $arr[] = $p1;
		if ($p2) $arr[] = $p2;
		if ($p3) $arr[] = $p3;
		if ($p4) $arr[] = $p4;
		if ($p5) $arr[] = $p5;
		if ($p6) $arr[] = $p6;
		return implode('/', $arr);
	}
	
	//************************************************************************************
	/**
	 * @param IHTTPServerRequest $oHttpRequest
	 * @param IHTTPServerResponse $oHttpResponse
	 */
	private function processApiCall($oHttpRequest, $oHttpResponse) {
		$pathParts = array();
		$currentURL = $oHttpRequest->getRequestURL();
		$found = false;
		foreach($this->getBaseLocations() as $loc) {
			$loc .= '/';
			if (substr($currentURL,0,strlen($loc)) == $loc) {
				// to jest to!
				$url = substr($currentURL, strlen($loc));
				$url = trim($url,'/');
				
				$pathParts = explode('/',$url);
				$found = true;
			}
		}
		
		if (!$found) return false;
		$requestId = null;
		
		try {
			if ($oHttpRequest->getMethod() != HTTPMethod::POST) throw new APIProcessingErrorException('Invalid HTTP method', APIProcessingErrorException::CODE_SERVER_REQUEST_INVALID_HTTP_METHOD, 405);
			if (strpos($oHttpRequest->getHeaders()->getOneIgnoringCase('Content-Type'),'application/json') === false) throw new APIProcessingErrorException('Invalid content type', APIProcessingErrorException::CODE_SERVER_REQUEST_INVALID_CONTENT_TYPE, 415);
			
			$jsonRequest = @json_decode($oHttpRequest->getRequestContent(), true);
			if (!is_array($jsonRequest)) throw new APIProcessingErrorException('Invalid JSONRPC request - could not parse', APIProcessingErrorException::CODE_SERVER_REQUEST_JSON_PARSE_ERROR, 500);
			
			$requestId = intval($jsonRequest['id']);
			$oAuthData = null;
			
			if ($jsonRequest['jsonrpc'] != '2.0') throw new APIProcessingErrorException('Invalid JSONRPC request - not jsonrpc object', APIProcessingErrorException::CODE_SERVER_REQUEST_JSON_PARSE_ERROR, 500);
			if (!is_array($jsonRequest['params'])) throw new APIProcessingErrorException('Invalid JSONRPC request - invalid params', APIProcessingErrorException::CODE_SERVER_REQUEST_JSON_PARSE_ERROR, 500);
			if (!is_int($jsonRequest['id'])) throw new APIProcessingErrorException('Invalid JSONRPC request - invalid id', APIProcessingErrorException::CODE_SERVER_REQUEST_JSON_PARSE_ERROR, 500);
			
			// auth parse
			if ($headerValue = $oHttpRequest->getHeaders()->getOneIgnoringCase('Authorization')) {
				$oAuthData = RemoteServiceAuthDataFactory::UnserializeHeader($headerValue);
			}
			if (isset($jsonRequest['auth']) && !$oAuthData) {
				$oAuthData = RemoteServiceAuthDataFactory::UnserializeJSON($jsonRequest['auth']);
			}
			
			$oRequest = new APIServerRequest($jsonRequest, $pathParts, $oAuthData);
			
			$oMatchingSupplier = null;
			foreach($this->getSuppliers() as $oSupplier) {
				if ($oSupplier->supplierMatches($oRequest)) {
					$oMatchingSupplier = $oSupplier;
					break;
				}
			}
			if (!$oMatchingSupplier) throw new APIProcessingErrorException('Supplier not found', APIProcessingErrorException::CODE_SERVER_SUPPLIER_NOT_FOUND, 404);
			
			
			// ok, to teraz przez acceptor i sprawdzamy
			if (true) {
				$oAcceptor = $this->getAuthAcceptorForSupplier($oMatchingSupplier);
				if (!$oAcceptor->isIPAccepted($oHttpRequest->getRemoteAddr())) {
					throw new SecurityException(sprintf('Source IP %s is not allowed', $oHttpRequest->getRemoteAddr()));
				}
				if (!$oAcceptor->isAccepted($oAuthData)) {
					throw new SecurityException('Given authorization is not accepted');
				}
			} 
			
			$jsonReturnValue = $oMatchingSupplier->supplierProcess($this, $oRequest);
			
			$oHttpResponse->setContentType('application/json');
			$oHttpResponse->setStatusCode(200);
			$oHttpResponse->pushOutputFunction(function() use ($jsonReturnValue, $requestId) {
				printf('%s', json_encode(array(
					'jsonrpc' => '2.0',
					'id' => $requestId,
					'result' => $jsonReturnValue,
				)));
			});
			return true;
			
		} catch(Exception $e) {
			$oClass = new ReflectionClass($e);
			$httpCode = 500;
			
			if ($oClass->hasMethod('apiHttpCode')) {
				$httpCode = intval($oClass->getMethod('apiHttpCode')->invoke($e));
			}
			if ($e instanceof SecurityException) {
				$httpCode = 403;
			}
			
			$jsonResponse = array(
				'jsonrpc' => '2.0',
				'id' => $requestId,
				'error' => array(
					'code' => 0,
					'message' => '',
					'data' => array(
					)
				)
			);
			
			if ($e instanceof JsonSerializable) {
				$tmp = $e->jsonSerialize();
				foreach($tmp as $k => $v) {
					$jsonResponse['error']['data'][$k] = $v;
				}
			}
			
			if ($e->getCode() != 0) {
				$jsonResponse['error']['code'] = $e->getCode();
			} else {
				$code = 1000;
				for($i=0;$i<strlen($oClass->getName());++$i) {
					$code += ord(substr($oClass->getName(), $i, 1));
				}
				$jsonResponse['error']['code'] = $code;
			}
			
			$jsonResponse['error']['message'] = $e->getMessage();
			$jsonResponse['error']['data']['exceptionType'] = get_class($e);
			
			if ($this->getConfig()->getValue('server.includeExceptionTrace')) {
				$jsonResponse['error']['data']['exceptionTrace'] = $e->getTraceAsString();
			}
			
			
			$oHttpResponse->setContentType('application/json');
			$oHttpResponse->setStatusCode($httpCode);
			$oHttpResponse->pushOutputFunction(function() use ($jsonResponse) {
				printf('%s', json_encode($jsonResponse));
			});
			
			if ($this->getConfig()->getValue('server.logExceptions')) {
				Logger::warn('APIApplicationComponent.processApiCall', $e);
			}
			
			return true;
		}
	}
	
	//************************************************************************************
	private function initRegistry() {
		$oDefaultAuth = $this->getDefaultClientAuth();
		
		foreach($this->getConfig()->getArray('registry') as $name => $config) {
			$serviceClass = '';
			$serviceName = '';
			
			if (strpos($name, '.') !== false) {
				list($a, $b) = explode('.',$name,2);
				$serviceClass = $a;
				$serviceName = $b;
			} else {
				$serviceClass = $name;
			}
			
			$oInterface = CodeBase::getInterface($serviceClass, false);
			if (!$oInterface) {
				throw new ConfigurationException(sprintf('Interface %s not found during processing api registry for %s',
					$serviceClass,
					$name
				));
			}
			
			if (!$config['url']) throw new ConfigEntryInvalidValueException(sprintf('api.registry.%s.url', $name));
			if (!filter_var($config['url'], FILTER_VALIDATE_URL)) throw new ConfigEntryInvalidValueException(sprintf('api.registry.%s.url', $name));
			
			$oAuthData = $oDefaultAuth;
			
			if (isset($config['auth'])) {
				$oAuthData = RemoteServiceAuthDataFactory::UnserializeJSON($config['auth']);
			}
			
			$oHTTPClient = HTTPClient_CURL::Create($config['url']);
			$oClient = RemoteServiceClient::Create($oInterface, $oHTTPClient, $oAuthData);
			$this->registerService($serviceClass, $serviceName, $oClient);
			
			if (isset($config['cache']) && is_array($config['cache'])) {
				$oClient->enableCache($config['cache']['name'], $config['cache']['methods']);
			}
		}
	}
	
}

?>