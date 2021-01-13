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


class ValidatorClassNameExtends implements IValidator {
	
	const CODE = 1150;
	
	const TXT_NOT_FOUND = '[i18n=ValidatorClassNameExtends.notfound]Class [fmt.strong]{value:s}[/fmt.strong] not found[/i18n]';
	const TXT_NOT_EXTENDS = '[i18n=ValidatorClassNameExtends.invalid]Class [fmt.strong]{value:s}[/fmt.strong] is not extending [fmt.strong]{className:s}[/fmt.strong] or is abstract[/i18n]';
	
	/**
	 * @var CodeBaseDeclaredClass
	 */
	private $oBaseClass = null;
	
	//************************************************************************************
	public function __construct($baseName) {
		$this->oBaseClass = CodeBase::getClass($baseName);
	}
	
	//************************************************************************************
	/**
	 * @param mixed $value
	 * @param ValidationProcessEntry $oEntry
	 * @param ValidationErrorsCollection $oErrors
	 */
	public function validate($value, $oEntry, $oErrors) {
		if (!$value) return;
		
		$oClass = CodeBase::getClass($value, false);
		if ($oClass) {
			if (!$oClass->isInterface() && !$oClass->isAbstract() && $oClass->isExtending($this->oBaseClass->getName())) {
				// ok
			} else {
				$oErrors->add(new ValidationError('', self::CODE + 2, ComplexString::Create(self::TXT_NOT_EXTENDS,array(
					'value' => $value,
					'className' => $this->oBaseClass->getName()	
				))));
			}
		} else {
			$oErrors->add(new ValidationError('', self::CODE + 1, ComplexString::Create(self::TXT_NOT_FOUND, array(
				'value' => $value,
				'className' => $this->oBaseClass->getName()
			))));
		}
	}
	
}

?>