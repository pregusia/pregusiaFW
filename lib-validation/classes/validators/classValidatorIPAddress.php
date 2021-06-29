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


class ValidatorIPAddress implements IValidator {
	
	const TYPE_V4 = 4;
	const TYPE_V6 = 6;
	
	const CODE = 1250;
	const TXT_INVALID = '[i18n=ValidatorIPv4Address.invalid]Given e-mail address is invalid[/i18n]';
	
	private $type = 0;
	
	//************************************************************************************
	public function getType() { return $this->type; }

	//************************************************************************************
	public function __construct($type=4) {
		$this->type = $type;
	}
	
	//************************************************************************************
	/**
	 * @param mixed $value
	 * @param ValidationProcessEntry $oEntry
	 * @param ValidationErrorsCollection $oErrors
	 */
	public function validate($value, $oEntry, $oErrors) {
		if (!$value) return;
		
		if ($this->type == self::TYPE_V4) {
			if (!filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
				$oErrors->add(new ValidationError('', self::CODE, ComplexString::Create(self::TXT_INVALID)));
			}
		}
		if ($this->type == self::TYPE_V6) {
			if (!filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
				$oErrors->add(new ValidationError('', self::CODE, ComplexString::Create(self::TXT_INVALID)));
			}
		}
	}
	
	
}

?>