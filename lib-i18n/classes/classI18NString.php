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
 * Teskt z rozroznieniem na jezyki
 * Immutable
 * @author pregusia
 *
 */
class I18NString implements JsonSerializable {
	
	private $value = array();
	
	//************************************************************************************
	public function __construct($param=false) {
		if ($param !== false) {		
			if (is_array($param)) {
				foreach($param as $lang => $v) {
					if (!LanguageEnum::getInstance()->isValid($lang)) throw new InvalidArgumentException('Invalid language - ' . $lang);
					$this->value[$lang] = trim(UtilsString::toString($v));
				}
			}
			elseif ($param instanceof I18NString) {
				$this->value = $param->value;
			}
			else {
				// domyslnie przyjmujemy ze jest to wartosc w pierwszym jezyku
				$this->value[LanguageEnum::getInstance()->getFirstKey()] = trim(UtilsString::toString($param));
			}
		}
	}
	
	//************************************************************************************
	public function getLang($lang) {
		return $this->value[$lang];
	}
	
	//************************************************************************************
	public function getAssoc() {
		return $this->value;
	}
	
	//************************************************************************************
	public function __toString() {
		return $this->getLang(LanguageEnum::getInstance()->getFirstKey());
	}
	
	//************************************************************************************
	public function jsonSerialize() {
		return $this->value;
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return I18NString
	 */
	public static function jsonUnserialize($arr) {
		if (is_array($arr)) {
			return new I18NString($arr);
		} else {
			return null;
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $s
	 * @return I18NString
	 */
	public static function CreateInAllLanguages($s) {
		$s = trim(UtilsString::toString($s));
		$obj = new I18NString();
		foreach(LanguageEnum::getInstance()->getItems() as $l => $aa) {
			$obj->value[$l] = $s;
		}
		return $obj;
	}
	
}

?>