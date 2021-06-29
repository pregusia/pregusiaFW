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


class UtilsUUID {
	
	private function __construct() { }
	
	const V5_NAMESPACE = '790E9033-17B1-4864-904D-DC93B1DE6F8D';
	
	//************************************************************************************
	/**
	 * Generuje losowa UUID v4
	 * @return string
	 */
	public static function generateRandom() {
		return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
	}	
	
	//************************************************************************************
	/**
	 * Generuje UUID v5 z podanego ciagu znakow i NS
	 * @param string $val
	 */
	public static function generateFromString($val, $ns='') {
		if (!$ns) $ns = self::V5_NAMESPACE;
		
		$hash = strtoupper(sha1($ns . strval($val)));
		
		$res = array();
		$res[] = substr($hash, 0, 8);
		$res[] = substr($hash, 10, 4);
		$res[] = sprintf('5%s', substr($hash, 9, 3));
		$res[] = sprintf('8%s', substr($hash, 22, 3));
		$res[] = substr($hash, 25, 12);

		// TODO: tutaj powinno jeszcze sie ustawiac jakies bity (https://stackoverflow.com/questions/10867405/generating-v5-uuid-what-is-name-and-namespace)
		// ale narazie olewamy... coz...
		
		return implode('-', $res);
	}
	
	//************************************************************************************
	/**
	 * Koduje ten UUID jako v4, ale tak, zeby mozna bylo sobie to odczytac
	 * @param int $value
	 * @return string
	 */
	public static function encodeInt($value) {
		$value = intval($value);
		if ($value < 0) throw new InvalidArgumentException('Negative values not supported');
		$valueHex = sprintf('%X', $value);
		
		$hashData = array();
		$hashData[] = $value;
		$hashData[] = $valueHex;
		$hashData[] = $value * 2719;
		$hashData[] = $value * 3947;
		
		$hash = strtoupper(sha1(implode('', $hashData)));
		
		// xxxxxxxx-xxxx-4xxx-8xxx-xxxxxxxxxxxx
		// xxxxxxNN-xxxx-4xLL-8xxx-xxxxxxxxxxxx
		// NN -> to jest zakodowana wartosc hex, o dlugosci LL
		// reszta to jest sha1 z wartosci
		
		$res = array();
		$res[] = substr($hash, 0, 8 - strlen($valueHex)) . $valueHex;
		$res[] = substr($hash, 10, 4);
		$res[] = sprintf('4%s%X',
			substr($hash, 9, 2),
			strlen($valueHex)
		);
		$res[] = sprintf('8%s', substr($hash, 22, 3));
		$res[] = substr($hash, 25, 12);
		
		return implode('-', $res);
	}
	
	//************************************************************************************
	/**
	 * Rozkodowuje UUID z encodeInt
	 * Jesli sie nie uda, zwraca 0
	 * 
	 * @param string $value
	 * @return int
	 */
	public static function decodeInt($valueString) {
		$valueString = strtoupper(trim($valueString));
		if (!$valueString) return 0;
		if (strlen($valueString) != 36) return 0;
		
		$parts = explode('-',$valueString);
		if (count($parts) != 5) return 0;
		if (strlen($parts[0]) != 8) return 0;
		if (strlen($parts[1]) != 4) return 0;
		if (strlen($parts[2]) != 4) return 0;
		if (strlen($parts[3]) != 4) return 0;
		if (strlen($parts[4]) != 12) return 0;
		
		if (substr($parts[2], 0, 1) != '4') return 0;
		if (substr($parts[3], 0, 1) != '8') return 0;
		
		$len = hexdec(substr($parts[2], 3, 1));
		if ($len == 0) return 0;
		
		$valueHex = substr($parts[0], 8 - $len, $len);
		$value = hexdec($valueHex);
		
		if (self::encodeInt($value) != $valueString) return 0;
		return intval($value);
	}
	
}

?>