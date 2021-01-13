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
class UIWidget_DatesRangeInput extends UIWidgetWithValue {
	
	//************************************************************************************
	/**
	 * @return DatesRange
	 */
	public function getValue() { return $this->value; }
	
	//************************************************************************************
	public function setValue($v) {
		if ($v instanceof DatesRange) {
			$this->value = $v;
		} else {
			throw new InvalidArgumentException('not DatesRange given');
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
			return sprintf('%s - %s', $this->getValue()->getStart()->toString(), $this->getValue()->getStop()->toString());
		} else {
			return '';
		}
	}
	
	//************************************************************************************
	public function setValueString($str) {
		list($a, $b) = explode(' - ',$str,2);
		$a = trim($a);
		$b = trim($b);
		
		$oStart = Date::FromString($a);
		$oStop = Date::FromString($b);
		if ($oStart && $oStop) {
			$this->value = DatesRange::CreateFromRange($oStart, $oStop);
		} else {
			$this->value = '';
		}
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
		$oStart = Date::FromString($oRequest->getString($this->getCombinedName('start')));
		$oStop = Date::FromString($oRequest->getString($this->getCombinedName('stop')));
		
		if ($oStart && $oStop) {
			$this->value = DatesRange::CreateFromRange($oStart, $oStop);
		} else {
			$this->value = null;
		}
	}
	
	//************************************************************************************
	public function tplRender($key, $oContext) {
		switch($key) {
			case 'valueStart': return $this->getValue() ? $this->getValue()->getStart()->toString() : '';
			case 'valueStop': return $this->getValue() ? $this->getValue()->getStop()->toString() : '';
			default: return parent::tplRender($key, $oContext);
		}
	}
	
	//************************************************************************************
	public function uiRenderGetTemplateLocation($ctx=null) {
		return 'lib-datetime:UIWidget.DatesRangeInput';
	}
	
}

?>