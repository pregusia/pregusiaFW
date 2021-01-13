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


class UIWidget_IntCollectionStringInput extends UIWidgetWithValue {
	
	//************************************************************************************
	public function getValue() {
		if (is_array($this->value)) {
			return $this->value;
		} else {
			return array();
		}
	}
	
	//************************************************************************************
	public function getValueString() {
		return implode(',', $this->getValue());
	}
	
	//************************************************************************************
	public function setValueString($str) {
		$this->value = array();
		foreach(explode(',',$str) as $v) {
			$v = intval(trim($v));
			if ($v > 0) {
				$this->value[] = $v;
			}
		}
	}
	
	//************************************************************************************
	public function __construct($name, $caption) {
		parent::__construct($name, $caption);
		$this->value = array();
		$this->setPrefix('[ui.icon.list/]');
	}
	
	//************************************************************************************
	/**
	 * @param WebRequest $oRequest
	 */
	protected function onRead($oRequest) {
		$str = $oRequest->getString($this->getName());
		$this->setValueString($str);
	}
	
}

?>