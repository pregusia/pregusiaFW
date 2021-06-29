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


class JWTHeader extends JWTClaimSet {
	
	const FIELD_ALGORITHM = 'alg';
	const FIELD_KEY_ID = 'kid';
	const FIELD_TOKEN_TYPE = 'typ';
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getAlgorithm() {
		return $this->get(self::FIELD_ALGORITHM);
	}
	
	//************************************************************************************
	/**
	 * @return bool
	 */
	public function hasKeyID() {
		return $this->has(self::FIELD_KEY_ID);
	}
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getKeyID() {
		return $this->get(self::FIELD_KEY_ID);
	}
	
	//************************************************************************************
	/**
	 * @return JWTHeader
	 */
	public function getCopy() {
		$obj = new JWTHeader();
		$obj->values = $this->values;
		return $obj;
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return JWTHeader
	 */
	public static function jsonUnserialize($arr) {
		if (!is_array($arr)) return null;
		
		$obj = new JWTHeader();
		$obj->values = $arr;
		return $obj;
	}
	
	//************************************************************************************
	/**
	 * @param string $value
	 * @return JWTHeader
	 */
	public static function UnserializeTokenPart($value) {
		if (!$value) throw new JWTException('Empty header');
		$value = UtilsString::base64URLDecodeJSON($value);
		if (!is_array($value)) throw new JWTException('Not array header');
		
		$obj = new JWTHeader();
		$obj->values = $value;
		return $obj;
	}
	
	
}

?>