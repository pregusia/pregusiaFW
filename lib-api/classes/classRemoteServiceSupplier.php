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


abstract class RemoteServiceSupplier {
	
	/**
	 * @var CodeBaseDeclaredInterface[]
	 */
	private $interfaces = false;
	
	/**
	 * @var APIApplicationComponent
	 */
	private $oComponent = null;

	/**
	 * @var APIServerRequest
	 */
	private $oRequest = null;
	
	
	//************************************************************************************
	/**
	 * @return APIApplicationComponent
	 */
	public function getAPIComponent() {
		return $this->oComponent;
	}
	
	//************************************************************************************
	/**
	 * @return ApplicationContext
	 */
	public function getApplicationContext() {
		if ($this->getAPIComponent()) {
			return $this->getAPIComponent()->getApplicationContext();
		} else {
			return ApplicationContext::getCurrent();
		}
	}
	
	//************************************************************************************
	/**
	 * @return APIServerRequest
	 */
	public function getAPIRequest() {
		return $this->oRequest;
	}
	
	//************************************************************************************
	/**
	 * @return RemoteServiceAuthAcceptor
	 */
	public function getAuthAcceptor() {
		return $this->getAPIComponent()->getAuthAcceptorForSupplier($this);
	}
	
	//************************************************************************************
	/**
	 * Stwierdza czy ten supplier pasuje do wywolania
	 * Wywolanie tego jest przed onInit i nie powinno byc w zaden sposob kontekstowe
	 * 
	 * @param APIServerRequest $oRequest
	 * @return bool
	 */
	public abstract function supplierMatches($oRequest);
	
	//************************************************************************************
	/**
	 * Zwraca liste interfejsow API zapewnianych przez ten supplier
	 * @return CodeBaseDeclaredInterface[]
	 */
	private function getInterfaces() {
		
		if ($this->interfaces === false) {
			$oClass = new ReflectionClass($this);
			$arr = array();
			
			foreach($oClass->getInterfaces() as $oInterface) {
				if ($oInterface->implementsInterface('IRemoteService') && $oInterface->getName() != 'IRemoteService')  {
					$arr[$oInterface->getName()] = 1;
				}
			}
			
			// ustala interfejsy ktore sa bezposrednio implementowane
			if (true) {
				$alreadyDeleted = array();
				while(true) {
					
					$tmpArr = $arr;
					foreach($tmpArr as $name => $num) {
						$oInterface = new ReflectionClass($name);
						foreach($oInterface->getInterfaceNames() as $n) {
							if (!in_array($n, $alreadyDeleted)) {
								$arr[$n] += 1;
							}
						}
					}
					
					$toDel = array();
					foreach($arr as $name => $num) {
						if ($num > 2) $toDel[] = $name;
					}
					if (!$toDel) break;
					
					foreach($toDel as $name) {
						unset($arr[$name]);
						$alreadyDeleted[] = $name;
					}
				}
			}
			
			$this->interfaces = array();
			foreach($arr as $name => $n) {
				$this->interfaces[] = CodeBase::getInterface($name);
			}
		}
		
		return $this->interfaces;
	}
	
	//************************************************************************************
	/**
	 * Zwraca nazwe interfejsu zapewnianego przez ten supplier
	 * jesli jest to wiecej niz jeden, to nic nie zwraca
	 * @return string
	 */
	public function getSuppliedInterfaceName() {
		$Interfaces = $this->getInterfaces();
		if (count($Interfaces) == 1) {
			return UtilsArray::getFirst($Interfaces)->getName();
		} else {
			return '';
		}
	}
	
	//************************************************************************************
	/**
	 * @param APIServerRequest $oRequest
	 */
	protected function onInit($oRequest) {
		
	}
	
	//************************************************************************************
	/**
	 * @param APIApplicationComponent $oComponent
	 * @param APIServerRequest $oRequest
	 * @return array
	 */
	public function supplierProcess($oComponent, $oRequest) {
		$this->oComponent = $oComponent;
		$this->oRequest = $oRequest;
		$jsonRequest = $oRequest->getRawJSONRequest();
		
		
		$methodName = trim($jsonRequest['method']);
		if (!$methodName) throw new APIProcessingErrorException('Method not found', APIProcessingErrorException::CODE_SERVER_METHOD_NOT_FOUND, 404);
			
		if ($methodName == '__testMethod') {
			return true;
			
		} else {
			$oMethod = null;
			foreach($this->getInterfaces() as $oInterface) {
				if ($oInterface->getReflectionType()->hasMethod($methodName)) {
					$oMethod = $oInterface->getReflectionType()->getMethod($methodName);
					break;
				}
			}
			if (!$oMethod) throw new APIProcessingErrorException('Method not found', APIProcessingErrorException::CODE_SERVER_METHOD_NOT_FOUND, 404);
	
			$oThisClass = new ReflectionClass($this);
			$oMethodAnnotations = CodeBaseAnnotationsCollection::ParseDocComment($oMethod->getDocComment());
	
			$methodInvokeArgs = array();
			foreach($oMethod->getParameters() as $oParam) {
				false && $oParam = new ReflectionParameter();
				$paramName = trim($oParam->getName(),'$');
				$paramValue = $jsonRequest['params'][$paramName];
				$paramType = $oMethodAnnotations->getParameterType($paramName);
				
				$methodInvokeArgs[] = UtilsAPI::fromJSON($paramValue, $paramType);
			}
			
			
			$this->onInit($oRequest);
			$returnValue = $oThisClass->getMethod($oMethod->getName())->invokeArgs($this, $methodInvokeArgs);
			$returnType = $oMethodAnnotations->getFirst('return') ? $oMethodAnnotations->getFirst('return')->getParam() : '';
			
			$returnValue = UtilsAPI::toJSON($returnValue, $returnType);
	
			return $returnValue;
		}
	}
	
}

?>