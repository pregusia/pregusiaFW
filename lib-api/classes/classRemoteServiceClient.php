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


abstract class RemoteServiceClient {
	
	/**
	 * @var ReflectionClass
	 */
	private $oInterface = null;
	
	/**
	 * @var IHTTPClientRequest
	 */
	private $oHTTPClient = null;
	
	/**
	 * @var IRemoteServiceAuthData
	 */
	private $oAuthData = null;
	
	
	/**
	 * @var ICacheMechanism
	 */
	private $oCache = null;
	
	/**
	 * @var string[]
	 */
	private $cacheMethods = array();
	
	
	//************************************************************************************
	/**
	 * @param ReflectionClass $oInterface
	 * @param IHTTPClientRequest $oHTTPClient
	 * @param IRemoteServiceAuthData $oAuthData
	 * @throws InvalidArgumentException
	 */
	protected function __construct($oInterface, $oHTTPClient, $oAuthData) {
		if (!($oInterface instanceof ReflectionClass)) throw new InvalidArgumentException('oInterface is not ReflectionClass');
		if (!$oInterface->isInterface()) throw new InvalidArgumentException('Given argument is not interface');
		if (!$oInterface->implementsInterface('IRemoteService')) throw new InvalidArgumentException('Given interface not implements IRemoteService');
		$this->oInterface = $oInterface;
		
		if (!($oHTTPClient instanceof IHTTPClientRequest)) throw new InvalidArgumentException('oHTTPClient is not IHTTPClientRequet');
		$this->oHTTPClient = $oHTTPClient;
		
		if ($oAuthData) {
			if (!($oAuthData instanceof IRemoteServiceAuthData)) throw new InvalidArgumentException('oAuthData is not IRemoteServiceAuthData');
			$this->oAuthData = $oAuthData;
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $cacheName
	 * @param string $methods
	 */
	public function enableCache($cacheName, $methods) {
		if (!is_array($methods)) throw new InvalidArgumentException('methods is not array');
		if (!$cacheName) throw new InvalidArgumentException('cacheName is empty');
		
		$oCache = ApplicationContext::getCurrent()->getComponent('cache');
		if (!$oCache) throw new RequirementException('Cache component is required for RemoteServiceClient caching');
		
		$this->oCache = $oCache->getCache($cacheName);
		$this->cacheMethods = $methods;
	}
	
	
	//************************************************************************************
	protected function internalCallMethod($methodName, $args) {
		$oMethod = $this->oInterface->getMethod($methodName);
		if (!$oMethod) throw new IllegalStateException('Method ' . $methodName . ' not found in interface ' . $this->oInterface->getName());
		
		$oMethodAnnotations = CodeBaseAnnotationsCollection::ParseDocComment($oMethod->getDocComment());
		$returnType = $oMethodAnnotations->getFirst('return') ? $oMethodAnnotations->getFirst('return')->getParam() : '';
		
		$jsonArgs = array();
		$idx = 0;
		
		foreach($oMethod->getParameters() as $oParam) {
			false && $oParam = new ReflectionParameter();
			$paramName = trim($oParam->getName(),'$');
			$paramType = $oMethodAnnotations->getParameterType($paramName);
			
			if (isset($args[$idx])) {
				$val = UtilsAPI::toJSON($args[$idx], $paramType);
			} else {
				$val = null;
			}
			
			$jsonArgs[$paramName] = $val;
			$idx += 1;
		}
		
		$res = $this->callRaw($methodName, $jsonArgs);
		return UtilsAPI::fromJSON($res, $returnType);
	}
	
	//************************************************************************************
	/**
	 * @param string $methodName
	 * @param string $jsonRequest
	 * @return string
	 */
	protected function callHTTPOrCache($methodName, $jsonRequest) {
		$cacheKey = '';
		
		if ($this->oCache && in_array($methodName, $this->cacheMethods)) {
			// moze byc cachowany
			
			$tmp = $jsonRequest;
			unset($tmp['id']);
			$chksum = md5(json_encode($tmp));
			$cacheKey = sprintf('%s_%s', $methodName, $chksum);
			
			if ($this->oCache->contains($cacheKey)) {
				$data = $this->oCache->get($cacheKey);
				$arr = @json_decode($data, true);
				if (is_array($arr)) {
					$arr['id'] = $jsonRequest['id'];
					$data = json_encode($arr);
				}
				
				return $data;
			}
		}
		
		
		$this->oHTTPClient->setMethod(HTTPMethod::POST);
		$this->oHTTPClient->setContentType('application/json');
		$this->oHTTPClient->setRequestContent(json_encode($jsonRequest));
		
		if ($this->oAuthData) {
			$headerValue = $this->oAuthData->headerSerialize();
			if ($headerValue) {
				$this->oHTTPClient->getHeaders()->putSingle('Authorization', $headerValue);
			}
		}
		
		$content = $this->oHTTPClient->run()->getContentString();
		
		
		if ($cacheKey && $this->oCache && in_array($methodName, $this->cacheMethods)) {
			$this->oCache->set($cacheKey, $content);
		}
		
		return $content;
	}
	
	//************************************************************************************
	/**
	 * @param string $methodName
	 * @param mixed[] $args
	 * @return mixed
	 */
	protected function callRaw($methodName, $args) {
		if (!is_array($args)) $args = array();
		if (!$methodName) throw new InvalidArgumentException('Empty method name');
		
		$requestId = rand(10, 1000);
		$jsonRequest = array(
			'jsonrpc' => '2.0',
			'id' => $requestId,
			'method' => $methodName,
			'params' => $args,
		);
		
		$content = $this->callHTTPOrCache($methodName, $jsonRequest);
		if (!$content) throw new APIProcessingErrorException('Empty content', APIProcessingErrorException::CODE_CLIENT_RESPONSE_EMPTY);
		
		
		$jsonResponse = @json_decode($content, true);
		if (!is_array($jsonResponse)) throw new APIProcessingErrorException('Invalid content - not parsable', APIProcessingErrorException::CODE_CLIENT_RESPONSE_JSON_PARSE_ERROR);
		if ($jsonResponse['jsonrpc'] != '2.0') throw new APIProcessingErrorException('Not JSONRPC response object', APIProcessingErrorException::CODE_CLIENT_RESPONSE_JSONRPC_VERSION);
		if ($jsonResponse['id'] != $requestId) throw new APIProcessingErrorException('JSONRPC response object id mismatch', APIProcessingErrorException::CODE_CLIENT_RESPONSE_JSONRPC_ID_MISMATCH);

		// parsing response
		if ($jsonResponse['error']) {
			// mamy blad
			$exceptionType = $jsonResponse['error']['data']['exceptionType'];
			$oException = null;
			
			if ($exceptionType) {
				CodeBase::getClass($exceptionType, false);
				if (class_exists($exceptionType, false)) {
					$oExceptionClass = new ReflectionClass($exceptionType);
					if ($oExceptionClass->isSubclassOf('Exception')) {
						
						if ($oExceptionClass->implementsInterface('JsonSerializable')) {
							$oException = call_user_func(array($oExceptionClass->getName(),'jsonUnserialize'), $jsonResponse['error']['data']);
						} else {
							$oException = $oExceptionClass->newInstance();
						}						
					}
				}
			}
			
			if (!$oException) {
				$oException = new RemoteServiceException($jsonResponse['error']);
			}
			
			UtilsExceptions::setCode($oException, $jsonResponse['error']['code']);
			UtilsExceptions::setMessage($oException, $jsonResponse['error']['message']);
			
			throw $oException;			
		}
		
		return $jsonResponse['result'];
	}
	
	//************************************************************************************
	/**
	 * @param ReflectionClass $oInterface
	 * @param CodeBaseDeclaredInterface $oInterface
	 * @param IHTTPClientRequest $oHTTPClient
	 * @param IRemoteServiceAuthData $oAuthData
	 * @return RemoteServiceClient
	 */
	public static function Create($oInterface, $oHTTPClient, $oAuthData=null) {
		if ($oInterface instanceof CodeBaseDeclaredInterface) {
			$oInterface = $oInterface->getReflectionType();
		}
		elseif ($oInterface instanceof ReflectionClass) {
			// ok
		}
		else {
			throw new InvalidArgumentException('oInterface is not ReflectionClass nor CodeBaseDeclaredInterface');
		}
		
		if (!$oInterface->isInterface()) throw new InvalidArgumentException('Given argument is not interface');
		if (!$oInterface->implementsInterface('IRemoteService')) throw new InvalidArgumentException('Given interface not implements IRemoteService');
		if (!($oHTTPClient instanceof IHTTPClientRequest)) throw new InvalidArgumentException('oHTTPClient is not IHTTPClientRequet');

		if ($oAuthData) {
			if (!($oAuthData instanceof IRemoteServiceAuthData)) throw new InvalidArgumentException('oAuthData is not IRemoteServiceAuthData');
		}
		
		$className = sprintf('RemoteServiceClient_%s', $oInterface->getName());
		
		if (!class_exists($className, false)) {
			$code = array();
			$code[] = sprintf('class %s extends RemoteServiceClient implements %s {', $className, $oInterface->getName());
			$code[] = sprintf('public function __construct($a, $b, $c) { parent::__construct($a, $b, $c); }');
			
			foreach($oInterface->getMethods() as $oMethod) {
				false && $oMethod = new ReflectionMethod();

				$nr = 0;
				$argsNames = array();
				foreach($oMethod->getParameters() as $oParameter) {
					$argName = sprintf('$p%02d',$nr);
					if ($oParameter->isDefaultValueAvailable()) {
						$defVal = $oParameter->getDefaultValue();
						switch(strtolower(gettype($defVal))) {
							case 'integer': $argName .= sprintf('=%d', $defVal); break;
							case 'double': $argName .= sprintf('=%.4f', $defVal); break;
							case 'string': $argName .= sprintf('="%s"', $defVal); break;
							case 'boolean': $argName .= sprintf('=%s', $defVal ? 'true' : 'false'); break;
							case 'null': $argName .= '=null';
							default: $argName .= '=null';
						}
					}
					$argsNames[] = $argName;
					$nr += 1;
				}
				
				$code[] = sprintf('public function %s(%s) { $args = func_get_args(); return $this->internalCallMethod("%s", $args); }',
					$oMethod->getName(),
					implode(', ', $argsNames),
					$oMethod->getName()
				);
			}
			
			$code[] = '}';
			
			eval(implode("\n", $code));
		}
		
		$oClass = new ReflectionClass($className);
		return $oClass->newInstanceArgs(array($oInterface, $oHTTPClient, $oAuthData));
	}
	
}

?>