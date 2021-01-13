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


class UIWidget_TagsInput extends UIWidgetWithValue {
	
	/**
	 * @var IEnumerable
	 */
	private $oTagsEnumerable = null;
	
	//************************************************************************************
	public function getValueString() { return implode(',',$this->getValue()); }

	//************************************************************************************
	public function setValue($v) {
		if (is_array($v)) {
			$this->value = array();
			foreach($v as $e) {
				if ($e = trim($e)) {
					$this->value[] = $e;
				}
			}
		}
	}
	
	//************************************************************************************
	public function setValueString($str) {
		$this->setValue(explode(',',$str));
	}
	
	//************************************************************************************
	public function __construct($name, $caption, $oEnumerable) {
		parent::__construct($name, $caption);
		$this->value = array();
		if ($oEnumerable) {
			if (!($oEnumerable instanceof IEnumerable)) throw new InvalidArgumentException('oEnumerable is not IEnumerable');
		}
		$this->oTagsEnumerable = $oEnumerable;
	}
	
	//************************************************************************************
	/**
	 * @param WebRequest $oRequest
	 */
	protected function onRead($oRequest) {
		$this->value = array();
		foreach($oRequest->getArray($this->getName()) as $v) {
			$this->value[] = $v;
		}
	}
	
	//************************************************************************************
	public function tplRender($key, $oContext) {
		
		if ($key == 'enumerableType') {
			if ($this->oTagsEnumerable) {
				if ($this->oTagsEnumerable->enumerableUsageType() == IEnumerable::USAGE_SIMPLE) {
					return 'select';
				}
				if ($this->oTagsEnumerable->enumerableUsageType() == IEnumerable::USAGE_SUGGEST) {
					return 'suggest';
				}
			}
			return 'suggest';
		}
		
		if ($key == 'enumerableRef') {
			if ($this->oTagsEnumerable) {
				return UtilsEnumerable::serializeRef($this->oTagsEnumerable);
			}
			return '';
		}
		
		if ($key == 'SelectOptions') {
			$selectedValues = array();
			$oEnum = new Enum();
			
			if ($this->oTagsEnumerable && $this->oTagsEnumerable->enumerableUsageType() == IEnumerable::USAGE_SIMPLE) {
				$oEnum = $this->oTagsEnumerable->enumerableGetAllEnum();
			}
			
			foreach($this->value as $tag) {
				$selectedValues[] = $tag;
				if (!$oEnum->isValid($tag)) {
					$oEnum->add($tag, $tag);
				}
			}
			
			$oRenderer = new UISelectOptionsRenderer($oEnum);
			return $oRenderer->render($selectedValues, false, $oContext);
		}
		
		return parent::tplRender($key, $oContext);
	}
	
}

?>