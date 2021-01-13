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


class UIWidget_StringDualInput extends UIWidgetWithValue {
	
	//************************************************************************************
	public function getValue() {
		if (!is_array($this->value)) $this->value = array();
		return $this->value;
	}
	
	//************************************************************************************
	public function setValue($v) {
		if (is_array($v)) {
			while(count($v) < 2) $v[] = '';
		} else {
			$v = array('','');
		}
		$this->value = $v;
	}
	
	//************************************************************************************
	public function getValueString() {
		return implode(',', $this->getValue());
	}
	
	//************************************************************************************
	public function setValueString($v) {
		$this->setValue(explode(',',$v));
	}
	
	//************************************************************************************
	/**
	 * @param WebRequest $oRequest
	 */
	protected function onRead($oRequest) {
		$this->value = array(
			$oRequest->getString($this->getCombinedName('0')),
			$oRequest->getString($this->getCombinedName('1')),
		);
	}
	
}

?>