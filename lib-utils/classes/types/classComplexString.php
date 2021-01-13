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
 * Zlozony ciag znakow
 * Moze zawierac meta-tagi oraz byc parametryzowany
 * 
 * @author pregusia
 *
 */
final class ComplexString implements JsonSerializable {
	
	private static $EMPTY = false;
	
	private $value = '';
	private $params = array();
	
	
	//************************************************************************************
	private function __construct() {
		
	}
	
	//************************************************************************************
	/**
	 * @return boolean
	 */
	public function hasMetaTags() {
		return strpos($this->value, '[') !== false && strpos($this->value, ']'); 
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return boolean
	 */
	public function hasMetaTag($name) {
		return strpos($this->value, sprintf('[%s/]', $name)) !== false || strpos($this->value, sprintf('[%s]', $name)) !== false; 
	}
	
	//************************************************************************************
	public function render($ctx, $renderFlags=0) {
		$val = $this->value;
		
		if ($this->hasMetaTags()) {
			$oTokens = MetaStringTokensCollection::Tokenize($val);
			$oElement = MetaStringElementList::Parse($oTokens);
			if ($oElement) {
				$val = $oElement->render($ctx, $renderFlags);
			}
		}
		
		return UtilsString::formatSimple($val, $this->params);
	}
	
	
	//************************************************************************************
	public function isEmpty() {
		return strlen($this->value) == 0;
	}
	
	//************************************************************************************
	public function __toString() {
		return $this->value;
	}
	
	//************************************************************************************
	/**
	 * @return ComplexString
	 */
	public function trim() {
		$obj = new ComplexString();
		$obj->value = trim($this->value);
		$obj->params = $this->params;
		return $obj;
	}
	
	//************************************************************************************
	public function jsonSerialize() {
		if ($this->params) {
			$arr = $this->params;
			$arr['@value'] = $this->value;
			return $arr;
		} else {
			return strval($this->value);
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $str
	 * @param array $params
	 * @return ComplexString
	 */
	public static function Create($str, $params=array()) {
		$str = strval($str);
		if (!$str) return self::CreateEmpty();
		
		$obj = new ComplexString();
		$obj->value = strval($str);
		$obj->params = $params;
		return $obj;
	}
	
	//************************************************************************************
	/**
	 * @param mixed $v
	 * @return ComplexString
	 */
	public static function Adapt($v) {
		if ($v instanceof ComplexString) {
			return $v;
		} else {
			return self::Create($v);
		}
	}
	
	//************************************************************************************
	public static function AdaptTrim($v) {
		if ($v instanceof ComplexString) {
			return $v->trim();
		} else {
			return self::Create(trim($v));
		}		
	}
	
	//************************************************************************************
	/**
	 * @return ComplexString
	 */
	public static function CreateEmpty() {
		if (self::$EMPTY === false) {
			self::$EMPTY = new ComplexString();
		}
		return self::$EMPTY;
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return ComplexString
	 */
	public static function jsonUnserialize($val) {
		if (is_array($val)) {
			if ($val['@value']) {
				$obj = new ComplexString();
				$obj->value = $val['@value'];
				foreach($val as $k => $v) {
					if ($k == '@value') continue;
					$obj->params[$k] = $v;
				}
				return $obj;
			} else {
				return self::CreateEmpty();
			}
		}
		return self::Adapt($val);
	}
	
}

?>