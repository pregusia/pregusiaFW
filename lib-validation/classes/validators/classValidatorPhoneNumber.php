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

class ValidatorPhoneNumber implements IValidator {
	
	const CODE = 1190;
	
	const TXT_INVALID = '[i18n=ValidatorPhoneNumber.invalid]Given phone number is invalid[/i18n]';
	
	private $forceMobile = false;
	
	//************************************************************************************
	public function __construct($forceMobile=false) {
		$this->forceMobile = $forceMobile;
	}
	
	//************************************************************************************
	/**
	 * @param mixed $value
	 * @param ValidationProcessEntry $oEntry
	 * @param ValidationErrorsCollection $oErrors
	 */
	public function validate($value, $oEntry, $oErrors) {
		if (!$value) return;
		
		// same cyfry
		// jedynie na poczatku moze wystapic plus
		// jesli nie sprecyzowano kraju przyjmuje sie ze jest to polska

		if (UtilsString::startsWith($value, '+')) {
			if (UtilsString::startsWith($value, '++')) {
				$oErrors->add(new ValidationError('', self::CODE, ComplexString::Create(self::TXT_INVALID)));
				return;
			}
			
			if (!ctype_digit(substr($value, 1))) {
				$oErrors->add(new ValidationError('', self::CODE, ComplexString::Create(self::TXT_INVALID)));
				return;
			}
			
			if (UtilsString::startsWith($value, '+48') && $this->forceMobile) {
				$nn = substr($value, 3);
				
				// numer PL
				// wiec musi miec 9 znakow
				if (strlen($nn) != 9) {
					$oErrors->add(new ValidationError('', self::CODE, ComplexString::Create(self::TXT_INVALID)));
					return;
				}
			}
			
		} else {
			// po prostu numer
			
			if (!ctype_digit($value)) {
				$oErrors->add(new ValidationError('', self::CODE, ComplexString::Create(self::TXT_INVALID)));
				return;
			}
			
			if ($this->forceMobile) {
				// zakladamy ze to numer PL
				// wiec musi miec 9 znakow
				if (strlen($value) != 9) {
					$oErrors->add(new ValidationError('', self::CODE, ComplexString::Create(self::TXT_INVALID)));
					return;
				}
			}			
		}
	}	
	
}


?>