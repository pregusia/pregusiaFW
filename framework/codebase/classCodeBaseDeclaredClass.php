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



class CodeBaseDeclaredClass extends CodeBaseDeclaredType {
	
	private $oReflectionType = null;
	private $oSingletonInstance = null;
	
	//************************************************************************************
	public function isClass() { return true; }
	public function isInterface() { return false; }
	public function isTrait() { return false; }	
	
	//************************************************************************************
	/**
	 * @return ReflectionClass
	 */
	public function getReflectionType() {
		if (!$this->oReflectionType) {
			$this->oReflectionType = new ReflectionClass($this->getName());
		}
		return $this->oReflectionType;
	}
	
	//************************************************************************************
	public function isInstanceOf($obj) {
		if (!$obj) return false;
		return $this->getReflectionType()->isInstance($obj);
	}
	
	//************************************************************************************
	/**
	 * Zwraca wartosc stalej
	 * @param string $name
	 */
	public function getConstantValue($name) {
		$oType = $this->getReflectionType();
		while($oType) {
			if ($oType->hasConstant($name)) return $oType->getConstant($name);
			$oType = $oType->getParentClass();
		}
		return null;
	}
	
	//************************************************************************************
	public function hasConstant($name) {
		$oType = $this->getReflectionType();
		while($oType) {
			if ($oType->hasConstant($name)) return true;
			$oType = $oType->getParentClass();
		}
		return false;
	}
	
	//************************************************************************************
	/**
	 * @return boolean
	 */
	public function isAbstract() {
		return $this->getReflectionType()->isAbstract();
	}
	
	//************************************************************************************
	/**
	 * @param string $ifaceName
	 * @return boolean
	 */
	public function isImplementing($ifaceName) {
		return in_array($ifaceName, $this->getReflectionType()->getInterfaceNames());
	}
	
	//************************************************************************************
	/**
	 * Stwierdza czy ta klasa dziedziczy jakos z $className
	 * @param string $className
	 */
	public function isExtending($className) {
		$oClass = $this->getReflectionType()->getParentClass();
		while($oClass) {
			if ($oClass->getName() == $className) return true;
			$oClass = $oClass->getParentClass();
		}
		return false;
	}
	
	//************************************************************************************
	/**
	 * Stwierdza czy ta klasa jest singletonem
	 * @return boolean
	 */
	public function isSingleton() {
		$oClass = $this->getReflectionType();
		while($oClass) {
			$arr = $oClass->getTraits();
			if ($arr['TSingleton']) return true;
			$oClass = $oClass->getParentClass();
		}
		return false;
	}
	
	//************************************************************************************
	/**
	 * @param string $methodName
	 * @return boolean
	 */
	public function hasStaticMethod($methodName) {
		$oClass = $this->getReflectionType();
		if (!$oClass->hasMethod($methodName)) return false;
		
		$oMethod = $oClass->getMethod($methodName);
		return $oMethod->isStatic();
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return boolean
	 */
	public function hasMethod($name) {
		return $this->getReflectionType()->hasMethod($name);
	}
	
	//************************************************************************************
	public function callStaticMethod($methodName, $args=array()) {
		if ($this->hasStaticMethod($methodName)) {
			return call_user_func_array(array($this->getName(), $methodName), $args);
		} else {
			return null;
		}
	}
	
	//************************************************************************************
	/**
	 * @return IClassInstantinatorAdapter[]
	 */
	public function getMathingInstanceCreationAdapters() {
		$arr = array();
		foreach(CodeBase::getInstantinatorAdapters() as $oAdapter) {
			if ($oAdapter->matches($this)) $arr[] = $oAdapter;
		}
		return $arr;
	}
	
	//************************************************************************************
	private function internalNewInstance($args) {
		if ($this->getReflectionType()->isInstantiable()) {
			return $this->getReflectionType()->newInstanceArgs($args);
		} else {
			throw new IllegalStateException('Class ' . $this->getName() . ' is not instantiable');
		}
	}
	
	//************************************************************************************
	/**
	 * Tworzy nowa instancje tej klasy
	 * Jesli jest ona singletonem, to zwraca globalna instancje
	 * w przeciwnym przypadku korzysta z odpowiednich IClassInstantinatorAdapter
	 * a jesli zaden tego nie obsluzy, to tworzy standardowo 
	 * @throws IllegalStateException
	 */
	public function getInstance() {
		if ($this->isSingleton()) {
			if (!$this->oSingletonInstance) {
				$this->oSingletonInstance = $this->internalNewInstance(array()); 
			}
			return $this->oSingletonInstance;
		}
		
		foreach($this->getMathingInstanceCreationAdapters() as $oAdapter) {
			$inst = $oAdapter->getInstanceOf($this);
			if ($inst) return $inst;
		}
			
		$args = func_get_args();
		return $this->internalNewInstance($args); 
	}
	
	//************************************************************************************
	/**
	 * Tworzy nowa instancje tego obiektu
	 * @param array $args
	 */
	public function ctorCreate($args=array()) {
		if ($this->isSingleton()) throw new IllegalStateException('Class is singleton');
		if ($this->getMathingInstanceCreationAdapters()) throw new IllegalStateException('Class creation is handled by some IClassInstantinatorAdapter');
		
		return $this->getReflectionType()->newInstanceArgs($args);
	}
	
	
}

?>