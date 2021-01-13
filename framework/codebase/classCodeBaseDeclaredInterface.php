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


class CodeBaseDeclaredInterface extends CodeBaseDeclaredType {
	
	private $oReflectionType = null;
	private $allInstances = false;
	
	//************************************************************************************
	public function isClass() { return false; }
	public function isInterface() { return true; }
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
	 * Stwierdza czy ten interfejs dziedziczy podany
	 * @param string $ifaceName
	 */
	public function isExtending($ifaceName) {
		return in_array($ifaceName, $this->getReflectionType()->getInterfaceNames());
	}
	
	//************************************************************************************
	/**
	 * Zwraca wszystkie instancje klas implementujacych ten interfejs
	 * @return object[]
	 */
	public function getAllInstances() {
		if ($this->allInstances === false) {
			$this->allInstances = array();
			foreach(CodeBase::getClassesImplementing($this->getName()) as $oClass) {
				if ($oClass->isAbstract()) continue;
				$this->allInstances[] = $oClass->getInstance();
			}
		}
		return $this->allInstances;
	}
	
	
}

?>