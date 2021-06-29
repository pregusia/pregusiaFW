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


class UtilsArray {
	
	private function __construct() { }
	
	//************************************************************************************
	/**
	 * @param mixed $value
	 * @param int $num
	 * @param array $arr
	 * @return array
	 */
	public static function fill($value, $num, $arr=array()) {
		if (!is_array($arr)) $arr = array();
		if ($num < 0) throw new InvalidArgumentException('num has invalid value');
		if ($num == 0) return $arr;
		
		while(count($arr) < $num) {
			$arr[] = $value;
		}
		
		return $arr;
	}
	
	//************************************************************************************
	/**
	 * @param array $keys
	 * @param mixed $value
	 * @return array
	 */
	public static function makeKeys($keys, $value=true) {
		if (!is_array($keys)) return array();
		$arr = array();
		foreach($keys as $k) {
			$arr[$k] = $value;
		}
		return $arr;
	}
	
	//************************************************************************************
	/**
	 * Zwraca informacje o roznicach elementow
	 * Zwracana tablica zawiera [add] [del] z odpowiednimi wartosciami
	 * Klucze nie sa brane pod uwage
	 * 
	 * @param array $from
	 * @param array $to
	 * @return array
	 */
	public static function getDifferences($from, $to) {
		if (!is_array($from)) throw new InvalidArgumentException('From is not Array');
		if (!is_array($to)) throw new InvalidArgumentException('To is not Array');
		
		$res = array(
			'add' => array(),
			'del' => array()
		);
		
		foreach($from as $v) {
			if (!in_array($v, $to)) $res['del'][] = $v;
		}
		
		foreach($to as $v) {
			if (!in_array($v, $from)) $res['add'][] = $v;
		}
		
		return $res;
	}
	
