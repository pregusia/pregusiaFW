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


class WebActionDefinition {
	
	const ANNO_NAME = 'WebAction';
	const ANNO_WRAPPER = 'WebActionWrapper';
	const ANNO_RAW_PATH = 'WebActionRawPath';
	
	private $oClass = null;
	private $oMethod = null;
	private $oWrapper = false;
	
	private $parametersTypes = array();
	private $oAnnotations = null;
	
	//************************************************************************************
	/**
	 * @return ReflectionClass
	 */
	public function getClass() { return $this->oClass; }

	//************************************************************************************
	/**
	 * @return ReflectionMethod
	 */
	public function getMethod() { return $this->oMethod; }

	//************************************************************************************
	/**
	 * @return CodeBaseAnnotationsCollection
	 */
	public function getAnnotations() { return $this->oAnnotations; }
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return IWebParameterType
	 */
	public function getParameterType($name) { return $this->parametersTypes[$name]; }

	//************************************************************************************
	/**
	 * @return string[]
	 */
	public function getParameterNames() {
		$arr = array();
		foreach($this->getMethod()->getParameters() as $oParameter) {
			$arr[] = ltrim($oParameter->getName(),'$');
		}
		return $arr;
	}
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getFirstName() {
		$oAnno = $this->getAnnotations()->getFirst(self::ANNO_NAME);
		if ($oAnno) {
			return $oAnno->getParam();
		} else {
			return '';
		}
	}
	
	//************************************************************************************
	/**
	 * @return string[]
	 */
	public function getNames() {
		$arr = array();
		foreach($this->getAnnotations()->getAll(self::ANNO_NAME) as $oAnno) {
			$name = trim($oAnno->getParam());
			if ($name) {
				$arr[] = $name;
			}
		}
		return $arr;
	}
	
	//************************************************************************************
	public function hasName($name) {
		return in_array($name, $this->getNames());
	}
	
	//************************************************************************************
	/**
	 * @return IWebActionWrapper
	 */
	public function getWrapper() {
		if ($this->oWrapper === false) {
			$this->oWrapper = null;
			
			$oAnno = $this->getAnnotations()->getFirst(self::ANNO_WRAPPER);
			if ($oAnno) {
				$className = $oAnno->getParam();
				if ($className) {
					$oClass = CodeBase::getClass($className);
					if (!$oClass->isImplementing('IWebActionWrapper')) {
						throw new IllegalStateException(sprintf('Class %s is not implementing IWebActionWrapper', $className));
					}
					
					$this->oWrapper = $oClass->ctorCreate();
				}
			}
			
		}
		return $this->oWrapper;
	}
	
	//************************************************************************************
	/**
	 * @param string $path
	 * @return bool
	 */
	public function matchesRawPath($path) {
		foreach($this->getAnnotations()->getAll(self::ANNO_RAW_PATH) as $oAnno) {
			if ($oAnno->getParam() == $path) {
				return true;
			}
		}
		return false;
	}
	
	//************************************************************************************
	private function __construct() {
		
	}
	
	//************************************************************************************
	/**
	 * Executes action
	 * @param WebDispatcher $oDispatcher
	 * @param array $params
	 */
	public function execute($oDispatcher, $params) {
		if (!($oDispatcher instanceof WebDispatcher)) throw new InvalidArgumentException('oDispatcher is not WebDispatcher');
		
		$obj = $this->getClass()->newInstanceArgs(array($oDispatcher));
		false && $obj = new WebController();
		
		$oDispatcher->getApplicationContext()->registerService('WebController', '', $obj);
		$obj->onBeforeAction($this, $params);
		
		if ($oWrapper = $this->getWrapper()) {
			$oWrapper->onBegin($this, $params);
		}
		
		$args = array();
		foreach($this->getParameterNames() as $paramName) {
			$oType = $this->getParameterType($paramName);
			$args[] = $oType->unserialize($params[$paramName]);
		}
		
		try {
			$oResponse = $this->getMethod()->invokeArgs($obj, $args);
			
			if ($oWrapper = $this->getWrapper()) {
				$oResponse = $oWrapper->onEnd($this, $oResponse);
			}
			
			return $obj->onAfterAction($this, $oResponse);
		} catch(Exception $e) {
			
			if ($oWrapper = $this->getWrapper()) {
				$res = $oWrapper->onException($this, $e);
				if ($res) {
					return $res;
				}
			}
			
			throw $e;
		}
	}
	
	//************************************************************************************
	/**
	 * @param ReflectionClass $oClass
	 * @param ReflectionMethod $oMethod
	 * @return WebActionDefinition
	 */
	public static function CreateFromMethod($oClass, $oMethod) {
		if (!($oClass instanceof ReflectionClass)) throw new InvalidArgumentException('oClass is not ReflectionClass');
		if (!($oMethod instanceof ReflectionMethod)) throw new InvalidArgumentException('oMethod is not ReflectionMethod');
		
		$obj = new WebActionDefinition();
		$obj->oClass = $oClass;
		$obj->oMethod = $oMethod;
		$obj->oAnnotations = CodeBaseAnnotationsCollection::ParseDocComment($oMethod->getDocComment());
		
		if (!$obj->getNames()) return null;
		
		foreach($obj->getParameterNames() as $paramName) {
			$typeStr = 'raw';
			foreach($obj->getAnnotations()->getAll('param') as $oAnno) {
				if (trim($oAnno->getParam(1),'$') == $paramName) {
					$typeStr = $oAnno->getParam(0);
				}
			}
			
			$obj->parametersTypes[$paramName] = WebParameterTypeHelper::getType($typeStr, true);
		}
		
		return $obj;
	}
	
	//************************************************************************************
	/**
	 * @param CodeBaseDeclaredClass $oClass
	 * @return WebActionDefinition[]
	 */
	public static function ScanClass($oClass) {
		if (!($oClass instanceof CodeBaseDeclaredClass)) throw new InvalidArgumentException('oClass is not CodeBaseDeclaredClass');
		
		if ($oClass->isAbstract()) return array();
		
		$actions = array();
		
		foreach($oClass->getReflectionType()->getMethods() as $oMethod) {
			false && $oMethod = new ReflectionMethod();
			
			if ($oMethod->isStatic()) continue;
			if ($oMethod->isAbstract()) continue;
			if (!$oMethod->isPublic()) continue;
			if (substr($oMethod->getName(), 0, 3) != 'web') continue;
			
			$res = self::CreateFromMethod($oClass->getReflectionType(), $oMethod);
			if ($res) {
				$actions[] = $res;
			}
		}
		
		return $actions;
	}
}

?>