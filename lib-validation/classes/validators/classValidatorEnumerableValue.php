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


class ValidatorEnumerableValue implements IValidator {
	
	const CODE = 1120;
	const TXT_EMPTY = '[i18n=ValidatorEnumerableValue.empty]Empty value is invalid in enumeration[/i18n]';
	const TXT_INVALID = '[i18n=ValidatorEnumerableValue.invalid]Invalid enumeration value[/i18n]';
	
	/**
	 * @var IEnumerable
	 */
	private $oEnumerable = null;
	
	/**
	 * @var bool
	 */
	private $allowEmpty = false;
	
	//************************************************************************************
	/**
	 * @param IEnumerable $oEnumerable
	 * @param bool $allowEmpty
	 */
	public function __construct($oEnumerable, $allowEmpty=false) {
		if (!($oEnumerable instanceof IEnumerable)) throw new InvalidArgumentException('oEnumerable is not IEnumerable');
		$this->oEnumerable = $oEnumerable;
		$this->allowEmpty = $allowEmpty;
	}
	
	//************************************************************************************
	/**
	 * @param mixed $value
	 * @param ValidationProcessEntry $oEntry
	 * @param ValidationErrorsCollection $oErrors
	 */
	public function validate($value, $oEntry, $oErrors) {
		if (!$value) {
			if (!$this->allowEmpty) {
				$oErrors->add(new ValidationError('', self::CODE + 1, ComplexString::Create(self::TXT_EMPTY)));
			}
			return;
		}
		
		if (!$this->oEnumerable->enumerableGetAllEnum()->isValid($value)) {
			$oErrors->add(new ValidationError('', self::CODE + 2, ComplexString::Create(self::TXT_INVALID)));
		}
	}
	
}

?>