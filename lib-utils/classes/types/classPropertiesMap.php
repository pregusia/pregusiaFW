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
 * Klasa przechowujaca mapowania k=>v
 * Przechowywanie jest jako NameValuePair
 * K oraz V traktowane jest jako string
 * Moga wystepowac duplikaty wartosci
 * @author pregusia
 *
 */
class PropertiesMap implements JsonSerializable, IteratorAggregate {
	
	/**
	 * @var NameValuePair[]
	 */
	private $values = array();
	
	//************************************************************************************
	public function __construct() {
		
	}
	
	//************************************************************************************
	/**
	 * Zwraca pierwsza wartosc o podanym kluczu lub null
	 * @param string $key
	 * @return string
	 */
	public function getOne($key) {
		foreach($this->values as $oPair) {
			if ($oPair->getName() == $key) return $oPair->getValue();
		}
		return null;
	}
	
	//************************************************************************************
	/**
	 * Zwraca wszystkie wartosci o podanym kluczu
	 * @param string $key
	 * @return string[]
	 */
	public function getAll($key) {
		$arr = array();
		foreach($this->values as $oPair) {
			if ($oPair->getName() == $key) $arr[] = $oPair->getValue();
		}
		return $arr;
	}
	
	//************************************************************************************
	public function putSingle($name, $value) {
		$this->putSinglePair(new NameValuePair($name, $value));
	}
	
	//************************************************************************************
	/**
	 * @param NameValuePair $oPair
	 */
	public function putSinglePair($oPair) {
		if (!($oPair instanceof NameValuePair)) throw new InvalidArgumentException('oPair is not NameValuePair');
		$this->remove($oPair->getName());
		$this->values[] = $oPair;
	}
	
	//************************************************************************************
	/**
	 * @param NameValuePair[] $arr
	 */
	public function putSinglePairs($arr) {
		UtilsArray::checkArgument($arr, 'NameValuePair');
		foreach($arr as $oPair) {
			$this->putSinglePair($oPair);
		}
	}
	
	//************************************************************************************
	public function putSingleAssoc($arr) {
		if (!UtilsArray::isIterable($arr)) throw new InvalidArgumentException('arr is not Iterable');
		foreach($arr as $k => $v) {
			$this->putSingle($k, $v);
		}
	}
	
	//************************************************************************************
	public function putMulti($name, $value) {
		$this->putMultiPair(new NameValuePair($name, $value));
	}
	
	//************************************************************************************
	/**
	 * @param NameValuePair $oPair
	 */
	public function putMultiPair($oPair) {
		if (!($oPair instanceof NameValuePair)) throw new InvalidArgumentException('oPair is not NameValuePair');
		$this->values[] = $oPair;
	}
	
	//************************************************************************************
	/**
	 * @param NameValuePair[] $arr
	 */
	public function putMultiPairs($arr) {
		UtilsArray::checkArgument($arr, 'NameValuePair');
		foreach($arr as $oPair) {
			$this->putMultiPair($oPair);
		}
	}
	
	//************************************************************************************
	public function putMultiAssoc($arr) {
		if (!UtilsArray::isIterable($arr)) throw new InvalidArgumentException('arr is not Iterable');
		foreach($arr as $k => $v) {
			$this->putMulti($k, $v);
		}
	}
	
	//************************************************************************************
	public function remove($key) {
		$arr = array();
		foreach($this->values as $oPair) {
			if ($oPair->getName() != $key) $arr[] = $oPair;
		}
		$this->values = $arr;
	}
	
	//************************************************************************************
	public function clear() {
		$this->values = array();
	}
	
	//************************************************************************************
	public function getAssoc() {
		$arr = array();
		foreach($this->values as $oPair) {
			$arr[$oPair->getName()] = $oPair->getValue();
		}
		return $arr;
	}
	
	//************************************************************************************
	public function getKeys() {
		$arr = array();
		foreach($this->values as $oPair) {
			$arr[] = $oPair->getName();
		}
		return $arr;
	}
	
	//************************************************************************************
	public function getKeysUnique() {
		return array_unique($this->getKeys());
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return bool
	 */
	public function contains($name) {
		foreach($this->values as $oPair) {
			if ($oPair->getName() == $name) return true;
		}
		return false;
	}

	//************************************************************************************
	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasName($name) {
		foreach($this->values as $oPair) {
			if ($oPair->getName() == $name) return true;
		}
		return false;
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return int
	 */
	public function getNameCount($name) {
		$num = 0;
		foreach($this->values as $oPair) {
			if ($oPair->getName() == $name) $num += 1;
		}
		return $num;
	}
	
	//************************************************************************************
	/**
	 * @return bool
	 */
	public function isEmpty() {
		return count($this->values) == 0;
	}
	
	
	//************************************************************************************
	/**
	 * @return NameValuePair[]
	 */
	public function getNameValuePairs() {
		return $this->values;
	}
	
	//************************************************************************************
	public function getIterator() {
		return new ArrayIterator($this->values);
	}
	
	//************************************************************************************
	public function jsonSerialize() {
		$arr = array();
		foreach($this->values as $oPair) {
			$arr[] = $oPair->jsonSerialize();
		}
		return $arr;
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return PropertiesMap
	 */
	public static function jsonUnserialize($arr) {
		$obj = new PropertiesMap();
		if (is_array($arr)) {
			foreach($arr as $v) {
				if ($oPair = NameValuePair::jsonUnserialize($v)) {
					$obj->values[] = $oPair;
				}
			}
		}
		return $obj;
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return PropertiesMap
	 */
	public static function CreateFromAssoc($arr) {
		if (!is_array($arr)) throw new InvalidArgumentException('arr is not array');
		$obj = new PropertiesMap();
		$obj->putSingleAssoc($arr);
		return $obj;
	}
	
	//************************************************************************************
	/**
	 * @param NameValuePair[] $arr
	 * @return PropertiesMap
	 */
	public static function CreateFromNameValuePairs($arr) {
		$obj = new PropertiesMap();
		$obj->putMultiPairs($arr);
		return $obj;
	}
	
}

?>