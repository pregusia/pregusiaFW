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


class Enum implements IEnumerable, JsonSerializable {

	use TSingleton;
	
	/**
	 * @var ComplexString[]
	 */
	private $items = array();

	//************************************************************************************
	public function __construct($items=array()) {
		if (!is_array($items)) throw new InvalidArgumentException('Given value is not array');
		
		foreach($items as $k => $v) {
			$this->add($k, $v);
		}
	}

	//************************************************************************************
	public function add($value,$caption) {
		$this->items[$value] = ComplexString::Adapt($caption);
	}

	//************************************************************************************
	public function isEmpty() {
		return count($this->items) == 0;
	}
	
	//************************************************************************************
	public function getCount() {
		return count($this->items);
	}
	
	//************************************************************************************
	/**
	 * @return ComplexString[]
	 */
	public function getItems() {
		return $this->items;
	}
	
	//************************************************************************************
	public function getKeys() {
		return array_keys($this->items);
	}
	
	//************************************************************************************
	public function getFirstKey() {
		if ($this->isEmpty()) return null;
		$keys = array_keys($this->items);
		return reset($keys);
	}
	
	//************************************************************************************
	/**
	 * @return ComplexString
	 */
	public function getFirstValue() {
		if ($this->isEmpty()) return null;
		return $this->items[$this->getFirstKey()];
	}

	//************************************************************************************
	public function isValid($v) {
		if (isset($this->items[$v])) return true;
		return false;
	}
	
	//************************************************************************************
	public function validateValueException($v) {
		if (!$this->isValid($v)) {
			throw new InvalidEnumValueException(get_class($this), $v);
		}
	}
	
	//************************************************************************************
	public static function Validate($v) {
		self::getInstance()->validateValueException($v);
	}
	
	//************************************************************************************
	public function contains($v) {
		if (isset($this->items[$v])) return true;
		return false; 
	}

	//************************************************************************************
	/**
	 * @param mixed $v
	 * @return string
	 */
	public function toRawString($v) {
		if (isset($this->items[$v])) {
			return $this->items[$v]->render(null);
		} else {
			return sprintf('Unknown (%s)', $v);
		}
	}
	
	//************************************************************************************
	/**
	 * @param mixed $v
	 * @return ComplexString
	 */
	public function toComplexString($v) {
		if (isset($this->items[$v])) {
			return $this->items[$v];
		} else {
			return ComplexString::Adapt(sprintf('Unknown (%s)', $v));
		}		
	}
	
	//************************************************************************************
	public function enumerableUsageType() { return IEnumerable::USAGE_SIMPLE; }
	public function enumerableGetAllEnum() { return $this; }
	public function enumerableSuggest($text) { return null; }
	
	//************************************************************************************
	public function enumerableToString($param) {
		if (is_array($param)) {
			$res = array();
			foreach($param as $id) $res[$id] = $this->toComplexString($id);
			return $res;
		} else {
			return $this->toComplexString($param);
		}
	}
	
	//************************************************************************************
	public function jsonSerialize() {
		$arr = array();
		foreach($this->items as $k => $v) {
			$arr[] = array(
				'k' => strval($k),
				'v' => $v->jsonSerialize(),	
			);
		}
		return $arr;
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return Enum
	 */
	public static function jsonUnserialize($arr) {
		$oEnum = new Enum();
		
		foreach($arr as $i) {
			if (isset($i['k']) && isset($i['v'])) {
				$oEnum->add($i['k'], ComplexString::jsonUnserialize($i['v']));
			}
		}
		
		return $oEnum;
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return Enum
	 */
	public static function CreateSimple($arr) {
		$oEnum = new Enum();
		foreach($arr as $v) $oEnum->add($v, $v);
		return $oEnum;
	}
	
}

?>