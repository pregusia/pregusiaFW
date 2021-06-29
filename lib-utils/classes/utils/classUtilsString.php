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


class UtilsString {
	
	private function __construct() { }
	
	//************************************************************************************
	/**
	 * Ogranicza ciag znakow do zadanej dlugosci
	 * @param string $str
	 * @param int $length
	 * @param string $suffix
	 * @return string
	 */
	public static function shorten($str, $length, $suffix='...') {
		if ($length <= 0) throw new InvalidArgumentException('Invalid length');
		
		$str = trim($str);
		if (mb_strlen($str) < $length) {
			return $str;
		} else {
			return mb_substr($str, 0, $length) . $suffix;
		}
	}

	//************************************************************************************
	public static function indent($str, $n, $prefix="\t") {
		if ($n == 0) return $str;
		if ($n < 0) throw new InvalidArgumentException('n has invalid value');
		
		$out = '';
		
		foreach(explode("\n", $str) as $line) {
			$line = rtrim($line);
			$out .= str_repeat($prefix, $n) . $line . "\n";
		}
		
		return $out;
	}
	
	//************************************************************************************
	/**
	 * Kazde wystopienie $search w $text opakowuje w tag $tagName
	 * @param string $text
	 * @param string $search
	 * @param string $tagName
	 * @return string
	 */
	public static function wrapInTag($text, $search, $tagName='strong') {
		if (!$text) return '';
		
		$search = trim($search);
		if (!$search) return $text;
		
		$search = str_replace('|','',$search);
		
		return preg_replace_callback(sprintf('|%s|i',$search),function($m) use($tagName) {
			return sprintf('<%s>%s</%s>', $tagName, $m[0], $tagName);
		});
	}
	
	//************************************************************************************
	/**
	 * Generuje ciag znakow o zadanej dlugosci
	 * @param int $length
	 * @param string $chars
	 * @return string
	 */
	public static function generate($length,$chars='abcdefghijklmnopqrstuwvxyz1234567890') {
		if ($length == 0) return '';
		if ($length < 0) throw new InvalidArgumentException('Length has invalid value');
		if (!$chars) throw new InvalidArgumentException('Empty chars');
		
		$out = '';
		while(strlen($out) < $length) {
			$idx = rand(0,strlen($chars)-1);
			$out .= substr($chars,$idx,1);
		}
		return $out;
	}
	
	//************************************************************************************
	/**
	 * @param mixed $v
	 * @return string
	 */
	public static function toString($v,$ctx=null) {
		if (is_object($v)) {
			if ($v instanceof ComplexString) {
				return $v->render($ctx);
			}
			
			$oClass = new ReflectionClass($v);
			if ($oClass->hasMethod('toString')) {
				return $oClass->getMethod('toString')->invoke($v);
			} else {
				return sprintf('[%s]', get_class($v));
			}
		} else {
			return strval($v);
		}
	}
	
	//************************************************************************************
	/**
	 * @param mixed[] $arr
	 * @return string
	 */
	public static function toStringArray($arr) {
		if (!is_array($arr)) return '[]';
		if (!$arr) return '[]';
		
		$tmp = array();
		foreach($arr as $i) {
			$tmp[] = self::toString($i); 
		}
		
		return '[' . implode(', ', $tmp) . ']';
	}
	
	//************************************************************************************
	/**
	 * @param mixed $v
	 * @return string
	 */
	public static function serializeBase64($v) {
		return base64_encode(serialize($v));
	}
	
	//************************************************************************************
	/**
	 * @param string $str
	 * @return mixed
	 */
	public static function unserializeBase64($str) {
		$str = @base64_decode($str);
		if ($str) {
			$val = @unserialize($str);
			if ($val) {
				return $val;
			} 
		}
		return null;
	}
	
	//************************************************************************************
	public static function slugCreate($id, $arr) {
		$id = intval($id);
		$tmp = array();
		foreach($arr as $v) {
			$v = preg_replace('/[^A-Za-z0-9]+/', '-', $v);
			$tmp[] = $v;
		}
		
		$str = sprintf('%d-%s', $id, $tmp);
		return preg_replace('/[\-]{2,}/', '-', $str);
	}
	
	//************************************************************************************
	/**
	 * Tworzy reprezentacje tego ciagu znakow jako pozbawiona spacji, znakow bialych itp
	 * 
	 * @param string $str
	 * @return str
	 */
	public static function escapePrintable($str) {
		$str = strtolower(trim($str));
		$str = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
		$str = preg_replace('/[^A-Za-z0-9]+/', '_', $str);
		$str = preg_replace('/\_{2,}/i','_',$str);
		return $str;
	}

	//************************************************************************************
	/**
	 * Podstawia zmienne do ciagu znakow w odpowiednich formatach
	 * @param string $str
	 * @param array $params
	 */
	public static function formatSimple($str, $params) {
		if (strpos($str, '{') === false) return $str;
		
		$res = '';
		$arr = preg_split('/(\{[A-Za-z0-9]+\:[a-z0-9\.]+\})/',$str, null, PREG_SPLIT_DELIM_CAPTURE);
		
		foreach($arr as $v) {
			if (substr($v,0,1) == '{') {
				$v = trim($v,'{}');
				list($a, $b) = explode(':',$v,2);
				$v = sprintf('%' . $b, $params[$a]);
			}
			$res .= $v;
		}
		
		return $res;
	}
	
	//************************************************************************************
	/**
	 * @param string $str
	 * @param string $test
	 * @return bool
	 */
	public static function startsWith($str, $test) {
		if (!$test) return true;
		return substr($str,0,strlen($test)) == $test;
	}
	
