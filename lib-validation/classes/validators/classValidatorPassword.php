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


class ValidatorPassword implements IValidator {
	
	const CODE = 1200;
	
	const FLAG_NEED_CAP_LETTER = 1;
	const FLAG_NEED_NUMBER = 2;
	const FLAG_NEED_SPECIAL = 4;
	
	const TXT_EMPTY = '[i18n=ValidatorPassword.empty]Empty password given[/i18n]';
	const TXT_LENGTH = '[i18n=ValidatorPassword.length]Minimum allowed password length is [fmt.strong]{minLen:d}[/fmt.strong] characters[/i18n]';
	const TXT_CAPITAL = '[i18n=ValidatorPassword.capital]Password needs capital letter[/i18n]';
	const TXT_DIGIT = '[i18n=ValidatorPassword.digit]Password needs digit[/i18n]';
	const TXT_SPECIAL = '[i18n=ValidatorPassword.special]Password needs special character[/i18n]';
	
	private $flags = 0;
	private $minLen = 0;
	
	//************************************************************************************
	public function __construct($minLen, $flags) {
		$this->minLen = intval($minLen);
		$this->flags = $flags;
		if ($this->minLen < 0) throw new InvalidArgumentException('Invalid minLength value');
	}
	
	//************************************************************************************
	/**
	 * @param mixed $value
	 * @param ValidationProcessEntry $oEntry
	 * @param ValidationErrorsCollection $oErrors
	 */
	public function validate($value, $oEntry, $oErrors) {
		if (!$value) {
			$oErrors->add(new ValidationError('', self::CODE + 1, ComplexString::Create(self::TXT_EMPTY)));
			return;
		}
		
		if (strlen($value) < $this->minLen) {
			$oErrors->add(new ValidationError('', self::CODE + 2, ComplexString::Create(self::TXT_LENGTH, array(
				'minLen' => $this->minLen	
			))));
			return;
		}
		
		if ($this->flags & self::FLAG_NEED_CAP_LETTER) {
			if (!preg_match('/[A-Z]+/', $value)) {
				$oErrors->add(new ValidationError('', self::CODE + 3, ComplexString::Create(self::TXT_CAPITAL)));
			}
		}
		if ($this->flags & self::FLAG_NEED_NUMBER) {
			if (!preg_match('/[0-9]+/', $value)) {
				$oErrors->add(new ValidationError('', self::CODE + 4, ComplexString::Create(self::TXT_DIGIT)));
			}
		}		
		if ($this->flags & self::FLAG_NEED_SPECIAL) {
			if (!preg_match('/[\_\!\@\#\$\%\^\&\*\(\)\-\+\=]+/', $value)) {
				$oErrors->add(new ValidationError('', self::CODE + 5, ComplexString::Create(self::TXT_SPECIAL)));
			}
		}
	}
	
}


?>