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


class UIWidget_IntCollectionEnumerableInput extends UIWidgetWithValue {
	
	/**
	 * @var IEnumerable
	 */
	private $oEnumerable = null;
	
	//************************************************************************************
	/**
	 * @return IEnumerable
	 */
	public function getEnumerable() { return $this->oEnumerable; }
	
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
		return implode(',', $this->value);
	}
	
	//************************************************************************************
	public function setValue($arg) {
		$this->value = array();
		if (is_array($arg)) {
			foreach($arg as $v) {
				if ($v = intval($v)) {
					$this->value[] = $v;
				}
			}
		}
	}
	
	//************************************************************************************
	public function setValueString($str) {
		return $this->setValue(explode(',', $str));
	}
	
	//************************************************************************************
	public function __construct($name, $caption, $oEnumerable) {
		if (!($oEnumerable instanceof IEnumerable)) throw new InvalidArgumentException('oEnumerable is not IEnumerable');
		parent::__construct($name, $caption);
		$this->value = array();
		$this->oEnumerable = $oEnumerable;
	}
	
	//************************************************************************************
	/**
	 * @param WebRequest $oRequest
	 */
	protected function onRead($oRequest) {
		$this->value = array();
		foreach($oRequest->getArray($this->getName()) as $v) {
			if ($v = intval($v)) {
				$this->value[] = $v;
			}
		}
	}
	
	//************************************************************************************
	public function tplRender($key, $oContext) {
		if ($key == 'enumerableRef') return UtilsEnumerable::serializeRef($this->oEnumerable);
		if ($key == 'SelectOptions') {
			if ($this->getEnumerable()->enumerableUsageType() == IEnumerable::USAGE_SIMPLE) {
				$oRenderer = new UISelectOptionsRenderer($this->getEnumerable()->enumerableGetAllEnum());
				return $oRenderer->render('', false, $oContext);
			} else {
				return '';
			}
		}
		if ($key == 'enumerableType') {
			if ($this->getEnumerable()->enumerableUsageType() == IEnumerable::USAGE_SIMPLE) {
				return 'select';
			}
			if ($this->getEnumerable()->enumerableUsageType() == IEnumerable::USAGE_SUGGEST) {
				return 'suggest';
			}
			return '';
		}
		if ($key == 'Items') {
			$items = array();
			$names = $this->getEnumerable()->enumerableToString($this->value);
			foreach($names as $id => $name) {
				$items[] = array(
					'id' => $id,
					'title' => $name,	
				);
			}
			return $items;
		}
		if ($key == 'valueString') {
			return implode(',',$this->getValue());
		}
		
		return parent::tplRender($key, $oContext);
	}
	
}

?>