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


class ValidationProcess implements IteratorAggregate {
	
	/**
	 * @var ValidationProcessEntry[]
	 */
	private $entries = array();
	
	//************************************************************************************
	public function __construct() {
		
	}
	
	//************************************************************************************
	/**
	 * @param IValidatable $obj
	 * @return bool
	 */
	public function add($obj) {
		if ($obj instanceof IValidatable) {
			$obj->validationFill($this);
			return true;
		} else {
			return fale;
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $fieldName
	 * @param mixed $value
	 * @param IValidator $oValidator
	 * @return ValidationProcessEntry
	 */
	public function addEntry($fieldName, $value, $oValidator) {
		if (!($oValidator instanceof IValidator)) throw new InvalidArgumentException('oValidator is not IValidator');
		$oEntry = new ValidationProcessEntry($fieldName, $value, $oValidator);
		$this->entries[] = $oEntry;
		return $oEntry;
	}
	
	//************************************************************************************
	/**
	 * @return ValidationErrorsCollection
	 */
	public function process($onlyFields=false) {
		$oErrors = new ValidationErrorsCollection();
		foreach($this->entries as $oEntry) {
			if (is_array($onlyFields)) {
				if (!in_array($oEntry->getFieldName(), $onlyFields)) continue;
			}
			
			$oEntry->validate($oErrors);
		}
		return $oErrors;
	}
	
	//************************************************************************************
	/**
	 * @return ValidationProcessEntry[]
	 */
	public function getEntries() {
		return $this->entries;
	}
	
	//************************************************************************************
	public function getIterator() {
		return new ArrayIterator($this->entries);
	}
	
	
}

?>