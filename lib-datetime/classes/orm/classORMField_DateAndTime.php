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


/**
 * 
 * @author pregusia
 * @NeedLibrary lib-orm
 *
 */
class ORMField_DateAndTime extends ORMField {

	/**
	 * @var DateAndTime
	 */
	private $oValue = null;
	
	//************************************************************************************
	/**
	 * @param mixed $v
	 * @return DateAndTime
	 */
	private function prepare($v) {
		if ($v) {
			if ($v instanceof DateAndTime) return $v;
			if (is_string($v)) return DateAndTime::FromString($v);
			if (is_int($v)) return DateAndTime::FromTimestamp($v);
		}
		
		return null;
	}
	
	//************************************************************************************
	public function set($v) {		
		$oNewValue = $this->prepare($v);
		if (!UtilsComparable::isEqual($this->oValue, $oNewValue)) {
			$this->oValue = $oNewValue;
			$this->changed = true;
		}
		return true;
	}
	
	//************************************************************************************
	public function setNow() {
		$this->set(DateAndTime::Now());
	}
	
	//************************************************************************************
	public function get() {
		if ($this->oValue) {
			return $this->oValue->toString();
		} else {
			return '';
		}
	}
	
	//************************************************************************************
	/**
	 * @return Date
	 */
	public function getDate() {
		if ($this->oValue) {
			return $this->oValue->getDate();
		} else {
			return null;
		}
	}
	
	//************************************************************************************
	/**
	 * @return Time
	 */
	public function getTime() {
		if ($this->oValue) {
			return $this->oValue->getTime();
		} else {
			return null;
		}
	}
	
	//************************************************************************************
	/**
	 * @return DateAndTime
	 */
	public function getDateAndTime() { return $this->oValue; }
	
	//************************************************************************************
	public function toSQL($oEscaper) {
		if ($this->oValue === null) {
			if ($this->getDefinition()->isNullable()) return 'NULL';
			return '"0000-00-00 00:00:00"';
		} else {
			return sprintf('"%s"', $this->oValue->toString());
		}
	}
	
	//************************************************************************************
	public function isNull() {
		return $this->oValue === null;
	}
	
}

?>