	//************************************************************************************
	/**
	 * Zwraca czesc wspolna dwuch tablic
	 * Nie bierze pod uwage duplikatow
	 * @param int[] $a
	 * @param int[] $b
	 * @return int[]
	 */
	public static function getIntersectionInt($a, $b) {
		if (!is_array($a)) throw new InvalidArgumentException('A is not Array');
		if (!is_array($b)) throw new InvalidArgumentException('B is not Array');
		
		$res = array();
		$tmp = array_merge($a, $b);
		foreach($tmp as $v) {
			$v = intval($v);
			if (in_array($v, $a) && in_array($v, $b)) {
				if (!in_array($v, $res)) $res[] = $v;
			}
		}
		return $res;
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return mixed
	 */
	public static function getFirst($arr) {
		if (!is_array($arr)) return null;
		
		foreach($arr as $v) {
			return $v;
		}
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return mixed
	 */
	public static function getLast($arr) {
		if (!is_array($arr)) return null;
		$tmp = null;
		
		foreach($arr as $v) {
			$tmp = $v;
		}
		
		return $tmp;
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return mixed
	 */
	public static function getRandom($arr) {
		if (!is_array($arr)) return null;
		if (!$arr) return null;
		
		$key = array_rand($arr);
		return $arr[$key];		
	}
	
	//************************************************************************************
	/**
	 * Zwraca N pierwszych elementow z tablicy
	 * @param array $arr
	 * @param int $num
	 * @return array
	 */
	public static function getFirstCount($arr, $num) {
		if ($num < 0) throw new InvalidArgumentException('num has invalid value');
		if ($num == 0) return array();
		
		$res = array();
		foreach($arr as $k => $v) {
			$res[$k] = $v;
			if (count($res) >= $num) break;
		}
		return $res;
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @param string $keyPrefix
	 * @return array
	 */
	public static function getWithKeysPrefixed($arr, $keyPrefix) {
		if (!is_array($arr)) throw new InvalidArgumentException('arr is not array');
		if (!$keyPrefix) return $arr;
		
		$res = array();
		foreach($arr as $k => $v) {
			$res[$keyPrefix . $k] = $v;
		}
		return $res;
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @param string $keySuffix
	 * @return array
	 */
	public static function getWithKeysSuffixed($arr, $keySuffix) {
		if (!is_array($arr)) throw new InvalidArgumentException('arr is not array');
		if (!$keySuffix) return $arr;
		
		$res = array();
		foreach($arr as $k => $v) {
			$res[$k . $keySuffix] = $v;
		}
		return $res;
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @param string $separator
	 * @param string $suffix
	 * @return string
	 */
	public static function joinWithSuffix($arr, $separator, $suffix) {
		if (!is_array($arr)) throw new InvalidArgumentException('arr is not array');
		$tmp = array();
		foreach($arr as $v) {
			$tmp[] = $v . $suffix;
		}
		return implode($separator, $tmp);
	}

	//************************************************************************************
	/**
	 * @param array $arr
	 * @param string $separator
	 * @param string $prefix
	 * @return string
	 */
	public static function joinWithPrefix($arr, $separator, $prefix) {
		if (!is_array($arr)) throw new InvalidArgumentException('arr is not array');
		$tmp = array();
		foreach($arr as $v) {
			$tmp[] = $prefix . $v;
		}
		return implode($separator, $tmp);
	}
	
	//************************************************************************************
	public static function isIterable($v) {
		if (is_array($v)) return true;
		if ($v instanceof Traversable) return true;
		return false;
	}
	
	//************************************************************************************
	public static function checkArgument($val, $type, $allowNulls=false) {
		if (!self::isIterable($val)) throw new InvalidArgumentException('Given argument is not iterable');
		$oType = new ReflectionClass($type);
		foreach($val as $v) {
			if ($v === null && !$allowNulls) {
				throw new InvalidArgumentException('Element is null');
			}
			if (!$oType->isInstance($v)) {
				throw new InvalidArgumentException(sprintf('Element is not %s', $type));
			}
		}
	}
	
	//************************************************************************************
	/**
	 * @param array $arr1
	 * @param array $arr2
	 * @return array
	 */
	public static function merge($arr1, $arr2) {
		if (!self::isIterable($arr1)) throw new InvalidArgumentException('arr1 is not iterable');
		if (!self::isIterable($arr2)) throw new InvalidArgumentException('arr2 is not iterable');
		
		$res = array();
		foreach($arr1 as $k => $v) $res[$k] = $v;
		foreach($arr2 as $k => $v) $res[$k] = $v;
		return $res;
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return array
	 */
	public static function sort($arr) {
		if (!self::isIterable($arr)) throw new InvalidArgumentException('arr is not iterable');
		if (!is_array($arr)) {
			$tmp = array();
			foreach($arr as $k => $v) $tmp[$k] = $v;
			$arr = $tmp;
		}
		sort($arr);
		return $arr;
	}

	//************************************************************************************
	/**
	 * @param array $arr
	 * @param int $num
	 * @return array[]
	 */
	public static function columnize($arr, $num) {
		if (!self::isIterable($arr)) throw new InvalidArgumentException('arr is not iterable');
		$num = intval($num);
		if ($num < 2) throw new InvalidArgumentException('num has invalid value');
		
		$res = array();
		$curr = array();
		foreach($arr as $k => $v) {
			$curr[$k] = $v;
			if (count($curr) == $num) {
				$res[] = $curr;
				$curr = array();
			}
		}
		if ($curr) {
			$res[] = $curr;
		}
		return $res;
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @param mixed $val
	 * @param bool $strictCompare
	 * @return bool
	 */
	public static function areAllSame($arr, $val, $strictCompare=false) {
		foreach($arr as $v) {
			if ($strictCompare) {
				if ($v !== $val) return false;
			} else {
				if ($v != $val) return false;
			}
		}
		return true;
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return int[]
	 */
	public static function prepareIDs($arr) {
		$res = array();
		if (is_array($arr)) {
			foreach($arr as $v) {
				$v = intval($v);
				if ($v > 0) {
					$res[] = $v;
				}
			}
		}
		return $res;
	}
	
	//************************************************************************************
	/**
	 * Dodaje do $res wartosc $value
	 * Nie zachowuje kluczy
	 * 
	 * @param array $res
	 * @param array $value
	 */
	public static function pushToArray(&$res, $value) {
		if (!is_array($res)) throw new InvalidArgumentException('res is not array');
		if (!is_array($value)) throw new InvalidArgumentException('value is not array');
		
		foreach($value as $v) {
			$res[] = $v;
		}
	}
	
}

?>