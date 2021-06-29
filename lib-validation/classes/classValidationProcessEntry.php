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


class ValidationProcessEntry {
	
	use TTagsContainer;
	
	private $fieldName = '';
	private $value = null;
	
	/**
	 * @var IValidator
	 */
	private $oValidator = null;

	//************************************************************************************
	public function getFieldName() { return $this->fieldName; }
	public function getValue() { return $this->value; }
	
	//************************************************************************************
	/**
	 * @return IValidator
	 */
	public function getValidator() { return $this->oValidator; }
	
	//************************************************************************************
	public function __construct($fieldName, $value, $oValidator) {
		if (!($oValidator instanceof IValidator)) throw new InvalidArgumentException('oValidator is not IValidator');
		
		$this->fieldName = trim($fieldName);
		$this->value = $value;
		$this->oValidator = $oValidator;
	}
	
	//************************************************************************************
	/**
	 * @param ValidationErrorsCollection $oErrors
	 * @return bool
	 */
	public function validate($oErrors) {
		if (!($oErrors instanceof ValidationErrorsCollection)) throw new InvalidArgumentException('oErrors is not ValidationErrorsCollection');
		
		$oTmpErrors = new ValidationErrorsCollection();
		$this->getValidator()->validate($this->value, $this, $oTmpErrors);
		
		foreach($oTmpErrors->getErrors() as $oError) {
			$oErrors->add(new ValidationError($this->fieldName, $oError->getErrorCode(), $oError->getErrorText()));
		}
		
		return !$oTmpErrors->hasAny();
	}
	
	//************************************************************************************
	/**
	 * @param mixed $value
	 * @param string $fieldName
	 * @param IValidator $oValidator
	 * @param ValidationErrorsCollection $oErrors
	 * @return bool
	 */
	public static function ValidateSingle($fieldName, $value, $oValidator, $oErrors) {
		if (!($oValidator instanceof IValidator)) throw new InvalidArgumentException('oValidator is not IValidator');
		if (!($oErrors instanceof ValidationErrorsCollection)) throw new InvalidArgumentException('oErrors is not ValidationErrorsCollection');
		
		$oEntry = new ValidationProcessEntry($fieldName, $value, $oValidator);
		return $oEntry->validate($oErrors);
	}	
	
}

?>