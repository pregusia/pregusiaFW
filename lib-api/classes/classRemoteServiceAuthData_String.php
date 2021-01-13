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


class RemoteServiceAuthData_String implements IRemoteServiceAuthData {
	
	private $value = '';
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getValue() { return $this->value; }
	
	//************************************************************************************
	public function __construct($value) {
		$this->value = trim($value);
	}
	
	//************************************************************************************
	/**
	 * @param string $pattern
	 * @return bool
	 */
	public function matches($pattern) {
		return preg_match($pattern, $this->value);
	}
	
	//************************************************************************************
	public function jsonSerialize() {
		$val = trim($this->value);
		if ($val) {
			return $val;
		} else {
			return null;
		}
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return RemoteServiceAuthData_String
	 */
	public static function jsonUnserialize($arr) {
		if (is_array($arr)) {
			if ($arr['type'] != 'RemoteServiceAuthData_String') return null;
			if (!$arr['value']) return null;
			return new RemoteServiceAuthData_String($arr['value']);
		}
		return null;
	}
	
}

?>