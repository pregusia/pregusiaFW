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
	
	private $tzName = '';
	
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
			$oValue = $this->getValue();
			if ($this->tzName != 'UTC') {
				$oValue = $oValue->AddHours(TimeZonesEnum::getUTCOffset($this->tzName));
			}
			return $oValue->toString();
		} else {
			return '';
		}
	}
	
	//************************************************************************************
	public function setValueString($str) {
		$oValue = DateAndTime::FromString($str);
		if ($this->tzName != 'UTC') {
			$oValue = $oValue->AddHours(-TimeZonesEnum::getUTCOffset($this->tzName));
		}
		$this->value = $oValue;
	}
	
	//************************************************************************************
	public function __construct($name, $caption, $tzName='UTC') {
		parent::__construct($name, $caption);
		$this->value = null;
		$this->tzName = $tzName;
	}
	
	//************************************************************************************
	/**
	 * @param WebRequest $oRequest
	 */
	protected function onRead($oRequest) {
		$oValue = DateAndTime::FromString($oRequest->getString($this->getName()));
		if ($this->tzName != 'UTC') {
			$oValue = $oValue->AddHours(-TimeZonesEnum::getUTCOffset($this->tzName));
		}
		$this->value = $oValue;
	}
	
	//************************************************************************************
	public function uiRenderGetTemplateLocation($ctx=null) {
		return 'lib-datetime:UIWidget.DateAndTimeInput';
	}
	
}

?>