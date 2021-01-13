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


class UtilsEnumerable {
	
	private function __construct() { }
	
	
	//************************************************************************************
	public static function serializeRef($obj) {
		if (!($obj instanceof IEnumerable)) {
			return '';
		}
		
		$arr = array();
		$arr['__class'] = get_class($obj);
		
		if ($obj instanceof JsonSerializable) {
			foreach($obj->jsonSerialize() as $k => $v) {
				$arr[$k] = $v;
			}
		}
		
		return base64_encode(json_encode($arr));
	}
	
	//************************************************************************************
	/**
	 * @param string $str
	 * @return IEnumerable
	 */
	public static function unserializeRef($str) {
		$arr = @json_decode(base64_decode($str), true);
		if (is_array($arr)) {
			$oClass = CodeBase::getClass($arr['__class'], false);
			if ($oClass && $oClass->isImplementing('IEnumerable')) {
				if ($oClass->isImplementing('JsonSerializable')) {
					return $oClass->callStaticMethod('jsonUnserialize',array($arr));
				} else {
					return $oClass->getInstance();
				}
			}
		}
		return null;
	}
	
}

?>