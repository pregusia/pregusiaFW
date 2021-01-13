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

class ExpressionParser_Value {
	
	private $value = 0;
	private $tag = '';
	
	//************************************************************************************
	public function getValue() { return $this->value; }
	public function getTag() { return $this->tag; }
	
	//************************************************************************************
	/**
	 * @return bool
	 */
	public function isNumber() {
		return is_float($this->value);
	}
	
	//************************************************************************************
	/**
	 * @return bool
	 */
	public function isString() {
		return is_string($this->value);
	}
	
	//************************************************************************************
	/**
	 * @return bool
	 */
	public function boolValue() {
		if ($this->isNumber()) return intval($this->value) != 0;
		if ($this->isString()) return strlen($this->value) > 0;
		return false;
	}

	//************************************************************************************
	private function __construct($value, $tag) {
		if (is_string($value)) $this->value = $value;
		elseif (is_float($value)) $this->value = $value;
		else {
			throw new InvalidArgumentException('Given argument has invalid type');
		}		
		
		$this->tag = $tag;
	}
	
	//************************************************************************************
	/**
	 * @param string $val
	 * @return ExpressionParser_Value
	 */
	public static function CreateNumber($val, $tag='') {
		return new ExpressionParser_Value(floatval($val), $tag);
	}
	
	//************************************************************************************
	/**
	 * @param string $val
	 * @return ExpressionParser_Value
	 */
	public static function CreateString($val, $tag='') {
		return new ExpressionParser_Value(strval($val), $tag);
	}
	
	//************************************************************************************
	/**
	 * @param mixed $val
	 * @return ExpressionParser_Value
	 */
	public static function CreateBool($val, $tag='') {
		if ($val) {
			return self::CreateNumber(1, $tag);
		} else {
			return self::CreateNumber(0, $tag);
		}
	}
	
}

?>