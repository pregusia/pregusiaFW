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


class JWTPayload extends JWTClaimSet {
	
	const FIELD_ISSUER = 'iss';
	const FIELD_SUBJECT = 'sub';
	const FIELD_AUDIENCE = 'aud';
	const FIELD_EXPIRATION_TIME = 'exp';
	const FIELD_NOT_BEFORE = 'nbf';
	const FIELD_ISSUED_AT = 'iat';


	//************************************************************************************
	/**
	 * @return string
	 */
	public function getIssuer() { return $this->get(self::FIELD_ISSUER); }
	public function setIssuer($v) { $this->set(self::FIELD_ISSUER, $v); }

	//************************************************************************************
	/**
	 * @return string
	 */
	public function getSubject() { return $this->get(self::FIELD_SUBJECT); }
	public function setSubject($v) { $this->set(self::FIELD_SUBJECT, $v); }
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getAudience() { return $this->get(self::FIELD_AUDIENCE); }
	public function setAudience($v) { $this->set(self::FIELD_AUDIENCE, $v); }

	//************************************************************************************
	/**
	 * @return array
	 */
	public function getCustomClaims() {
		$res = array();
		foreach($this->values as $k => $v) {
			if ($k == self::FIELD_AUDIENCE) continue;
			if ($k == self::FIELD_EXPIRATION_TIME) continue;
			if ($k == self::FIELD_ISSUED_AT) continue;
			if ($k == self::FIELD_ISSUER) continue;
			if ($k == self::FIELD_NOT_BEFORE) continue;
			if ($k == self::FIELD_SUBJECT) continue;
						
			$res[$k] = $v;
		}
		return $res;
	}
	
	//************************************************************************************
	/**
	 * @param int $now
	 * @param int $maxAge
	 * @param int $leeway
	 * @return bool
	 */
	public function verifyTimestamp($now, $maxAge, $leeway) {
		if ($this->has(self::FIELD_EXPIRATION_TIME)) {
			if ($now > $this->get(self::FIELD_EXPIRATION_TIME) + $leeway) {
				return false;
			}
		}
		// TODO: iat
		// TODO: nbf
		return true;
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return JWTHeader
	 */
	public static function jsonUnserialize($arr) {
		if (!is_array($arr)) return null;
		
		$obj = new JWTPayload();
		$obj->values = $arr;
		return $obj;
	}	
	
	//************************************************************************************
	/**
	 * @param string $value
	 * @return JWTPayload
	 */
	public static function UnserializeTokenPart($value) {
		if (!$value) throw new JWTException('Empty payload');
		$value = UtilsString::base64URLDecodeJSON($value);
		if (!is_array($value)) throw new JWTException('Not array payload');
		
		$obj = new JWTPayload();
		$obj->values = $value;
		return $obj;
	}	
	
}

?>