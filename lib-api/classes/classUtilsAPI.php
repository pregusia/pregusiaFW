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


class UtilsAPI {
	
	private function __construct() { }
	
	
	//************************************************************************************
	public static function toJSON($val, $type) {
		$type = trim($type);
		$bIsArray = false;
		
		if ($type && substr($type,-2) == '[]') {
			$bIsArray = true;
			$type = rtrim($type,'[]');
		}
		
		if ($bIsArray) {
			$tmp = array();
			if (is_array($val)) {
				foreach($val as $k => $v) {
					$tmp[$k] = self::toJSON($v, $type);
				}
			}
			return $tmp;
		} else {
			if ($type == 'int') $val = intval($val);
			if ($type == 'string') $val = strval($val);
			if ($type == 'float') $val = floatval($val);
			if ($type == 'bool') $val = boolval($val);
			if ($type == 'array') return $val;
			if ($val instanceof JsonSerializable) {
				$val = $val->jsonSerialize();
			}
			if (is_object($val)) $val = null;
		}
		
		return $val;
	}
	
	//************************************************************************************
	public static function fromJSON($val, $type) {
		$type = trim($type);
		$bIsArray = false;
		
		if ($type && substr($type,-2) == '[]') {
			$bIsArray = true;
			$type = rtrim($type,'[]');
		}
		
		if ($bIsArray) {
			$tmp = array();
			if (is_array($val)) {
				foreach($val as $k => $v) {
					$tmp[$k] = self::fromJSON($v, $type);
				}
			}
			return $tmp;
		} else {
			if ($type == 'int') $val = intval($val);
			if ($type == 'string') $val = strval($val);
			if ($type == 'float') $val = floatval($val);
			if ($type == 'bool') $val = boolval($val);
			if ($type == 'array') return $val;
			if ($oClass = CodeBase::getClass($type, false)) {
				if ($oClass->isImplementing('JsonSerializable')) {
					if (!$oClass->hasStaticMethod('jsonUnserialize')) throw new SerializationException(sprintf('Class %s dont have static jsonUnserialize', $oClass->getName()));
					return $oClass->callStaticMethod('jsonUnserialize', array($val));
				} else {
					return null;
				}
			}
			return $val;
		}
		
		return null;
	}
	
}

?>