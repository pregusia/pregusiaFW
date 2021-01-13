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


class ValidatorClassNameImplements implements IValidator {
	
	const CODE = 1140;
	
	const TXT_NOT_FOUND = '[i18n=ValidatorClassNameImplements.notfound]Class [fmt.strong]{value:s}[/fmt.strong] not found[/i18n]';
	const TXT_NOT_IMPLEMENTS = '[i18n=ValidatorClassNameImplements.invalid]Class [fmt.strong]{value:s}[/fmt.strong] is not implementing [fmt.strong]{interfaceName:s}[/fmt.strong][/i18n]';
	
	
	/**
	 * @var CodeBaseDeclaredInterface
	 */
	private $oInterface = null;
	
	//************************************************************************************
	public function __construct($ifaceName) {
		$this->oInterface = CodeBase::getInterface($ifaceName);
		if (!$this->oInterface->isInterface()) {
			throw new InvalidArgumentException(sprintf('Value %s is not interface', $ifaceName));
		}
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
			if (!$oClass->isInterface() && !$oClass->isAbstract() && $oClass->isImplementing($this->oInterface->getName())) {
				// ok
			} else {
				$oErrors->add(new ValidationError('', self::CODE + 2, ComplexString::Create(self::TXT_NOT_IMPLEMENTS, array(
					'value' => $value,
					'interfaceName' => $this->oInterface->getName(),	
				))));
			}
		} else {
			$oErrors->add(new ValidationError('', self::CODE + 1, ComplexString::Create(self::TXT_NOT_FOUND,array(
				'value' => $value,
				'interfaceName' => $this->oInterface->getName()
			))));
		}
	}
	
}

?>