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


class ORMTableFieldDefinition {

	private $name = '';
	private $table = null;
	
	private $primary = false;
	private $unique = false;
	private $autoIncrement = false;
	private $nullable = false;
	private $fkZeroAllow = false;
	private $length = 0;
	private $precision = 0;
	private $options = array();

	/**
	 * @var CodeBaseDeclaredClass
	 */
	private $oValuesSource = null;
	
	/**
	 * @var bool
	 */
	private $valuesSourceContextual = false;
	
	/**
	 * @var CodeBaseDeclaredClass
	 */
	private $oFieldType = null;

	//************************************************************************************
	public function getName() { return $this->name; }
	
	//************************************************************************************
	public function isPrimary() { return $this->primary; }
	public function setPrimary($v) { $this->primary = $v; return $this; }
	
	//************************************************************************************
	public function isUnique() { return $this->unique; }
	public function setUnique($v) { $this->unique = $v; return $this; }
	
	//************************************************************************************
	public function isAutoIncrement() { return $this->autoIncrement; }
	public function setAutoIncrement($v) { $this->autoIncrement = $v; return $this; }
	
	//************************************************************************************
	public function getLength() { return $this->length; }
	public function setLength($v) { $this->length = $v; return $this; }
	
	//************************************************************************************
	public function getPrecision() { return $this->precision; }
	public function setPrecision($v) { $this->precision = $v; return $this; }
	
	//************************************************************************************
	public function isNullable() { return $this->nullable; }
	public function setNullable($v) { $this->nullable = $v; return $this; }

	//************************************************************************************
	public function getFkZeroAllow() { return $this->fkZeroAllow; }
	public function setFkZeroAllow($v) { $this->fkZeroAllow = $v; return $this; }
	
	//************************************************************************************
	/**
	 * @return CodeBaseDeclaredClass
	 */
	public function getType() { return $this->oFieldType; }
	
	//************************************************************************************
	public function setType($className) {
		$this->oFieldType = CodeBase::getClass($className);
		return $this;
	}
	
	//************************************************************************************
	public function setOption($k,$v) {
		$this->options[$k] = $v;
	}
	
	//************************************************************************************
	public function getOption($k) {
		return $this->options[$k];
	}
	
	//************************************************************************************
	/**
	 * @return ORMTable
	 */
	public function getTable() { return $this->table; }
	public function setTable($v) { $this->table = $v; return $this; }
	
	//************************************************************************************
	/**
	 * @return CodeBaseDeclaredClass
	 */
	public function getValuesSource() { return $this->oValuesSource; }
	
	//************************************************************************************
	/**
	 * @return boolean
	 */
	public function isValuesSourceContextual() { return $this->valuesSourceContextual; }
	
	//************************************************************************************
	public function setValuesSource($className, $contextual=false) {
		$this->oValuesSource = CodeBase::getClass($className);
		$this->valuesSourceContextual = $contextual;
		return $this;
	}
	
	//************************************************************************************
	/**
	 * Zwraca instancje zrodla danych dla tego pola
	 * @throws IllegalStateException Jesli zrodlo danych jest kontekstowe
	 * @return IEnumerable
	 */
	public function getValuesSourceObject() {
		if (!$this->oValuesSource) return null;
		if ($this->valuesSourceContextual) {
			throw new IllegalStateException('Could not get contextural valuesSource from ORMTableFieldDefinition');
		}
		return $this->getValuesSource()->getInstance();
	}

	//************************************************************************************
	public function __construct($name) {
		$this->name = $name;
	}
	
	//************************************************************************************
	/**
	 * @return ORMField
	 */
	public function createField() {
		$oField = $this->getType()->getInstance();
		$oField->setDefinition($this);
		
		foreach($this->options as $k => $v) {
			$oField->onApplyOption($k, $v);
		}
		
		return $oField;
	}

}

?>