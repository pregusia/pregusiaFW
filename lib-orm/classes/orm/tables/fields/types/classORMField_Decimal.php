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


class ORMField_Decimal extends ORMField {
	
	/**
	 * @var Decimal
	 */
	private $oValue = null;
	
	//************************************************************************************
	public function set($v) {
		if ($v === null) {
			if ($this->getDefinition()->isNullable()) {
				if ($this->oValue !== null) {
					$this->oValue = null;
					$this->changed = true;
				}
				return true;
			}
		}
		if ($v instanceof Decimal) {
			if ($this->oValue) {
				if ($this->oValue->toString() != $v->toString()) {
					$this->oValue = $v;
					$this->changed = true;					
				}
			} else {
				$this->oValue = $v;
				$this->changed = true;
			}
		} else {
			if ($this->oValue) {
				if ($this->oValue->getFloat() != floatval($v)) {
					$this->oValue = new Decimal($v);
					$this->changed = true;
				}
			} else {
				$this->oValue = new Decimal($v);
				$this->changed = true;
			}			
		}
		return true;
	}
	
	//************************************************************************************
	/**
	 * @return Decimal
	 */
	public function get() {
		return $this->oValue;
	}
	
	//************************************************************************************
	/**
	 * @return Decimal
	 */
	public function getDecimal() {
		return $this->oValue;
	}
	
	//************************************************************************************
	public function toSQL($oEscaper) {
		if ($this->oValue === null) {
			if ($this->getDefinition()->isNullable()) return 'NULL';
			return '0';
		}
		return str_replace(',','.',$this->oValue->toString($this->getDefinition()->getPrecision()));
	}
	
	//************************************************************************************
	public function isNull() {
		return $this->oValue === null;
	}
	
	//************************************************************************************
	public function tplRender($oContext) {
		if ($this->isNull()) return '[null]';
		return $this->oValue->toString($this->getDefinition()->getPrecision());
	}
	
	
}

?>