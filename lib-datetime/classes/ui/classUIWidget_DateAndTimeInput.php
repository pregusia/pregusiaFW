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


/**
 * 
 * @author pregusia
 * @NeedLibrary lib-web-ui
 *
 */
class UIWidget_DateAndTimeInput extends UIWidgetWithValue {
	
	//************************************************************************************
	/**
	 * @return DateAndTime
	 */
	public function getValue() { return $this->value; }
	
	//************************************************************************************
	public function setValue($v) {
		if ($v instanceof DateAndTime) {
			$this->value = $v;
		} else {
			$this->value = DateAndTime::FromString($v);
		}
		return $this;
	}
	
	//************************************************************************************
	public function resetValue() {
		$this->value = null;
	}
	
	//************************************************************************************
	public function getValueString() {
		if ($this->value) {
			return $this->getValue()->toString();
		} else {
			return '';
		}
	}
	
	//************************************************************************************
	public function setValueString($str) {
		$this->value = DateAndTime::FromString($str);
	}
	
	//************************************************************************************
	public function __construct($name, $caption) {
		parent::__construct($name, $caption);
		$this->value = null;
	}
	
	//************************************************************************************
	/**
	 * @param WebRequest $oRequest
	 */
	protected function onRead($oRequest) {
		$this->value = DateAndTime::FromString($oRequest->getString($this->getName()));
	}
	
	//************************************************************************************
	public function uiRenderGetTemplateLocation($ctx=null) {
		return 'lib-datetime:UIWidget.DateAndTimeInput';
	}
	
}

?>