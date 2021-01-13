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


class UtilsNumber {
	
	private function __construct() { }
	
	//************************************************************************************
	public static function clamp($v, $min, $max) {
		if ($v < $min) return $min;
		if ($v > $max) return $max;
		return $v;
	}
	
	//************************************************************************************
	/**
	 * @param int $v
	 * @param string $baseChars
	 * @return string
	 */
	public static function toBaseConvert($v, $baseChars) {
		$v = intval($v);
		
		$b = strlen($baseChars);
		$r = $v % $b;
		$res = $baseChars[$r];
		$q = floor($v / $b);
		while ($q) {
			$r = $q % $b;
			$q = floor($q / $b);
			$res = $baseChars[$r] . $res;
		}
		return $res;		
	}
	
	//************************************************************************************
	/**
	 * @param string $v
	 * @param string $baseChars
	 * @return int
	 */
	public static function fromBaseConvert($v, $baseChars) {
		$v = strval($v);
		$limit = strlen($v);
		$b = strlen($baseChars);
		$res = strpos($baseChars, $v[0]);
		for($i=1;$i<$limit;$i++) {
			$res = $b * $res + strpos($baseChars,$v[$i]);
  		}
		return intval($res);
	}
	
}

?>