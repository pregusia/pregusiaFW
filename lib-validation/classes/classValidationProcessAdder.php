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


class ValidationProcessAdder {
	
	use TTagsContainer;
	
	private $oProcess = null;
	
	//************************************************************************************
	public function __construct($oProcess) {
		if (!($oProcess instanceof ValidationProcess)) throw new InvalidArgumentException('oProcess is not ValidationProcess');
		$this->oProcess = $oProcess;
	}
	
	//************************************************************************************
	/**
	 * @return ValidationProcess
	 */
	public function getProcess() {
		return $this->oProcess;
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
		$oEntry = $this->getProcess()->addEntry($fieldName, $value, $oValidator);
		foreach($this->tags as $k => $v) {
			$oEntry->setTag($k, $v);
		}
		return $oEntry;
	}
	
}

?>