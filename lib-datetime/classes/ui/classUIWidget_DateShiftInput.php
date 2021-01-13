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
class UIWidget_DateShiftInput extends UIWidget_StringInput {
	
	
	//************************************************************************************
	/**
	 * @return DateShift
	 */
	public function getDateShift() {
		try {
			return DateShift::CreateFromString($this->getValueString());
		} catch(Exception $e) { 
			
		}
		return null;
	}
	
	
	//************************************************************************************
	public function setValue($v) {
		if ($v instanceof DateShift) $v = $v->toString();
		return parent::setValue($v);
	}
	
	//************************************************************************************
	public function tplRender($key, $oContext) {
		if ($key == 'shiftValue' || $key == 'shiftUnitSelectOptions') {
			$shiftValue = 1;
			$shiftUnit = DateUnit::MONTH;
			
			try {
				if ($oShift = DateShift::CreateFromString($this->value)) {
					$shiftUnit = $oShift->getUnit();
					$shiftValue = $oShift->getValue();
				}
			} catch(Exception $e) {
				
			}
			
			$oEnum = new Enum();
			$oEnum->add(DateUnit::DAY, '[i18n=DateUnit.day]day[/i18n]');
			$oEnum->add(DateUnit::WEEK, '[i18n=DateUnit.week]week[/i18n]');
			$oEnum->add(DateUnit::MONTH, '[i18n=DateUnit.month]month[/i18n]');
			$oEnum->add(DateUnit::YEAR, '[i18n=DateUnit.year]year[/i18n]');
			
			if ($key == 'shiftValue') return $shiftValue;
			if ($key == 'shiftUnitSelectOptions') {
				$oRenderer = new UISelectOptionsRenderer($oEnum);
				return $oRenderer->render($shiftUnit, false, $oContext);
			}
		}
		
		return parent::tplRender($key, $oContext);
	}
	
	//************************************************************************************
	public function uiRenderGetTemplateLocation($ctx=null) {
		return 'lib-datetime:UIWidget.DateShiftInput';
	}
	
	//************************************************************************************
	protected function onRead($oRequest) {
		$unit = $oRequest->getInteger($this->getCombinedName('unit'));
		$value = $oRequest->getInteger($this->getCombinedName('value'));
		
		$this->value = DateShift::Create($unit, $value)->toString();
	}
	
}

?>