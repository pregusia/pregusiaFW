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
class UIWidget_DateInput extends UIWidgetWithValue {
	
	const MODE_FULL = 1;
	const MODE_MONTH_YEAR = 2;
	
	private $mode = 1;
	
	//************************************************************************************
	public function getMode() { return $this->mode; }
	public function setMode($v) { $this->mode = $v; }
	
	//************************************************************************************
	/**
	 * @return Date
	 */
	public function getValue() { return $this->value; }
	
	//************************************************************************************
	public function setValue($v) {
		if ($v instanceof Date) {
			$this->value = $v;
		} else {
			$this->value = Date::FromString($v);
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
		$this->value = Date::FromString($str);
	}
	
	//************************************************************************************
	public function __construct($name, $caption, $mode=1) {
		parent::__construct($name, $caption);
		$this->value = null;
		$this->mode = $mode;
	}
	
	//************************************************************************************
	public function tplRender($key, $oContext) {
		if ($key == 'dateInputMode') {
			if ($this->mode == 1) return 'full';
			if ($this->mode == 2) return 'monthyear';
			return 'full';
		}
		if ($key == 'value') {
			if ($this->mode == self::MODE_FULL) return $this->getValueString();
			if ($this->mode == self::MODE_MONTH_YEAR) {
				if ($this->getValue()) {
					return sprintf('%04d-%02d', $this->getValue()->getYear(), $this->getValue()->getMonth());
				} else {
					return '';
				}
			}
			return $this->getValueString();
		}
		
		return parent::tplRender($key, $oContext);
	}	
	
	//************************************************************************************
	/**
	 * @param WebRequest $oRequest
	 */
	protected function onRead($oRequest) {
		$val = trim($oRequest->getString($this->getName()));
		
		if ($this->mode == self::MODE_MONTH_YEAR) {
			if (preg_match('/[0-9]{4}\-[0-9]{2}/i', $val)) {
				$arr = explode('-',$val);
				$this->value = new Date(1, $arr[1], $arr[0]);
			} else {
				$this->value = null;
			}
		} else {
			$this->value = Date::FromString($val);
		}
	}
	
	//************************************************************************************
	public function uiRenderGetTemplateLocation($ctx=null) {
		return 'lib-datetime:UIWidget.DateInput';
	}
	
}

?>