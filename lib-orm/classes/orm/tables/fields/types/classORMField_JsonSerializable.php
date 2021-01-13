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


class ORMField_JsonSerializable extends ORMField_String {
	
	/**
	 * @var CodeBaseDeclaredClass
	 */
	private $oClass = null;
	
	/**
	 * @var JsonSerializable
	 */
	private $object = false;
	
	//************************************************************************************
	/**
	 * @return CodeBaseDeclaredClass
	 */
	public function getClass() { return $this->oClass; }
	
	//************************************************************************************
	public function newInstance() {
		if (!$this->oClass) throw new IllegalStateException('Class not set');
		return $this->getClass()->getInstance();
	}
	
	//************************************************************************************
	/**
	 * @param JsonSerializable $obj
	 * @throws IllegalStateException
	 * @throws InvalidArgumentException
	 */
	public function setObject($obj) {
		if (!$this->oClass) throw new IllegalStateException('Class not set');
		if (is_object($obj)) {
			if (!$this->getClass()->isInstanceOf($obj)) {
				throw new InvalidArgumentException('$obj is not ' . $this->getClass()->getName());
			}
			
			$this->set(json_encode($obj->jsonSerialize()));
			$this->object = $obj;
			return true;
		} else {
			$this->object = null;
			if ($this->getDefinition()->isNullable()) {
				$this->set(null);
			} else {
				$this->set('null');
			}
		}
	}
	
	//************************************************************************************
	public function set($v) {
		parent::set($v);
		$this->object = false;
	}
	
	//************************************************************************************
	public function getObject() {
		if ($this->object === false) {
			$this->object = null;
			
			$arr = @json_decode($this->get(), true);
			if (is_array($arr)) {
				$this->object = $this->getClass()->callStaticMethod('jsonUnserialize', array($arr));
			}
		}
		return $this->object;
	}
	
	//************************************************************************************
	public function onApplyOption($name, $value) {
		if ($name == 'className') {
			$oClass = CodeBase::getClass($value);
			if (!$oClass->isImplementing('JsonSerializable')) throw new InvalidArgumentException(sprintf('Class %s is not implementing JsonSerializable', $value));
			if (!$oClass->hasStaticMethod('jsonUnserialize')) throw new InvalidArgumentException(sprintf('Class %s is implementing JsonSerializable but dont have jsonUnserialize static method', $value));
			$this->oClass = $oClass;
		}
	}
	
}

?>