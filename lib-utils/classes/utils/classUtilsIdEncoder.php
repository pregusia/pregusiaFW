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


class UtilsIdEncoder {
	
	private function __construct() { }
	
	const BASE = "tEmduyrafc8hvo0U15BeCWkqVAD4PLpIYnl6MQ7swKbORjNJgHS3T2zZFiGX9";
	const PRIME = 7457;
	const LENGTH = 20;
	
	//************************************************************************************
	private static function baseConvertInt($v) {
		return UtilsNumber::toBaseConvert($v, self::BASE);
	}
	
	//************************************************************************************
	private static function padToLength($v, $len=-1) {
		if ($len == -1) $len = self::LENGTH;
		
		$v = strval($v);
		if (strlen($v) > $len) $v = substr($v, 0, $len);
		$hash = md5($v);
		
		// ustalmy ile przed i ile po
		$need = $len - strlen($v);
		$needLeft = floor($need / 2);
		$needRight = $need - $needLeft;
		
		$res = '';
		if ($needLeft == 1) $res .= 'x';
		elseif ($needLeft > 1) $res .= substr($hash, 0, $needLeft - 1) . 'x';
		$res .= $v;
		if ($needRight == 1) $res .= 'x';
		elseif ($needRight > 1) $res .= 'x' . substr($hash, 10, $needRight - 1);
		
		return $res;
	}
	
	//************************************************************************************
	private static function unpadFromLength($v, $len=-1) {
		if ($len == -1) $len = self::LENGTH;
		
		$v = trim(strval($v));
		if (strlen($v) != $len) return '';
		
		list($a, $b, $c) = explode('x',$v,3);
		return $b;
	}
	
	//************************************************************************************
	private static function create() {
		$args = func_get_args();
		$v = '';
		foreach($args as $a) $v .= strval($a);
		
		$res = '';
		for($i=0;$i<strlen($v);++$i) {
			$n = ord(substr($v,$i,1));
			$res .= UtilsNumber::toBaseConvert($n, self::BASE);
		}
		
		return self::padToLength($res);
	}
	
	//************************************************************************************
	/**
	 * Koduje ID z numerka (> 0) na string
	 * @param int $v
	 * @return string
	 */
	public static function encodeId($id) {
		$id = intval($id);
		if ($id <= 0) return '';
		
		$v = $id * self::PRIME;
		return self::padToLength(UtilsNumber::toBaseConvert($id * self::PRIME, self::BASE));
	}
	
	//************************************************************************************
	/**
	 * Konwertuje ID ze string na ID
	 * Jesli sie nie powiedzie zwraca 0
	 * @param string $str
	 */
	public static function decodeId($str) {
		$str = trim(strval($str));
		if (!$str) return 0;
		if (strlen($str) != self::LENGTH) return 0;
		$str = self::unpadFromLength($str);
		
		$i = UtilsNumber::fromBaseConvert($str, self::BASE);
		if ($i == 0) return 0;
		
		if ($i % self::PRIME == 0) {
			return intval($i / self::PRIME);
		}
		
		return 0;
	}
	
}

?>