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


class ORMField_Binary extends ORMField {
	
	protected $value = '';
	
	//************************************************************************************
	public function set($v) {
		if ($v === null) {
			if ($this->getDefinition()->isNullable()) {
				if ($this->value !== null) {
					$this->value = null;
					$this->changed = true;
				}
				return true;
			}
		}
		
		if ($v instanceof BinaryData) {
			$v = $v->getData();
		}
		
		if (strval($v) !== $this->value) {
			$this->value = strval($v);
			$this->changed = true;
		}
		return true;
	}
	
	//************************************************************************************
	public function get() {
		return $this->value;
	}
	
	//************************************************************************************
	/**
	 * @return BinaryData
	 */
	public function getBinaryData() {
		return new BinaryData($this->value);
	}
	
	//************************************************************************************
	public function toSQL($oEscaper) {
		if ($this->value === null) {
			if ($this->getDefinition()->isNullable()) return 'NULL';
			return '""';
		}
		return $oEscaper->escapeBinary($this->value);
	}
	
	//************************************************************************************
	public function isNull() {
		return $this->value === null;
	}

}

?>