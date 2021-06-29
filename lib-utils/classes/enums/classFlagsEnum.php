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


class FlagsEnum {

	use TSingleton;
	
	/**
	 * @var ComplexString[]
	 */
	private $items = array();

	//************************************************************************************
	public function __construct($items=array()) {
		if (!is_array($items)) throw new InvalidArgumentException('Given value is not array');
		
		foreach($items as $v => $c) {
			if (!self::isValueValid($v)) throw new InvalidArgumentException('Invalid flag value - ' . $v);
			$this->add($v, $c);
		}
	}
	
	//************************************************************************************
	public static function isValueValid($v) {
		if ($v <= 0) return false;
		$s = decbin($v);
		$n = 0;
		for($i=0;$i<strlen($s);++$i) {
			if (substr($s,$i,1) == '1') $n += 1;
		}
		return $n == 1;
	}
	
	//************************************************************************************
	public function contains($f) {
		return isset($this->items[$f]);
	}

	//************************************************************************************
	public function add($value,$caption) {
		$value = intval($value);
		if (!self::isValueValid($value)) throw new InvalidArgumentException('Invalid flag value - ' . $value);
		$this->items[$value] = ComplexString::Adapt($caption);
	}
	
	//************************************************************************************
	public function addNext($caption) {
		if (count($this->items) == 0) {
			$this->add(1, $caption);
		} else {
			$max = 0;
			foreach($this->items as $k => $v) {
				if ($k > $max) $max = $k;
			}
			$this->add($k * 2, $caption);
		}
	}

	//************************************************************************************
	public function isEmpty() {
		return count($this->items) == 0;
	}
	
	//************************************************************************************
	public function getItems() {
		return $this->items;
	}

	//************************************************************************************
	public function toRawString($value, $ctx=null) {
		$value = intval($value);
		$arr = array();
		foreach($this->items as $v => $c) {
			if (($value & $v) != 0) $arr[] = $c->render($ctx);
		}
		return implode(', ',$arr);
	}
	
	//************************************************************************************
	public static function isFlagSet($flags,$f) {
		return ($flags & $f) != 0;
	}
	
	//************************************************************************************
	public static function removeFlag($flags, $f) {
		return $flags & ~$f;
	}

}

?>