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
 * Przechowuje liczby zadanej precyzji
 * @author pregusia
 */
class Decimal implements IComparable {
	
	const PRECISION = 16;
	
	private $value = '';
	
	//************************************************************************************
	public function __construct($v) {
		bcscale(self::PRECISION);
		
		if ($v instanceof Decimal) {
			$this->value = $v->value;
		} else {
			if (!$v) $v = '0';
			$v = str_replace(',', '.', $v);
			
			if (!preg_match('/^-?(?:\d+|\d*\.\d+)$/', $v)) throw new InvalidArgumentException('Invalid number - ' . $v);
			$this->value = $v;
		}
	}
	
	//************************************************************************************
	public function equals($obj) {
		if ($obj instanceof Decimal) {
			return $obj->value == $this->value;
		} else {
			return floatval($obj) == $this->value;
		}
	}
	
	//************************************************************************************
	public function isZero() { return floatval($this->value) == 0; }
	
	//************************************************************************************
	/**
	 * @param object $obj
	 * @return int
	 */
	public function compareTo($obj) {
		if (!($obj instanceof Decimal)) $obj = new Decimal($obj);
		return bccomp($this->value, $obj->value);
	}
	
	//************************************************************************************
	/**
	 * @param mixed $obj
	 * @return Decimal
	 */
	public function Add($obj) {
		if (!($obj instanceof Decimal)) $obj = new Decimal($obj);
		$res = bcadd($this->value, $obj->value);
		return new Decimal($res);
	}
	
	//************************************************************************************
	/**
	 * @param mixed $obj
	 * @return Decimal
	 */
	public function Sub($obj) {
		if (!($obj instanceof Decimal)) $obj = new Decimal($obj);
		$res = bcsub($this->value, $obj->value);
		return new Decimal($res);
	}	
	
	//************************************************************************************
	/**
	 * @param mixed $obj
	 * @return Decimal
	 */
	public function Mul($obj) {
		if (!($obj instanceof Decimal)) $obj = new Decimal($obj);
		$res = bcmul($this->value, $obj->value);
		return new Decimal($res);
	}	
	
	//************************************************************************************
	/**
	 * @param mixed $obj
	 * @return Decimal
	 */
	public function Div($obj) {
		if (!($obj instanceof Decimal)) $obj = new Decimal($obj);
		$res = bcdiv($this->value, $obj->value);
		return new Decimal($res);
	}
	
	//************************************************************************************
	/**
	 * @return Decimal
	 */
	public function Neg() {
		return $this->Mul(-1);
	}
	
	//************************************************************************************
	/**
	 * Zaokrlagla liczbe
	 * @param int $digits
	 * @return Decimal
	 */
	public function round($precision) {
		$number = $this->value;
		if (strpos($number, '.') !== false) {
			if ($number[0] != '-') {
				$res = bcadd($number, '0.' . str_repeat('0', $precision) . '5', $precision);
			} else {
				$res = bcsub($number, '0.' . str_repeat('0', $precision) . '5', $precision);
			}
			return new Decimal($res);
		} else {
			return $this;
		}
	}
	
	//************************************************************************************
	public function toString($precision=false) {
		if ($precision !== false) {
			return $this->round($precision)->toString();
		} else {
			$v = strval($this->value);
			$l = localeconv();
			$v = str_replace('.',$l['decimal_point'],$v);
			return $v;
		}
	}
	
	//************************************************************************************
	public function toStringZerosStripped($precision=false) {
		$str = $this->toString($precision);
		$str = rtrim($str,'0');
		if (substr($str,-1) == '.' || substr($str,-1) == ',') $str .= '0';
		return $str;
	}
	
	//************************************************************************************
	public function __toString() {
		return $this->toString();
	}
	
	//************************************************************************************
	public function getFloat() {
		return floatval($this->value);
	}
	
}

?>