	//************************************************************************************
	/**
	 * @param string $str
	 * @param string $test
	 * @return bool
	 */
	public static function endsWith($str, $test) {
		if (!$test) return true;
		return substr($str,-strlen($test)) == $test;
	}
	
	//************************************************************************************
	/**
	 * @param int $size
	 * @param string[] $units
	 * @param int $base
	 * @return string
	 */
	public static function sizeToString($size, $units=false, $base=1024) {
		if ($units === false) {
			$units = array('B','KiB','MiB','GiB','TiB');
		} else {
			if (!is_array($units)) throw new InvalidArgumentException('units is not array');
			if (!$units) throw new InvalidArgumentException('units is empty array');
		}
		$base = intval($base);
		if ($base <= 0) throw new InvalidArgumentException('Invalid base');
		
		$order = 0;
		while($size > $base && $order < count($units) - 1) {
			$order += 1;
			$size /= $base;
		}
		return sprintf('%.2f %s', $size, $units[$order]);
	}
	
	//************************************************************************************
	/**
	 * @param string $data
	 * @param string $key
	 * @return string
	 */
	public static function simpleEncrypt($data, $key) {
		$key = trim($key);
		
		if (!$key) throw new InvalidArgumentException('key is empty');
		if (!$data) return '';
		
		$result = '';
		for($i=0; $i<strlen($data); ++$i) {
			$dataChar = substr($data, $i, 1);
			$keyChar = substr($key, ($i % strlen($key)) - 1, 1);
			$result .= chr(ord($dataChar) + ord($keyChar));
		}
		return $result;
	}
	
	//************************************************************************************
	/**
	 * @param string $data
	 * @param string $key
	 * @return string
	 */
	public static function simpleDecrypt($data, $key) {
		$key = trim($key);
		
		if (!$key) throw new InvalidArgumentException('key is empty');
		if (!$data) return '';
		
		$result = '';
		for($i=0; $i<strlen($data); ++$i) {
			$dataChar = substr($data, $i, 1);
			$keyChar = substr($key, ($i % strlen($key)) - 1, 1);
			$result .= chr(ord($dataChar) - ord($keyChar));
		}
		return $result;	
	}
	
	//************************************************************************************
	/**
	 * Zwraca podany ciag znakow jako base64url
	 * @param string $value
	 * @return string
	 */
	public static function base64URLEncode($value) {
		return rtrim(strtr(base64_encode($value), '+/', '-_'),'=');
	}
	
	//************************************************************************************
	/**
	 * @param string $value
	 * @return string
	 */
	public static function base64URLDecode($value) {
		$value = trim($value);
		if (!$value) return null;
		
		return @base64_decode(strtr($value, '-_', '+/'));		
	}
	
	//************************************************************************************
	/**
	 * @param array $json
	 * @return string
	 */
	public static function base64URLEncodeJSON($json) {
		if (!is_array($json)) throw new InvalidArgumentException('Given argument is not array');
		return self::base64URLEncode(json_encode($json));
	}
	
	//************************************************************************************
	/**
	 * @param string $value
	 * @return array
	 */
	public static function base64URLDecodeJSON($value) {
		$data = self::base64URLDecode($value);
		if ($data) {
			$arr = json_decode($data, true);
			if (is_array($arr)) {
				return $arr;
			}
		}
		return null;
	}
	
	//************************************************************************************
	/**
	 * Serializuje podana tablice do JSON, nastepnie koduje kluczem (patrz simpleEncrypt)
	 * i zwraca wartosc base64 z normalizacja znakow + i /
	 * 
	 * @param array $data
	 * @param string $key
	 * @return string
	 */
	public static function urlSafeEncrypt($data, $key) {
		if (!is_array($data)) throw new InvalidArgumentException('Given argument is not array');
		
		$data = self::simpleEncrypt(json_encode($data), $key);
		return self::base64URLEncode($data);
	}
	
	//************************************************************************************
	/**
	 * Rozkodowywuje dane i zwraca dane JSON
	 * 
	 * @param string $data
	 * @param string $key
	 * @return array
	 */
	public static function urlSafeDecrypt($data, $key) {
		$data = trim($data);
		if (!$data) return array();
		
		$data = self::base64URLDecode($data);
		if ($data) {
			$arr = @json_decode(self::simpleDecrypt($data, $key), true);
			if (is_array($arr)) {
				return $arr;
			}
		}
		
		return array();
	}
	
	//************************************************************************************
	/**
	 * @param string $tagName
	 * @param NameValuePair[] $attrs
	 * @param bool $autoClose
	 * @return string
	 */
	public static function makeXMLTag($tagName, $attrs, $autoClose=false) {
		if (!$tagName) throw new InvalidArgumentException('Empty tagName');
		$arr = array();
		$arr[] = sprintf('<%s', $tagName);
		
		if (is_array($attrs)) {
			UtilsArray::checkArgument($attrs, 'NameValuePair');
			foreach($attrs as $oPair) {
				$arr[] = sprintf('%s="%s"', $oPair->getName(), htmlspecialchars($oPair->getValue()));
			}
		}
		
		if ($autoClose) {
			$arr[] = '/>';
		} else {
			$arr[] = '>';
		}
		
		return implode(' ',$arr);
	}
	
	//************************************************************************************
	/**
	 * @param string $value
	 * @param string $validChars
	 * @param string $replacement
	 * @return string
	 */
	public static function normalizeString($value, $validChars, $replacement) {
		$n = '';
		for($i=0;$i<strlen($value);++$i) {
			$ch = substr($value, $i, 1);
			if (strpos($validChars, strtolower($ch)) !== false) {
				$n .= $ch;
			} else {
				$n .= $replacement;
			}
		}
		if (!$n) $n = 'empty';
		return $n;		
	}
	
}

?>