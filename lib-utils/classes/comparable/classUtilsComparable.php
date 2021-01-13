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


class UtilsComparable {
	
	private function __construct() { }
	
	//************************************************************************************
	/**
	 * @param IComparable $obj
	 * @param mixed $arg
	 * @return boolean
	 */
	public static function isEqual($obj, $arg) {
		if ($obj === null && $arg === null) return true;
		if ($obj === null) return false;
		if ($arg === null) return false;
		
		if (!($obj instanceof IComparable)) throw new InvalidArgumentException('obj is not IComparable');
		return $obj->compareTo($arg) == 0;
	}
	
	//************************************************************************************
	/**
	 * @param IComparable $obj
	 * @param mixed $arg
	 * @return boolean
	 */
	public static function isNotEqual($obj, $arg) {
		return !self::isEqual($obj, $arg);
	}
	
	//************************************************************************************
	/**
	 * @param IComparable $obj
	 * @param mixed $arg
	 * @return boolean
	 */
	public static function isLess($obj, $arg) {
		if (!($obj instanceof IComparable)) throw new InvalidArgumentException('obj is not IComparable');
		return $obj->compareTo($arg) < 0;
	}
	
	//************************************************************************************
	/**
	 * @param IComparable $obj
	 * @param mixed $arg
	 * @return boolean
	 */
	public static function isGreater($obj, $arg) {
		if (!($obj instanceof IComparable)) throw new InvalidArgumentException('obj is not IComparable');
		return $obj->compareTo($arg) > 0;
	}
	
	//************************************************************************************
	/**
	 * @param IComparable $obj
	 * @param mixed $arg
	 * @return boolean
	 */
	public static function isLessOrEqual($obj, $arg) {
		return self::isLess($obj, $arg) || self::isEqual($obj, $arg);
	}
	
	//************************************************************************************
	/**
	 * @param IComparable $obj
	 * @param mixed $arg
	 * @return boolean
	 */
	public static function isGreaterOrEqual($obj, $arg) {
		return self::isGreater($obj, $arg) || self::isEqual($obj, $arg);
	}
	
}

?>