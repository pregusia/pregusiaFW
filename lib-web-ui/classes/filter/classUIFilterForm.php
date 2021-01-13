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


class UIFilterForm extends UIForm {
	
	const WIDGET_TAG_APPLY_FUNCTION = 'ApplyFunction';
	
	//************************************************************************************
	/**
	 * @param WebController $oController
	 */
	public function __construct($oController) {
		parent::__construct($oController);
	}
	
	//************************************************************************************
	/**
	 * @param UIWidgetWithValue $oWidget
	 * @param Closure $oApplyFunc
	 * @return UIWidgetWithValue
	 */
	public function addFilterWidget($oWidget, $oApplyFunc) {
		if (!($oWidget instanceof UIWidgetWithValue)) throw new InvalidArgumentException('oWidge is not UIWidgetWithValue');
		if (!($oApplyFunc instanceof Closure)) throw new InvalidArgumentException('oApplyFunc is not Closure');
		
		$oWidget->setTag(self::WIDGET_TAG_APPLY_FUNCTION, $oApplyFunc);
		return $this->addWidget($oWidget);
	}

	//************************************************************************************
	public function setWidgetValue($name, $value) {
		if ($oWidget = $this->getWidget($name)) {
			if ($oWidget instanceof UIWidgetWithValue) {
				$oWidget->setValueString($value);
			}
		}
	}
	
	//************************************************************************************
	public function reset() {
		foreach($this->getWidgets() as $oWidget) {
			if ($oWidget instanceof UIWidgetWithValue) {
				$oWidget->resetValue();
			}
		}
	}
	
	//************************************************************************************
	public function serialize() {
		$arr = array();
		foreach($this->getWidgets() as $oWidget) {
			if ($oWidget instanceof UIWidgetWithValue) {
				$arr[$oWidget->getName()] = $oWidget->getValueString();
			}
		}
		return base64_encode(json_encode($arr));
	}
	
	//************************************************************************************
	public function unserialize($str) {
		$this->reset();
		
		$arr = @json_decode(base64_decode($str),true);
		if (is_array($arr)) {
			foreach($this->getWidgets() as $oWidget) {
				if ($oWidget instanceof UIWidgetWithValue) {
					$oWidget->setValueString($arr[$oWidget->getName()]);
				}
			}
		}
	}
	
	//************************************************************************************
	protected function onProcess() {
		
	}
	
	//************************************************************************************
	protected function onAdaptResponse($oResponse) {
		
	}
	
	//************************************************************************************
	/**
	 * @param ValidationProcess $oProcess
	 */
	public function validationFill($oProcess) {
		
	}
	
	//************************************************************************************
	public function apply($arg) {
		foreach($this->getWidgets() as $oWidget) {
			if ($oWidget instanceof UIWidgetWithValue) {
				$oFunc = $oWidget->getTag(self::WIDGET_TAG_APPLY_FUNCTION);
				
				if ($oFunc instanceof Closure) {
					$oFunc($arg, $oWidget);
				}
			}
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $key
	 * @param TemplateRenderableProxyContext $oContext
	 */
	public function tplRender($key,$oContext) {
		if ($key == 'serialized') {
			return $this->serialize();
		}
		if ($key == 'isEmpty') {
			$empty = true;
			foreach($this->getWidgets() as $oWidget) {
				if ($oWidget instanceof UIWidgetWithValue) {
					if (!$oWidget->isValueEmpty()) $empty = false;
				}
			}
			return $empty;
		}
		return parent::tplRender($key, $oContext);
	}
	
	
	
}

?>