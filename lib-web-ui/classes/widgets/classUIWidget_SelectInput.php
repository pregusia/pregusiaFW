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


class UIWidget_SelectInput extends UIWidgetWithValue {
	
	const FLAG_WITH_NEUTRAL = 1;
	const FLAG_MULTI = 2;
	
	/**
	 * @var IEnumerable
	 */
	private $oEnumerable = null;
	
	private $flags = 0;
	
	
	//************************************************************************************
	/**
	 * @return IEnumerable
	 */
	public function getEnumerable() { return $this->oEnumerable; }

	//************************************************************************************
	public function flagSet($flag) { $this->flags |= $flag; }
	public function flagUnset($flag) { $this->flags &= ~$flag; }
	public function flagHas($flag) { return $this->flags & $flag ? true : false; }
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @param string $caption
	 * @param IEnumerable $oEnumerable
	 * @param int $flags
	 */
	public function __construct($name, $caption, $oEnumerable, $flags=0) {
		if (!($oEnumerable instanceof IEnumerable)) throw new InvalidArgumentException('oEnumerable is not IEnumerable');
		if ($oEnumerable->enumerableUsageType() != IEnumerable::USAGE_SIMPLE) throw new InvalidArgumentException('Given enumerable usage is not USAGE_SIMPLE');
		
		parent::__construct($name, $caption);
		
		$this->oEnumerable = $oEnumerable;
		$this->flags = $flags;
	}
	
	//************************************************************************************
	public function getValue() {
		if ($this->flagHas(self::FLAG_MULTI)) {
			if (!is_array($this->value)) return array();
			return $this->value;
		} else {
			return trim($this->value);
		}
	}
	
	//************************************************************************************
	public function setValue($v) {
		if ($this->flagHas(self::FLAG_MULTI)) {
			if (is_array($v)) {
				$this->value = $v;
			} else {
				$v = trim($v);
				if ($v) {
					$this->value = array($v);
				} else {
					$this->value = array();
				}
			}
		} else {
			if (is_array($v)) $v = UtilsArray::getFirst($v);
			$this->value = trim($v);
		}
		return $this;
	}
	
	//************************************************************************************
	public function resetValue() {
		if ($this->flagHas(self::FLAG_MULTI)) {
			$this->value = array();
		} else {
			$this->value = '';
		}
	}
	
	//************************************************************************************
	public function getValueString() {
		if ($this->flagHas(self::FLAG_MULTI)) {
			if (is_array($this->value)) {
				return implode(',',$this->value);
			} else {
				return '';
			}
		} else {
			return trim($this->value);
		}
	}
	
	//************************************************************************************
	public function setValueString($str) {
		if ($this->flagHas(self::FLAG_MULTI)) {
			$this->value = array();
			foreach(explode(',',$str) as $v) {
				if ($v = trim($v)) {
					$this->value[] = $v;
				}
			}
		} else {
			$this->value = trim($str);
		}
	}	
	
	//************************************************************************************
	/**
	 * @param WebRequest $oRequest
	 */
	protected function onRead($oRequest) {
		if ($this->flagHas(self::FLAG_MULTI)) {
			$this->value = $oRequest->getArray($this->getName());
		} else {
			$this->value = $oRequest->getString($this->getName());
		}
	}
	
	//************************************************************************************
	public function tplRender($key, $oContext) {
		switch($key) {
			case 'SelectOptions': {
				$oRenderer = new UISelectOptionsRenderer($this->getEnumerable()->enumerableGetAllEnum());
				return $oRenderer->render($this->getValue(), $this->flagHas(self::FLAG_WITH_NEUTRAL) && !$this->flagHas(self::FLAG_MULTI), $oContext);
			}
			case 'flag-withNeurtal': return $this->flagHas(self::FLAG_WITH_NEUTRAL);
			case 'flag-multi': return $this->flagHas(self::FLAG_MULTI);
			default: return parent::tplRender($key, $oContext);
		}
	}
	
}

?>