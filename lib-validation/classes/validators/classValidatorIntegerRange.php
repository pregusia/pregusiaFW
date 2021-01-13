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


class ValidatorIntegerRange implements IValidator {
	
	const CODE = 1100;
	
	const TXT_MIN = '[i18n=ValidatorIntegerRange.min][fmt.strong]{value:d}[/fmt.strong] is lesser than allowed [fmt.strong]{min:d}[/fmt.strong][/i18n]';
	const TXT_MAX = '[i18n=ValidatorIntegerRange.max][fmt.strong]{value:d}[/fmt.strong] is greater than allowed [fmt.strong]{max:d}[/fmt.strong][/i18n]';
	
	
	private $min = 0;
	private $max = 0;
	
	//************************************************************************************
	public function __construct($min, $max) {
		$min = intval($min);
		$max = intval($max);
		if ($min > $max) throw new InvalidArgumentException('min > max');
		$this->min = $min;
		$this->max = $max;		
	}
	
	//************************************************************************************
	/**
	 * @param mixed $value
	 * @param ValidationProcessEntry $oEntry
	 * @param ValidationErrorsCollection $oErrors
	 */
	public function validate($value, $oEntry, $oErrors) {
		$value = intval($value);
		if ($value < $this->min) {
			$oErrors->add(new ValidationError('', self::CODE + 1, ComplexString::Create(self::TXT_MIN, array(
				'value' => $value,
				'min' => $this->min,
				'max' => $this->max,	
			))));
		}
		if ($value > $this->max) {
			$oErrors->add(new ValidationError('', self::CODE + 2, ComplexString::Create(self::TXT_MAX, array(
				'value' => $value,
				'min' => $this->min,
				'max' => $this->max	
			))));
		}
	}	
	
}

?>