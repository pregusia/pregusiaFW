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


class UIWidget_Flags extends UIWidgetWithValue {
	
	/**
	 * @var FlagsEnum
	 */
	private $oFlags = null;
	
	private $ignoredFlags = array();
	
	//************************************************************************************
	/**
	 * @return FlagsEnum
	 */
	public function getFlags() { return $this->oFlags; }
	
	//************************************************************************************
	public function __construct($name, $caption, $oFlags) {
		if (!($oFlags instanceof FlagsEnum)) throw new InvalidArgumentException('oFlags is not FlagsEnum');
		parent::__construct($name, $caption);
		$this->oFlags = $oFlags;
	}
	
	//************************************************************************************
	/**
	 * @param int $flag
	 */
	public function addIgnoredFlag($flag) {
		if (!$this->oFlags->contains($flag)) throw new InvalidArgumentException('Given flag is invalid');
		$this->ignoredFlags[] = $flag;
	}

	//************************************************************************************
	/**
	 * @param WebRequest $oRequest
	 */
	protected function onRead($oRequest) {
		$initialValue = 0;
		foreach($this->ignoredFlags as $flag) {
			if (($this->value & $flag) != 0) {
				$initialValue |= $flag;
			}
		}
		
		$this->value = $initialValue;
		
		foreach($this->getFlags()->getItems() as $flagValue => $flagCaption) {
			if (in_array($flagValue, $this->ignoredFlags)) continue;
			
			if ($oRequest->isCheckboxPressed($this->getCombinedName($flagValue))) {
				$this->value |= $flagValue;
			}
		}
	}
	
	//************************************************************************************
	public function tplRender($key, $oContext) {
		if ($key == 'Flags') {
			$flags = array();
			foreach($this->getFlags()->getItems() as $flagValue => $flagCaption) {
				if (in_array($flagValue, $this->ignoredFlags)) continue;
				
				$flags[] = array(
					'value' => $flagValue,
					'caption' => $flagCaption,
					'checked' => ($this->value & $flagValue) != 0
				);
			}
			return $flags;
		}
		return parent::tplRender($key, $oContext);
	}
	
}

?>